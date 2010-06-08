=== Hikari Enhanced Comments ===
Contributors: shidouhikari 
Donate link: http://Hikari.ws/wordpress/#donate
Tags: comment, comments, widget, title, titled, enhanced, recent comments, most commented, ip2nation, country, IP, avatar, gravatar, pingback, MySQL, database
Requires at least: 2.8.0
Tested up to: 2.9.2
Stable tag: 0.03.05

Comments are enhanced with new features that make them more visible and becoming more exciting in website structure.

== Description ==

**Hikari Enhanced Comments** enhances comments with features that make comments more visible and and becoming more exciting in website structure.

Things that you've been wanted to do, now can be done much easier.


= Features =

* An **Enhanced Recent Comments** *widget*, based on Wordpress core widget, but redesigned to make it possible to show at least 60 last comments.
* ERC widget allows to exclude users from having their comments shown, perfect for website owners and authors that really participate on their site's comment debates ;)
* If you also have <a href="http://Hikari.ws/titled-comments/">Hikari Titled Comments</a> plugin installed, comments with titles have their titles listed
* Comments authors have their gravatar shown in the ERC widget
* For pingbacks, their gravatar is replaced by a "P" icon
* A **Most Commented Posts** *widget*, that lists your posts with higher number of comments
* If you have ip2nation installed (see installation instructions), comment authors are also shown with a flag of their country, in Enhanced Recent Comments widget and in comments area
* Country flags can be added anywhere in your site, you just need to tweak your theme and use your imagination



== Installation ==

**Hikari Enhanced Comments** requires at least *Wordpress 2.8* and *PHP5* to work.

You can use the built in installer and upgrader, or you can install the plugin manually.

1. Download the zip file, upload it to your server and extract all its content to your <code>/wp-content/plugins</code> folder. Make sure the plugin has its own folder (for exemple  <code>/wp-content/plugins/hikari-enhanced-comments/</code>).
2. Activate the plugin through the 'Plugins' menu in WordPress admin page.
3. Now you can go to widgets admin page and you'll find 2 new widgets available: "Hikari Enhanced Recent Comments" and "Hikari Most Commented Posts"


= Installing and configuring ip2nation =

<a href="http://ip2nation.com/">ip2nation</a> is a website that provides a MySQL database table, listing all world's IPs and relating them to their respective country, together with each country name and code.

This table is provided in a downloadable .sql file. I don't add this table together with the plugin because it's updated from time to time, so it's better to just get it from the source and install it.

You are not required to have ip2nation installed to use **Hikari Enhanced Comments**. The plugin uses it just to get country flags. If ip2nation is not available, or if you are not able to install it, all other features from the plugin will still work, only flags won't be shown (the place where they'd go will be blank, don't worry with broken <img> code :)



First of course you must download the file. You can go to their <a href="http://ip2nation.com/">home page</a> and follow the 'Download' menu, or just download it directly: <a href="http://ip2nation.com/ip2nation.zip">http://ip2nation.com/ip2nation.zip</a>.

Once you have the file, you must upload it to your database. But here's a cool feature: since its tables so big (somewhat 3MB the uncompressed .sql file, and 1MB after importing to MySQL) and rarely updated, I provided a way to add it to a different database, **separated** from your Wordpress one. Doing so, you're not forced to back it up together with your website content (after all, it's not a relevant data worthy backing up, if you lose it you can just download it back from their site), and also if you have multiple sites you can have it stored only once.

If you wanna have ip2nation in the same database as your Wordpress install, just use it to import the .sql file. If you wanna use a different database, I suggest creating a new one only for it. The trick is to assign over this new database, full access from your Wordpress database's user (you can find its name in Wordpress <code>config.php</code>. If the same user can access more than 1 database, it's easy for MySQL to provide simultaneous access to all of them. And if your Wordpress database user only has access to these 2 databases, and ip2nation table only has this data, you shouldn't have security issues. To know how to assing user permissions to databases, please call your webhost provider. Don't install each table in separated database, ip2nation tables can be separated from Wordpress but both tables must be in the same database!

As I've said, ip2nation database is big, I had trouble uploading it with phpMySQL. I just split the file in 3 and imported them separately, it's just 2 CREATE TABLE in the begining and then a bunch of endless INSERT INTO. If you have timeout trouble, try splitting it in more files, or again request your webhost assistance.

Once you have your ip2nation database created, configured and populated with its data, and database user has privilege to it, go take a look if data was imported correctly. It creates 2 tables, 'ip2nation' with more than 40.000 records, and 'ip2nationcountries' with somewhat 250 records.

Now, to finish ip2nation installation, you must go to **Hikari Enhanced Comments** admin page, and under '*ip2nation Database*' option, set ip2nation database name. If you just installed it in Wordpress database, leave this option blank. If it has its own database, the full database name must be added here. Pay attention because many shared hostings only let you define part of databases names and hide the part you can't change, make sure you're using the full name here.

To help you diagnose ip2nation and see if flags and countries are working, in this same page there is a section just below options where you can see it working. This section uses a testing IP from Brasil to query ip2nation tables, and if it was configured correctly you'll see its country code as '**br**' and country name as '**Brazil**', and just below you'll see Brasil's flag.

If you see all these info, you are good to go, gratz! If you see Brasil name and code but doesn't see its flag, assure you have properly extracted the plugin. If it was installed in <code>/wp-content/plugins/hikari-enhanced-comments/</code>, you should have a folder <code>/wp-content/plugins/hikari-enhanced-comments/flags/</code> and inside it a bunch of files with those flags.

And if you don't even see Brasil's name and code in diagnose, then ip2nation is not properly configured. Verify its database again, and then assure Wordpress database user can access it. If you have any trouble setting up your tables, or flags are in their place but are not accessible, please again go to your hosting provider ask for assistance.


= Adding flags to your theme =

You don't need to tweak your theme to have flags shown in it, **Hikari Enhanced Comments** adds them automatically to your comments authors. In the plugin admin page you can choose if flag should be added before or after author's name, where it better fits your theme layout.

But you can also add it manually, if you want a more customized layout. For that, just use the function <code>HkEC_flag($ip)</code>, where <code>$ip</code> is the IP whose flag you want. Flag image will be echoed automatically.

If that IP is from a comment author, you just use <code>kEC_flag($comment->comment_author_IP)</code> and you are done.


= Upgrading =

If you have to upgrade manually, simply delete <code>hikari-enhanced-comments</code> folder and follow installation steps again.

You don't need to touch ip2nation tables once they are installed, but since they are updated from time to time, you may want to update them to have info about all IPs. In this case, just import the lastest .sql file to your database.

= Uninstalling =

If you go to plugins list page and deactivate the plugin, it's config stored in database will remain stored and won't be deleted.

If you want to fully uninstall the plugin and clean up database, go to its options page and use its uninstall feature. This feature deletes all stored options, restoring them to default (which can also be used if you want defaults restored back), and then gives you direct link to securely deactive the plugin so that no database data remains stored.

Plugin uninstall feature **doesn't** delete ip2nation tables, regarding if they are in same database as Wordpress or in a separated one. If they are not used anymore, currently you must delete them manually if you don't need them anymore.

Also, make sure to delete plugin's widgets before uninstalling the plugin. ATM it's not deleted upon uninstalling, I'll fix it in a future version :(



== Frequently Asked Questions ==

= Is "Hikari Enhanced Recent Comments" widget related to Wordpress core "Recent Comments"? =

Well, in parts. PHP code is totally independant, but based on it. But it uses compatible CSS classes, so it shoud fit to theme layouts.

= Can I use both core "Recent Comments" and "Hikari Enhanced Recent Comments" widgets together?  =

Sure! You can use them together and as many times as you want!

= Is your titled comments plugin required to use the widget? =

No, my idea isn't to force somebody to use them together.

Indeed, **Hikari Enhanced Comments** was first idealized to be part of **Hikari Titled Comments**, being implemented on a future version. But when I started coding it, I saw that core "Recent Comments" couldn't be simply extended (I did that :P ), and using filter wasn't enough for me. Code was becoming too big and getting out of "titled comments" scope, and also there was ip2nation to be installed separated, so I decided to port these features to a separated plugin and make both compatible.

If both are available they work together, but they work alone pretty fine too.

= Your titled comments plugin requires WP 2.9, and this enhanced comments requires 2.8, strange!  =

All my plugins require PHP5 because I use OOP, and WP 2.8 because I use <code>settings_fields()</code>.

**Hikari Titled Comments** requires WP 2.9 only because it uses comments metadata. Even though **Hikari Enhanced Comments** supports **Hikari Titled Comments**, it only uses its functions, it doesn't go over comments metadata directly, and since **Hikari Enhanced Comments** doesn't use metadata code, it works with WP 2.8. Remember these plugins work together but they don't require each other to do their own job :)

= I've looked on HTML code your widgets generate, and noted they use ordered lists, but ERC shows no list marking and MCP shows a disc mark instead of number marks. Why did you do that? =

I like to use correct semantic markup and nice styles on the same time. Both widgets list some content sorted by date or amount, so they are ordered lists, and not unordered. But, Recent comments, even more when titled comments are present, don't fit well with list marks, while Most Commented Posts wouldn't be nice being listed with numbers. Well at least in my theme.

These styles are added directly to the HTML document, in 'wp_head' action, that's the same behavior of Wordpress Core's Recent Comments widget. If you wanna change it, just add your own style in your theme's style.css and override them. :)

= You your ERC widget allows to exclude users from being listed, but MCP doesn't? =

It's all database related. For Recent Comments, it's just a matter of defining comment's autor to be excluded, and MySQL does the trick easily because each comment has its author name on it.

For Commented Posts, on the other hand, each post has a field informing how many comments it has, and this field is easy to use. If I'd make specific commenters be excluded, I'd have to ignore this field, make a join between wp_posts and wp_comments based on post ID, and then exclude each comment whose author name matches those defined to not be counted. This SQL query would be much heavier and I believe it's not worthy. We could use cache to store the query and avoid it being done on every page load, but that would require another plugin to handle cache persistence. Explaining all this (or even worse, explain somebody why his site's page load became a bit slower upon using the widget) wouldn't be nice. Maybe someday I implement the feature, or if somebody asks me for it.

= What happens if I don't have ip2nation tables installed and working? =

What the plugin does with that is query it for a country related to a IP. If tables are not accessible for *any* reason, the query will return with error and it'll be ignored.

Without the country code, the flag URL can't be created, generating an error. And even if the code is found, if for some reason the flag can't be accesses, same error will happen.

This error is verified, and if flag is available, its <img> tag is generated and provided. But if flag is unavailable, it will just return a harmless blank string. In HTML document there will be no track of it, and user will just see nothing where the flag would be placed.

Of course, if you are adding the flag or relying on country code or name in your theme, you must be prepared for not receiving them, due to some error or simply the plugin not being active.



== Screenshots ==

1. Wordpress core, default Recent Comments widget
2. **Hikari Enhanced Recent Comments** widget, showing all comments
3. **Hikari Enhanced Recent Comments** widget, excluding my comments!
4. When **Hikari Titled Comments** plugin is used, comments' titles are shown!
5. **Hikari Enhanced Recent Comments** widget config interface
6. A comment author
7. A comment author with his country flag :)
8. Here's ip2nation Diagnose when everything's working
6. **Hikari Most Commented Posts** widget

== Changelog ==

= 0.03 =
* **New widget**: Most Commented Posts
* Added country name to flags title attribute (popup)
* Recent Comments widget: fixed minor widget saving code
* Small code cleanups

= 0.02 =
* First public release.

== Upgrade Notice ==

= 0.02 and above =
If you have to upgrade manually, simply delete <code>hikari-enhanced-comments</code> folder and follow installation steps again.

You don't need to touch ip2nation tables once they are installed, but since they are updated from time to time, you may want to update them to have info about all IPs. In this case, just import the lastest .sql file to your database.
