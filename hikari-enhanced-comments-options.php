<?php

require_once 'hikari-tools.php';



global $hkEC_Op;
$hkEC_Op = new HkEC_Op();



class HkEC_Op extends HkEC_HkToolsOptions{

	public $optionsName = 'hkEC';
	protected $pluginfile = HkEC_pluginfile;
	protected $optionsDBVersion = 1;
	
	public $opStructure = array(
		"database" => array( "name" => "ip2nation Database",
				"desc" => "ip2nation can be stored in a different database from your Wordpress one",
				"largeDesc" => "<p class=\"description\">If ip2nation tables are in another database, provide the full database name. If they are on the same datababase leave this option blank. Both <em>ip2nation</em> and <em>ip2nationcountries</em> <strong>must</strong> be in the same database.</p>",
				"id" => "database",
				"default" => '',
				"type" => "text",
				"options" => array("size" => "65", "full_width" => false)
		),
		
		"author_filter" => array( "name" => "Auto flag comment author",
				"desc" => "Set where you want country flags to be automatically inserted in comment authors names.",
				"largeDesc" => "<p class=\"description\">If you don't wanna touch your theme, just set it to be inserted automatically. Or you can set it to not be automatically and add the function <code>HkEC_flag($ comment->comment_author_IP)</code> to your <code>comments.php</code> whre you want the flags to appear.</p>",
				"id" => 'author_filter',
				"default" => 'after',
				"type" => "radio",
				"options" => array(
						'none'	=> "<strong>Don't</strong> add flag automatically",
						'before'=> "Add flag <strong>before</strong> author's name",
						'after'	=> "Add flag <strong>after</strong> author's name"
				)
		)
	
	
	
			);



	public function __construct(){
		parent::__construct();
	
		$this->uninstallArgs = array(
				'name' => $this->optionspageName,
				'plugin_basename' => HkEC_basename,
				'options' => array(
						array(
							'opType' => 'wp_options',
							'itemNames' => array($this->optionsDBName)
						)
					)
			);
			
		
	}
	


	public function options_page_middle(){
		$ip = '201.9.158.252';

?>
<div class="postbox hikari-postbox"><div class="inside">
	<h2>ip2nation Diagnose</h2>
	<p>ip2nation is a table that relates every public IPs in the world with the country assigned to it. <?php echo $this->optionspageName; ?> only uses this table, it's not provided with the plugin, for a tutorial on how to install the table, please refere to plugin's installation instructions.</p>
	<p>This section is meant to test and digagnose if the table is available and properly working. Based on the following data you are able to know what's going on and receive a few suggestions to fix it.</p>
	<p>&nbsp;</p>
	
	<h4>IP check</h4>
	<p>Your current IP is '<strong><?php echo $_SERVER['REMOTE_ADDR']; ?></strong>'.
	<p>This info is gathered from <code>$_SERVER['REMOTE_ADDR']</code>, if for some reason your server's PHP can't get your IP, probably it won't be able to with your visitors. To make sure next steps will work, I'm gonna use for test the IP <em><?php echo $ip; ?></em>, it's assined for Brasil.</p>
	<p>&nbsp;</p>
	
	<h4>IP to nation</h4>
<?php
global $hkEC;
?>
	<p>If ip2nation table is properly installed, <?php echo $ip; ?>'s country code should be <em>br</em>, the gathered country code is '<strong><?php echo $hkEC->getCountryCode($ip); ?></strong>'.</p>
	<p>And if it's working, the IP's country name should be <em>Brazil</em>, the gathered country name is '<strong><?php echo $hkEC->getCountryName($ip); ?></strong>'.</p>
	<p>&nbsp;</p>
	<p>If you didn't get the correct country data, don't worry, <strong><?php echo $this->optionspageName; ?></strong> will still work. It's widget and the widget features will work fine, only thing that won't work is country flags, that won't be present. But, if you got any PHP error in this test, please contact me with this error so I can take a look.</p>
	<p>&nbsp;</p>
	
	<h4>Flags</h4>
	<p>If you already have ip2nation tables installed and passed previous test, then let's try to add a proper flag now.</p>
	<p>Here you should get Brasil's flag: '<?php HkEC_flag($ip); ?>'</p>
	<p>&nbsp;</p>
	<p>If you didn't pass prvious test, this one for sure won't work, because it uses countries code to get the flag. But if previous one passed and now you don't see a flag, then the plugin is not being able to find flags files.</p>
	<p>Verify if it's properly installed, if 'flags' folder is inside the plugin main folder, and if there are a lot of .gif files inside it (in special case, BR.gif, which is used in this test). If those files are not there, try deleting the whole plugin folder and reinstalling it.</p>
	<p>If after reinstalling the plugin, enabling ip2nation tables and passing previous test, you are still unable to see flags, just submit a comment in the plugin's page with as many details as possible and I'm gonna try to help :)</p>
	
	
</div></div>

<p>&nbsp;</p>
<p>&nbsp;</p>

<?php
	}










}