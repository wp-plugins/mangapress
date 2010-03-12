<?php
/**
 * @package Manga_Press
 * @subpackage Manga_Press_Changelog
 * @since 2.5
*/
/**
 * Changelog
 * 0.1b	-	initial launch
 * 0.2b	-	10/14/08 Updated SQL queries to use $wpdb->prepare to help prevent SQL injection attacks.
 * 			also found a workaround for the "Wrong datatype for second argument" error thrown by in_array in
 * 			post.php when wp_insert_post() is called.
 * 0.3b	-	11/30/08 Added an option in the add comic area to add an optional banner image.
 * 0.5b	-	12/10/08 Cleaning up and streamlining code. An almost total re-write from the original
 * 			Wordpress Webcomic Manager Plugin back in February/March 2008. Worked out bugs involving
 * 			comic posting feature, sort-by-date (the comics table needed a post_date column.
 * 1.0 RC1-	General maintenance, fixing up look-and-feel of admin side. Putting together companion theme.
 * 1.0 RC2-	Modified add_comic(), add_footer_info()
 * 1.0 RC2.5-Found a major bug involving directory/file permissions. Has been corrected, but I'm keeping my
 * 			eye on this one for future reference. See website for a fix.
 * 2.0beta -Major reworking of code in mangapress-classes.php and mangapress-fucntions.php
 * 				* Reworked code of add_comic() function so it is compatible with the Wordpress post db and Media Library
 * 				* removed create directory for series option
 * 				* added wp_sidebar_comic()
 * 2.0.1beta -	Corrected a minor bug in update_options. Banner skin wouldn't be uploaded even if "use banner skin" option
 * 				were checked and user had selected an image for upload. Also corrected a jQuery UI Tabs bug in the user admin
 * 				area that is present when Manga+Press is used with Wordpress 2.8
 * 2.1/2.5	-	2.1 renamed to 2.5. Eliminated the banner skin option and all functions attached. Feature can be duplicated with a little CSS
 * 				positioning. Option for creating a banner from uploaded comic or uploading a seperate banner still remains, as well
 * 				as the option to set banner width & height. Removed both the Manga+Press help and Template Tag pages. Will be hosted
 * 				in a help wiki on the Manga+Press website. Made changes to the Post Comic page. Also reworded the "New Version" text.
 * 				Had a typo :(
 * 				Created options to have the comic banner & navigation included at the top of The Loop on the home page, as well
 * 				automatically filtering comic categories from the front page and automatically modifying The Loop for the latest
 * 				comic page. Removed the make banner option.
 * 2.6		-	Changed handling of plugin options. They are now stored in one entry in the options table instead of being spread
 * 				out over multiple entries.
 *				Fixed bugs that were present in 2.5. Manga+Press options page now located under Settings, Post New Comic page has
 *				been moved to Posts and Uninstall Manga+Press is located under Plugins.
 * 2.6.1	-	Corrected Static page issue. Also changed mpp_filter_latest_comicpage() so that Post title is included in output.
 * 2.6.2	-	Added multi-language support and made changes to directory parsing in mangapress-constants.php
 *
 */
?>