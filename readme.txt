=== Manga+Press Comic Manager ===
Contributors: Jess Green
Donate link: http://manga-press.jes.gs/
Tags: webcomics, online comics
Requires at least: 3.3
Tested up to: 3.3.1
Stable tag: trunk

Manga+Press is a webcomic management system for WordPress.

== Description ==

Manga+Press is a webcomic managment system for WordPress. Manga+Press uses WordPress's posts, pages and categories to help you keep track of your comic posts. Manga+Press also includes its own custom template tags to help make creating themes easier. Version 2.7 contains some new features listed here:

* New Thumbnail/Banner handling.

  1. 2.7 comes with Post Thumbnail support. This feature replaces the Comic Banner feature in previous versions of Manga+Press.

* Custom Taxonomies

  1. Manga+Press 2.7 introduces custom taxonomies (Series) for organizing comics.

* Posting New Comics:

  1. Comics are now a custom post-type.


== Changelog ==

= 2.7 =
* 2.7 Beta 2
  * Corrected issue with framework paths which prevented the Manga+Press Options forms from displaying properly.
  * Added closing PHP tags for servers that have short open tags disabled.

* 2.7 Beta
 * Eliminated "Insert Banner" and Comic Update codes. These features may return in future versions.
 * Added custom taxonomies, and post thumbnail support.
 * Eliminated TimThumb.

= 2.6 =
* 2.6.2
  * Introduced Spanish language support.

* 2.6.1
  * Corrected Static page issue. Also changed mpp_filter_latest_comicpage() so that Post title is included in output.

* 2.6
  * Fixed bugs that were present in 2.5. Manga+Press options page now located under Settings, Post New Comic page has been moved to Posts and Uninstall Manga+Press is located under Plugins.

* 2.6b
  * Changed handling of plugin options so that they are compatible with Wordpress 2.8 and higher. They are now stored in one entry in the options table instead of being spread out over multiple entries. Moved Manga+Press options page to Settings, Uninstall to Plugins, and Post New Comic to Posts. Removed /admin, /css, /js as they were no longer necessary for the plugin to function.

= 2.5 =
* 2.1/2.5
  * 2.1 renamed to 2.5. Eliminated the banner skin option and all functions attached. Feature can be duplicated with a little CSS positioning. Option for creating a banner from uploaded comic or uploading a seperate banner still remains, as well as the option to set banner width & height. Removed both the Manga+Press help and Template Tag pages. Will be hosted in a help wiki on the Manga+Press website. Made changes to the Post Comic page. Also reworded the "New Version" text. Created options to have the comic banner & navigation included at the top of The Loop on the home page, as well automatically filtering comic categories from the front page and automatically modifying The Loop for the latest comic page. Removed the make banner option.

* 2.0.1-beta
  * Corrected a minor bug in update_options. Banner skin wouldn't be uploaded even if "use banner skin" option were checked and user had selected an image for upload. Also corrected a jQuery UI Tabs bug in the user admin area that is present when Manga+Press is used with Wordpress 2.8

= 2.0 =
* 2.0-beta
  * Major reworking of code in mangapress-classes.php and mangapress-functions.php
    1. Reworked code of add_comic() function so it is compatible with the Wordpress post db and Media Library
    2. removed create directory for series option
    3. added wp_sidebar_comic()

= 1.0 =
* 1.0 RC2.5
  * Found a major bug involving directory/file permissions. Has been corrected, but I'm keeping my eye on this one for future reference. See website for a fix.

* 1.0 RC2
  * Modified add_comic(), add_footer_info()

* 1.0 RC1
  * General maintenance, fixing up look-and-feel of admin side. Putting together companion theme.

== Upgrade Notice ==
= 2.7 Beta 2 =
Fixes a problem with the Manga+Press Options page. A path issue in the framework may prevent option fields from displaying properly.

== Installation ==

1. Unpack the .zip file onto your hard drive.

2. Upload the `mangapress` folder to the `/wp-content/plugins/` directory.

3. Activate the plugin through the 'Plugins' menu in WordPress

4. Create two new pages; these pages will be your latest comic and comic archives pages. Label them something like 'Latest Comic' and 'Past Comics' or whatever, as long as it makes sense to you.

6. Click on the Manga+Press Options tab under Settings and go to Basic Manga+Press Options, and set Latest Comic Page, and Comic Archive Page to your two newly created pages.


== Frequently Asked Questions ==


== Screenshots ==

1. screenshot-1.jpg
2. screenshot-2.jpg
3. screenshot-3.jpg

== Credits ==

(c) 2008-2012 Jessica C. Green

Found a bug? Or did you find a bug and figure out a fix? Visit http://manga-press.jes.gs/ or email me at jgreen@psy-dreamer.com. Please include screenshots, WordPress version, a list of any other plugins you might have installed, or code (if you figured out a fix) and webserver configuration info; for example, Manga+Press was developed using a WAMP (Windows, Apache, MySQL, PHP) environment but works fine on the server my sites are hosted at, which is a LAMP environment. Be as detailed as possible.

For updates, you can visit http://manga-press.jes.gs/

Uses icons from the Fugue icon set found at http://p.yusukekamiyamane.com/

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA