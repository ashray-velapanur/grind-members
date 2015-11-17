=== Pages by User Role for WordPress ===
Author: Alberto Lau (RightHere LLC)
Author URL: http://plugins.righthere.com/pages-by-user-role/
Tags: Access Control, User Roles, Hide Pages, Menu, Custom Post Types, Categories, WordPress
Requires at least: 3.0
Tested up to: 3.1.2
Stable tag: 1.1.5 rev7652



======== CHANGELOG ========
Version 1.1.5 rev7652 - August 8, 2011
* New Feature: Built-in Shortcode pur_restricted to restrict access to certain sections of the content by capability; defaults to view_restricted-content but any capability
* Updated Options Panel updated
* New Feature: No access behavior customization. Admin can specify if a restricted page should redirect to login or to redirect URL.
* New Feature: Custom Post Types by User Role. This only shows if there are custom post types.
This is a mini-plugin itself that adds the following functionality:
In the tab option it shows a list of custom post types, and checkboxes of all the existing user roles for each custom post type.
By checking a user role for a custom post type you are restricting admin access to that post type only to the checked user role.

VERY IMPORTANT: Always check the Administrator. Note we do not do it by default, thus maybe the Administrator changed the the administrator user role, so we don't really know what the administrator role is.

In the case of incorrectly setting the administrator user role, there is an option on the same tab to disable this feature an recover access to the custom post types.

Version 1.1.4 rev4375 - May 7, 2011
* Bug Fixed: After setting user roles in Category and removing all, all roles where denied access afterwards.

Version 1.1.3 rev 2526 - March 24, 2011
* New feature, added comment filtering to comments fetch with the wp method get_comments (recent comments widget)

Version 1.1.2 rev 1863 - March 1, 2011
* Changed the procedure for redirect;
     1) the custom url
     2) the default url
     3) the login page
     4) if you are logged in and do not have access, an error message is shown.

Version 1.1.1 - February 8, 2011
* Fixed broken default redirect URL
* Fixed Post and Post redirect URL

Version 1.1.0 - February 3, 2011
* Added support for non standard WordPress table pre-fix
* Added support for access control to Categories
* Categories with access control are not searchable (unless you have access)
* Restrict access to Post by using the Posts ID.
* Category will not show in the menu if restricted access

Version 1.0.0 - November 3, 2010
* First release.


======== DESCRIPTION ========

This plugin lets you restrict access to a Page, Post, Custom Post Type or Category depending on which Role the user has. It removes the Page, Post, Custom Post Type or Categories from search results and blog roll. You can hide Page and Categories from the menu when the user is not logged in.

You can also set a specific redirect URL for users that don’t have the required User Role.

== INSTALLATION ==

1. Upload the 'pages-by-user-role' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Click on 'Pages by User Role' in the left admin bar of your dashboard

== Frequently Asked Questions ==

Q: Can I hide a Page from the menu when a user is not logged in?
A: Yes, if you choose to restrict access to a Page, Post or Custom Post Type then the page will NOT show in the menu.

Q: What happens if I don't set a redirect URL and a user try to access a Page or Post they he or she doesn't have access to?
A: The user will get redirect to the default page saying "You don't have access to this page, contact the website administrator."

Q: Can I create a custom page that users will be redirect to if they don't have access?
A: Yes, you can create a custom page and then enter the URL either as the default redirect page. You can actually redirect user to an individual Page for every Post or Post you create.

Q: Can I provide access to more than one User Role to the same Page or Post?
A: Yes, simply by selecting more than one User Role in the "Page Access" box.


