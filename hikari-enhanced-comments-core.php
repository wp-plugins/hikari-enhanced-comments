<?php


global $hkEC;
$hkEC = new HkEC();


function HkEC_flag($ip=null){
	global $hkEC;
	echo $hkEC->getFlag($ip);
}

function HkEC_get_gravatar($gravatar_size='16'){
	global $hkEC;
	return $hkEC->get_gravatar($gravatar_size);
}




class HkEC extends HkEC_HkTools{

	private $op;
	private $database=null;
	public $insideWidget=false;
	
	
	public function __construct(){
		parent::__construct();
	
		global $hkEC_Op;
		$this->op = $hkEC_Op->optionsDBValue;
		
		if(!empty($this->op['database'])) $this->database = $this->op['database'].'.';
		
		$this->flag_folder_path = apply_filters(
				'HkEC_flag_folder_path',$this->plugin_dir_path.'flags/');
		$this->flag_folder_url = apply_filters(
				'HkEC_flag_folder_url',$this->plugin_dir_url.'flags/');
	
	
		$this->setFilters();
	}
	
	
	public function setFilters(){
	
		add_filter('get_comment_author',array($this,'authorFilter'));
	
	}
	
	
	
	public function getCountryInfo($ip){
		global $wpdb;
		
		$sql = "SELECT c.country, c.code
		  FROM ".$this->database."ip2nationCountries c, ".$this->database."ip2nation i
		  WHERE i.ip < INET_ATON('".$ip."')
		  AND c.code = i.country
		  ORDER BY i.ip DESC
		  LIMIT 0,1";
		$row = $wpdb->get_row("$sql");
		
		// name: $row->country
		// code: $row->code
		
		return $row;
	}
	
	public function getCountryName($ip=null){
		if(empty($ip)) $ip = $_SERVER['REMOTE_ADDR'];
		
		$result = $this->getCountryInfo($ip);
		return $result->country;
	}

	public function getCountryCode($ip=null){
		if(empty($ip)) $ip = $_SERVER['REMOTE_ADDR'];
		
		$result = $this->getCountryInfo($ip);
		return $result->code;
	}



	public function getFlag($ip=null){
		
		$code = $this->getCountryCode($ip);
		if($this->isBlank($code)) return;
		
		$code = strtoupper($code);
		
		$name = $this->getCountryName($ip);
		
		
		$img = apply_filters('HkEC_flag_img',$code.'.gif',$code);
		
		$file_found = apply_filters('HkEC_flag_file_found',file_exists($this->flag_folder_path.$img));
		
		if(!$file_found) return;
		
		$flag = '<img class="comment-author-flag" alt="flag" title="'.
					$name." ".$code.' flag" src="'.$this->flag_folder_url.$img.'" />';
		
		return apply_filters('HkEC_getFlag',$flag);

	}


	public function authorFilter($author){
		if($this->insideWidget) return $author;
		
		global $comment;
		$ip = $comment->comment_author_IP;
	
		switch($this->op['author_filter']){
			case 'before':
				return $this->getFlag($ip).' '.$author;
				break;
			case 'after':
				return $author.' '.$this->getFlag($ip);
				break;
			case 'none':
			default:
				return $author;
		}
	}
//


	// requires global $comment to work
	public function get_gravatar($gravatar_size='16'){
		global $comment;
		
		if($comment->comment_type == 'comment' || empty($comment->comment_type)){
			$avatar = '<span class="avatar_cont gravar_comment"> ' .
					get_avatar( get_comment_author_email(), $gravatar_size, '', 'gravatar' ) .
					'</span>';
		}else{
			$avatar = '<span class="avatar_cont gravar_ping"> <img width="'.$gravatar_size.
				'" height="'.$gravatar_size.
				'" src="'.$this->flag_folder_url.
				'pingback.png" alt="P" title="Pingback" class="avatar avatar-'.
				$gravatar_size.' photo" /></span>';
		}
		
		return $avatar;
	}










}


add_action('widgets_init', "HkEC_widgets_registration");

function HkEC_widgets_registration(){
	register_widget("HkEC_Widget_Recent_Comments");
	register_widget("HkEC_Widget_Most_Commented_Posts");
}

class HkEC_Widget_Recent_Comments extends WP_Widget {

	function HkEC_Widget_Recent_Comments() {
		$widget_ops = array(
				'classname' => 'widget_recent_comments widget_hikari_enhanced_recent_comments',
				'description' => 'The most recent comments, built by Hikari Enhanced Comments plugin'
			);
		$this->WP_Widget('hkec-recent-comments', 'Hikari Enhanced Recent Comments', $widget_ops);
		
		$this->alt_option_name = 'hkec_widget_recent_comments';

		if( is_active_widget(false, false, $this->id_base) )
			add_action( 'wp_head', array(&$this, 'recent_comments_style') );



		// when a new comment is submited
		add_action( 'comment_post', array(&$this, 'flush_widget_cache') );
		
		// when comment status changes, ex: pending comment is approved
		add_action( 'transition_comment_status', array(&$this, 'flush_widget_cache') );
		
	}
	

	function widget( $args, $instance ) {
		global $wpdb, $comments, $comment, $hkEC;
		
		$hkEC->insideWidget=true;

		extract($args, EXTR_SKIP);
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Comments') : $instance['title']);
		
		if ( !$number = (int) $instance['number'] )
			$number = 5;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 60 )
			$number = 60;
			
		$excludes = explode(",",$instance['exclude']);

		if ( !$comments = wp_cache_get( 'recent_comments', 'widget' ) ) {
			$sql = "SELECT $wpdb->comments.*
FROM $wpdb->comments JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->comments.comment_post_ID
WHERE comment_approved = '1' AND post_status = 'publish'";

			foreach($excludes as $exclude){
				$sql .= "\nAND comment_author != '".trim($exclude)."'";
			}

			$sql .= "\nORDER BY comment_date_gmt DESC
LIMIT 60";
		
			$comments = $wpdb->get_results($sql);
			wp_cache_add( 'recent_comments', $comments, 'widget' );
		}

		$comments = array_slice( (array) $comments, 0, $number );

		
		
			echo $before_widget;
			if ( $title ) echo $before_title . $title . $after_title; ?>

<?php // please don't remove copyright ?>
<!-- Enhanced Recent Comments provided by
	Hikari Enhanced Comments - http://Hikari.ws -->

<?php
			if( $comments ){
?>
<ol class="hkec-recentcomments-list"><?php
				foreach( (array) $comments as $comment){
					$GLOBALS['comment'] = $comment;
				
$comment_item = "\n\t".'<li class="hkec-recentcomments-item">';

if(function_exists('hkTC_get_comment_title')){
	$cTitle = hkTC_get_comment_title($comment->comment_ID);
	
	if(!empty($cTitle))
		$comment_item .= '<strong><a href="' .
			esc_url( get_comment_link($comment->comment_ID) ) .
			'" title="' . $cTitle . " on: " .get_the_title($comment->comment_post_ID) .
			'">' . $cTitle . '</a></strong><br />';
}

$comment_item .= sprintf(_x('%1$s on %2$s', 'widgets'), /* translators: comments widget: 1: comment author, 2: post link /**/
		
		'<span class="avatar_cont">' . $hkEC->get_gravatar() . '</span> ' .
		get_comment_author_link() . ' ' . $hkEC->getFlag($comment->comment_author_IP),
		
		'<a href="' . esc_url( get_comment_link($comment->comment_ID) ) .
			'" title="'.get_comment_author().' commented on: '	.
				get_the_title($comment->comment_post_ID).'">' . 
			get_the_title($comment->comment_post_ID) . '</a>');

			
$comment_item .= '</li>';

					echo apply_filters('HkEC_widget_comment_item',$comment_item,$comment);
				
				}

?>
</ol>

<?php
			}
			
			echo $after_widget;
		
			$hkEC->insideWidget=false;

	}
	
	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$number = isset($instance['number']) ? absint($instance['number']) : 5;
		$exclude = isset($instance['exclude']) ? esc_attr($instance['exclude']) : '';
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of comments to show:'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /><br />
		<small>(at most 60)</small></p>
		
		<p><label for="<?php echo $this->get_field_id('exclude'); ?>">Exclude users:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('exclude'); ?>" name="<?php echo $this->get_field_name('exclude'); ?>" type="text" value="<?php echo $exclude; ?>" />
		<small>Comments authors you want to exclude from listing. Separate names with comma.</small></p>
<?php
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['exclude'] = strip_tags($new_instance['exclude']);
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['hkec_widget_recent_comments']) )
			delete_option('hkec_widget_recent_comments');

		return $instance;
	}
	
	
	
	
	function recent_comments_style() { ?>
<style type="text/css">
	.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}
	.hkec-recentcomments-item{list-style-type: none;}
	.hkec-recentcomments-item strong a{font-weight: bold}
</style>
<?php
	}
	
	function flush_widget_cache() {
		wp_cache_delete('recent_comments', 'widget');
	}



}
/**/



class HkEC_Widget_Most_Commented_Posts extends WP_Widget {

	function HkEC_Widget_Most_Commented_Posts() {
		$widget_ops = array(
				'classname' => 'widget_hikari_most_commented_posts',
				'description' => 'List of posts with higher number of comments, built by Hikari Enhanced Comments plugin'
			);
		$this->WP_Widget('hkec-most-commented-posts', 'Hikari Most Commented Posts', $widget_ops);
		
		$this->alt_option_name = 'hkec_widget_most_commented_posts';

		if( is_active_widget(false, false, $this->id_base) )
			add_action( 'wp_head', array(&$this, 'commented_posts_style') );


		// when a new comment is submited
		add_action( 'comment_post', array(&$this, 'flush_widget_cache') );
		
		// when comment status changes, ex: pending comment is approved
		add_action( 'transition_comment_status', array(&$this, 'flush_widget_cache') );
		
	}
	

	function widget( $args, $instance ) {
		global $wpdb, $comments, $comment, $hkEC;
		
		$hkEC->insideWidget=true;

		extract($args, EXTR_SKIP);
		$title = apply_filters('widget_title', empty($instance['title']) ? "Most Commented Posts" : $instance['title']);
		
		if ( !$number = (int) $instance['number'] )
			$number = 5;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 60 )
			$number = 60;
			
		$excludes = explode(",",$instance['exclude']);

		if ( !$posts = wp_cache_get( 'hikari_most_commented_posts', 'widget' ) ) {
			$sql = "SELECT ID
FROM $wpdb->posts
WHERE post_status = 'publish' AND comment_count > 0 ORDER BY comment_count DESC
LIMIT 60";

			$posts = $wpdb->get_results($sql);
			wp_cache_add( 'hikari_most_commented_posts', $posts, 'widget' );
		}

		$posts = array_slice( (array) $posts, 0, $number );

		
		
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title; ?>

<?php // please don't remove copyright ?>
<!-- Most Commented Posts provided by
	Hikari Enhanced Comments - http://Hikari.ws -->
<?php
		if(!empty($posts)){
?>
<ol class="hkec-commentedposts-list"><?php
			foreach ( (array) $posts as $post_id){
				global $post;
				$post = get_post($post_id->ID);
				$comments_count = get_comments_number($post_id->ID);
				
$post_item = "\n\t".'<li class="hkec-commentedposts-item">';

	$post_item .= '<a href="' . get_permalink() .'" rel="bookmark" title="' .
			the_title_attribute(array('echo' => false)) . '">'. get_the_title() .'</a> (' .
			$comments_count .')';

$post_item .= '</li>';

				echo apply_filters('HkEC_widget_commentedposts_item',$post_item,$post);
				
			}
?>
			
</ol>
			
<?php
		}	// if(!empty($posts))
		
		echo $after_widget;
		
		$hkEC->insideWidget=false;

	}
	
	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$number = isset($instance['number']) ? absint($instance['number']) : 5;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>">Number of posts to show:</label><br />
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /><br />
		<small>(at most 60)</small></p>

<?php
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['hkec_widget_most_commented_posts']) )
			delete_option('hkec_widget_most_commented_posts');

		return $instance;
	}
	
	
	

	function commented_posts_style() { ?>
<style type="text/css">
	.hkec-commentedposts-item{list-style-type: disc;}
</style>
<?php
	}

	function flush_widget_cache() {
		wp_cache_delete('hikari_most_commented_posts', 'widget');
	}



}
/**/