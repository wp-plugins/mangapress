=== Manga+Press Comic Manager ===
Contributors: Jessica Green
Donate link: http://manga-press.silent-shadow.net/
Tags: webcomics, online comics
Requires at least: 2.8
Tested up to: 2.9.1
Stable tag: 2.6.3

Manga+Press is a webcomic managment system for Wordpress.

== Description ==

Manga+Press is a webcomic managment system for Wordpress. Manga+Press uses Wordpress's posts, pages and categories to help you keep track of your comic posts. Manga+Press also includes its own custom template tags to help make creating themes easier.

= Update Notice =

Please update to 2.6.3 immediately! 2.6.2 and older uses an older version of TimThumb, which may not be secure. 

= New in 2.6.2 =

* Improved multi-language support. Spanish currently supported. POT file included for other translators to use.

= Current Features: =

* Automatic Options:

1. You now have the option to automatically exclude the comic category from the front page.

2. You can now automatically have the comic navigation code inserted into the comic posts and comic page.

3. Comic banner can now be automatically inserted into the front page.

4. All of this can be done by enabling certain options in Manga+Press. No editing of Wordpress themes are necessary!


* Comic Update Codes:

1. New options to insert both TheWebcomicList.com "Last Update: " and OnlineComics.net "PageScan"-codes.

2. Banner images are no longer generated. The front page banner now uses TimThumb to generate a cached image from the comic according to dimensions specified in Image Options.

* Posting New Comics:

1. For greater control, you now have the option of using either Wordpress's Add New page or the Manga+Press Post Comic page. When using the Add New page, Manga+Press checks which categories the post is being assigned to and then automatically adds the post to the comic database and the required post meta if the assigned categories match the comic category specified in Comic Options.

2. Changes have been made to the Post Comic page. Categories are now listed as checkboxes instead of a dropdown, and an optional excerpt can be added.

== Changelog ==

= 2.6 =
* 2.6b	Changed handling of plugin options so that they are compatible with Wordpress 2.8 and higher. They are now stored in one entry in the options table instead of being spread out over multiple entries. Moved Manga+Press options page to Settings, Uninstall to Plugins, and Post New Comic to Posts. Removed /admin, /css, /js as they were no longer necessary for the plugin to function.
* 2.6	Fixed bugs that were present in 2.5. Manga+Press options page now located under Settings, Post New Comic page has been moved to Posts and Uninstall Manga+Press is located under Plugins.
* 2.6.1	Corrected Static page issue. Also changed mpp_filter_latest_comicpage() so that Post title is included in output.
* 2.6.2	Added multi-language support and made changes to directory parsing in mangapress-constants.php. Included spanish language PO/MO files.

= 2.5 =
* 2.1/2.5 2.1 renamed to 2.5. Eliminated the banner skin option and all functions attached. Feature can be duplicated with a little CSS positioning. Option for creating a banner from uploaded comic or uploading a seperate banner still remains, as well as the option to set banner width & height. Removed both the Manga+Press help and Template Tag pages. Will be hosted in a help wiki on the Manga+Press website. Made changes to the Post Comic page. Also reworded the "New Version" text. Created options to have the comic banner & navigation included at the top of The Loop on the home page, as well automatically filtering comic categories from the front page and automatically modifying The Loop for the latest comic page. Removed the make banner option.

= 2.0 =
* 2.0beta Major reworking of code in mangapress-classes.php and mangapress-fucntions.php

1. Reworked code of add_comic() function so it is compatible with the Wordpress post db and Media Library

2. removed create directory for series option

3. added wp_sidebar_comic()

* 2.0.1beta	Corrected a minor bug in update_options. Banner skin wouldn't be uploaded even if "use banner skin" option were checked and user had selected an image for upload. Also corrected a jQuery UI Tabs bug in the user admin area that is present when Manga+Press is used with Wordpress 2.8

= 1.0 =
* 1.0 RC1 General maintenance, fixing up look-and-feel of admin side. Putting together companion theme.

* 1.0 RC2 Modified add_comic(), add_footer_info()

* 1.0 RC2.5	Found a major bug involving directory/file permissions. Has been corrected, but I'm keeping my eye on this one for future reference. See website for a fix.


== Installation ==

1. Unpack the .zip file onto your hard drive.

2. Upload the `mangapress` folder to the `/wp-content/plugins/` directory.

3. Activate the plugin through the 'Plugins' menu in WordPress

4. Create two new pages; these pages will be your latest comic and comic archives pages. Label them something like 'Latest Comic' and 'Past Comics' or whatever, as long as it makes sense to you.

5. Create a new category; this one holds all of your comics. If you want, you can create additional child-categories to order your comics by series or chapters.

6. Click on the Manga+Press page tab in your Admin area and go to Comic Options, and set Comic Category to your newly created category, and set Latest Comic Page, and Comic Archive Page to your two newly created pages.


== Frequently Asked Questions ==


== Screenshots ==

1. screenshot-1.jpg
2. screenshot-2.jpg

== Credits ==

(c) 2008-2010 Jessica C. Green

Found a bug? Or did you find a bug and figure out a fix? Visit http://manga-press.silent-shadow.net/ or email me at jgreen@psy-dreamer.com. Please include screenshots, Wordpress version, a list of any other plugins you might have installed, or code (if you figured out a fix) and webserver configuration info; for example, Manga+Press was developed using a WAMP (Windows, Apache, MySQL, PHP) environment but works fine on the server my sites are hosted at, which is a LAMP environment. Be as detailed as possible.

For updates, you can visit http://manga-press.silent-shadow.net/

The code for the comic navigation is based on the MyComic Wordpress plugin found at http://borkweb.com/story/wordpress-plugin-mycomic-browser

Manga+Press also makes use of TimThumb, created by Tim McDaniels and Darren Hoyt with tweaks by Ben Gillbanks http://code.google.com/p/timthumb/

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA