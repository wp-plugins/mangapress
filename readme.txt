=== Manga+Press Comic Manager ===
Contributors: Jessica Green
Donate link: http://manga-press.silent-shadow.net/
Tags: webcomics, online comics
Requires at least: 2.8
Tested up to: 2.8
Stable tag: 2.5

Manga+Press is a webcomic managment system for Wordpress.

== Description ==

Manga+Press is a webcomic managment system for Wordpress. Manga+Press uses Wordpress's posts, pages and categories to help you keep track of your comic posts. Manga+Press also includes its own custom template tags to help make creating themes easier. Version 2.5 contains some new features listed here:
    * Automatic Options:
          o You now have the option to automatically exclude the comic category from the front page.
          o You can now automatically have the comic navigation code inserted into the comic posts and comic page.
          o Comic banner can now be automatically inserted into the front page.
          o All of this can be done by enabling certain options in Manga+Press. No editing of Wordpress themes are necessary!
    * Comic Update Codes:
          o New options to insert both TheWebcomicList.com "Last Update: " and OnlineComics.net "PageScan"-codes.
          o Banner images are no longer generated. The front page banner now uses TimThumb to generate a cached image from the comic according to dimensions specified in Image Options.
    * Posting New Comics:
          o For greater control, you now have the option of using either Wordpress's Add New page or the Manga+Press Post Comic page. When using the Add New page, Manga+Press checks which categories the post is being assigned to and then automatically adds the post to the comic database and the required post meta if the assigned categories match the comic category specified in Comic Options.
          o Changes have been made to the Post Comic page. Categories are now listed as checkboxes instead of a dropdown, and an optional excerpt can be added.

== Installation ==

1. Unpack the .zip file onto your hard drive.

2. Upload the `mangapress` folder to the `/wp-content/plugins/` directory.

3. Activate the plugin through the 'Plugins' menu in WordPress

4. Create two new pages; these pages will be your latest comic and comic archives pages. Label them something like 'Latest Comic' and 'Past Comics' or whatever, as long as it makes sense to you.

5. Create a new category; this one holds all of your comics. If you want, you can create additional child-categories to order your comics by series or chapters.

6. Click on the Manga+Press page tab in your Admin area and go to Comic Options, and set Comic Category to your newly created category, and set Latest Comic Page, and Comic Archive Page to your two newly created pages.


== Frequently Asked Questions ==


== Screenshots ==

1. screenshot-1.png
2. screenshot-2.png
3. screenshot-3.png

== Credits ==

(c) 2008-2009 Jessica C. Green

Found a bug? Or did you find a bug and figure out a fix? Visit http://manga-press.silent-shadow.net/ or email me at jgreen@psy-dreamer.com. Please include screenshots, Wordpress version, a list of any other plugins you might have installed, or code (if you figured out a fix) and webserver configuration info; for example, Manga+Press was developed using a WAMP (Windows, Apache, MySQL, PHP) environment but works fine on the server my sites are hosted at, which is a LAMP environment. Be as detailed as possible.

For updates, you can visit http://manga-press.silent-shadow.net/

The code for the comic navigation is based on the MyComic Wordpress plugin found at http://borkweb.com/story/wordpress-plugin-mycomic-browser

Manga+Press also makes use of TimThumb, created by Tim McDaniels and Darren Hoyt with tweaks by Ben Gillbanks http://code.google.com/p/timthumb/

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA