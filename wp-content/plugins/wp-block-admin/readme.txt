=== WP Block Admin ===
Contributors: chmac
Donate link: http://www.callum-macdonald.com/code/donate/
Tags: wp-admin, block admin, admin interface, admin
Requires at least: 2.0
Tested up to: 2.9.2
Stable tag: 0.2.2

Block access to the admin interface based on user capability. The default is only editors and admins are allowed access, but the required capability can be set by editing the plugin file.

== Description ==

Block access to the admin interface based on user capability. The default is only editors and admins are allowed access, but the required capability can be set by editing the plugin file.

You can change the configuration of the plugin by modifying the file itself. The two config variables are on lines 18 and 21.

== Installation ==

1. Download
2. Upload to your `/wp-contents/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Can I change the redirection url? =

Yes, you need to edit the plugin file and change the variable on line 21.

= Can I change the required capability? =

Yes. For more information see the codex for [http://codex.wordpress.org/Roles_and_Capabilities#Capabilities Roles and Capabilities]. Once you've chosen a capability, simply edit the plugin file at line 18 and replace "edit_others_posts" with your chosen capability.

= I have another question... =

If your question is not answered here, you can try contacting me. However, I don't actively support this plugin, so I'm not likely to make changes to meet your requirements, etc.
<http://www.callum-macdonald.com/contact/>

== Changelog ==

= 0.2.2 =
* Fixes a bug introduced in 0.2.1 using back ticks instead of quotes.
* Excludes admin-ajax.php as well as async-upload.php.

= 0.2.1 =
* Bugfix contributed by Sam hermans http://www.greenfudge.org/

= 0.2 =
* Added option to set config vars as constants in wp-config.php

= 0.1.3 =
*  Bugfix contributed by Jonah Korbes http://neveradudelikethisone.com/

== Upgrade Notice ==

= 0.2.2 =
Fixes bug introduced in 0.2.1. Apologies for any inconvenience.

= 0.2.1 =
Fixes an issue with uploading files.

== Support Questions ==

If your question is not answered here, you can try contacting me. However, I don't actively support this plugin, so I'm not likely to make changes to meet your requirements, etc.
<http://www.callum-macdonald.com/contact/>
