<?php
/**
 * @package Manga_Press
 * @version 2.5 beta
 * @author Jessica Green <jgreen@psy-dreamer.com>
 *
 * @todo Manga+Press website: Create Help Wiki.
 */
/*
 Plugin Name: Manga+Press Comic Manager
 Plugin URI: http://manga-press.silent-shadow.net/
 Description: Turns Wordpress into a full-featured Webcomic Manager. Be sure to visit <a href="http://manga-press.silent-shadow.net/">Manga+Press</a> for more info.
 Version: 2.5
 Author: Jessica Green
 Author URI: http://www.dumpster-fairy.com

*/
/**
 * (c) 2008, 2009 Jessica C Green
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
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
 *
 *              TODO: Manga+Press website: Create Help Wiki.
 */
 
/**
 * Global variables section
 *
 * @global object $wp_rewrite. WP_Rewrite object. @link http://codex.wordpress.org/Function_Reference/WP_Rewrite
 * @global object $wpdb. WPDB (Wordpress Database) Class object. @link http://codex.wordpress.org/Function_Reference/wpdb_Class
 * @global string $wp_version. Wordpres version declaration.
 * @global array $mp_options. Manga+Press options array.
 * @global array $messages. Status message string array. Not used.
 */ 
global $wp_rewrite, $wpdb, $wp_version, $mp_options, $messages;

include_once(ABSPATH . "/wp-includes/pluggable.php");
include_once("includes/mangapress-classes.php");
include_once("includes/mangapress-functions.php");
include_once("includes/mangapress-template-functions.php");
include_once("mangapress-display-tabs.php");

if (!defined('MP_VERSION')) {
	define('MP_VERSION',	'2.5');
}
if (!defined('MP_DB_VERSION')) {
	define('MP_DB_VERSION', '1.0');
}
if (!defined('MP_HOME_PAGE_URL')) {
	define('MP_HOME_PAGE_URL', 'http://www.dumpster-fairy.com/version.php');
}
define('MP_FOLDER', plugin_basename( dirname(__FILE__)) );
define('MP_ABSPATH', WP_CONTENT_DIR.'/plugins/'.plugin_basename( dirname(__FILE__)).'/' );
define('MP_URLPATH', WP_CONTENT_URL.'/plugins/'.plugin_basename( dirname(__FILE__)).'/' );
			 
$wpdb->mpcomics			= $wpdb->prefix . 'comics';
$wpdb->mpcomicseries	= $wpdb->prefix . 'comics_series';

$mp_options[latestcomic_cat]	=	get_option('comic_latest_default_category');
$mp_options[latestcomic_page]	=	get_option('comic_latest_page');
$mp_options[comic_archive_page]	=	get_option('comic_archive_page');
$mp_options[order_by]			=	get_option('comic_order_by');
$mp_options[banner_width]		=	(int)get_option('banner_width');
$mp_options[banner_height]		=	(int)get_option('banner_height');
$mp_options[make_thumb]			=	(int)get_option('comic_make_thmb');
$mp_options[nav_css]			=	get_option('comic_use_default_css');
$mp_options[comic_front_page]	=	(bool)get_option('comic_front_page');
$mp_options[insert_nav]			=	(bool)get_option('insert_nav');
$mp_options[mp_ver]				=	get_option('comic_plugin_ver'); // needed for updates tab
$mp_options[insert_banner]		=	(bool)get_option('insert_banner');
$mp_options[twc_code_insert]	=	(bool)get_option('twc_code_insert');
$mp_options[oc_code_insert]		=	(bool)get_option('oc_code_insert');
$mp_options[oc_comic_id]		=	get_option('oc_comic_id');

register_activation_hook( __FILE__, 'webcomicplugin_activate' );

add_action('admin_menu', 'web_init');
add_action('delete_post', 'delete_comic');
add_action('publish_post', 'add_comic_post');
add_action('create_category', 'add_series');
add_action('delete_category', 'delete_series');

if ($mp_options[nav_css] == 'default_css') { add_action('wp_head', 'add_navigation_css');}
add_action('wp_head',	'add_header_info');
add_action('wp_footer', 'add_footer_info');
add_action('wp_meta',	'add_meta_info');
add_action('pre_get_posts', 'filter_posts_frontpage');
add_action('the_post', 'filter_latest_comicpage');
if ($mp_options[twc_code_insert]){ add_action('loop_start', 'comic_insert_twc_update_code'); }
if ($mp_options[oc_code_insert]){ add_filter('the_content', 'comic_insert_oc_update_code'); }
if ($mp_options[insert_banner]){ add_action('loop_start', 'comic_insert_banner'); }
if ($mp_options[insert_nav]) { add_action('loop_start', 'comic_insert_navigation'); }

function web_init() {
global $mp_options;

	$main = add_menu_page	("Manga+Press Options",	"Manga+Press",	10,	MP_FOLDER,	'main_page', MP_URLPATH."/images/manga-press-icon.png");
	$new = add_submenu_page(MP_FOLDER,	"Manga+Press Options",	"Post Comic",		10,	'post_comic',		'upload_form');
	add_submenu_page(MP_FOLDER,	"Manga+Press Options",	"Series List",	10,	'series-list',	'series_list');
	$options = add_submenu_page(MP_FOLDER,	"Manga+Press Options",	"Comic Options",	10,	'comic-options',	'comic_options');
	$uninstall = add_submenu_page(MP_FOLDER,	"Manga+Press Options",	"Uninstall",	10, 'uninstall',	'remove_mangapress');
	if ( $mp_options[mp_ver] <> MP_VERSION ){
		$upgrade = add_submenu_page(MP_FOLDER,	"Manga+Press Options",	"Upgrade",	10,	'upgrade', 'upgrade_mangapress');
	}

	add_action("admin_print_scripts-$main",			'manga_press_admin_header');
	add_action("admin_print_scripts-$new",			'manga_press_admin_header');
	add_action("admin_print_scripts-$options",		'manga_press_admin_header');
	add_action("admin_print_scripts-$uninstall",	'manga_press_admin_header');
	add_action("admin_print_scripts-$upgrade",		'manga_press_admin_header');
}

function webcomicplugin_activate(){
global $mp_options, $wpdb;
require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

	// add comic options to database
	add_option('comic_latest_default_category',	'1',		'',	'yes');
	add_option('comic_latest_page',				'',			'',	'yes');
	add_option('comic_archive_page',			'',			'',	'yes');
	add_option('comic_plugin_ver',				MP_VERSION,		'',	'yes');
	add_option('comic_order_by',				'ID',		'',	'yes');
	add_option('banner_width',					'0',		'',	'yes');
	add_option('banner_height',					'0',		'',	'yes');	
	add_option('comic_make_thmb',				'1',		'',	'yes');
	add_option('comic_use_default_css',			'default_css',	'',	'yes');
	add_option('comic_db_ver',					MP_DB_VERSION,	'',	'yes');
	add_option('comic_front_page',				'0',		'',	'yes');
	add_option('insert_nav',					'0',		'',	'yes');
	add_option('insert_banner',					'0',		'',	'yes');
	add_option('twc_code_insert',				'0',		'',	'yes');
	add_option('oc_code_insert',				'0',		'',	'yes');
	add_option('oc_comic_id',					'',			'',	'yes');
	
	//
	//	create table to hold comics, but check first to see if there is a table there already...
	$newversion = false;
	if(get_settings('comic_plugin_ver') <> MP_VERSION) {
		$newversion = true;
		update_option('comic_plugin_ver', MP_VERSION);
	}
	
	if(($wpdb->get_var("show tables like '{".$wpdb->mpcomics."}'") != $wpdb->mpcomics) || $newversion) {

		$sql = $wpdb->prepare("CREATE TABLE ". $wpdb->mpcomics ." (
				`id` bigint(20) unsigned NOT NULL auto_increment,
				`post_id` mediumint(9) unsigned NOT NULL default '0',
				`post_date` datetime NOT NULL,
				PRIMARY KEY  (`id`),
				UNIQUE KEY `post_id` (`post_id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
	  	dbDelta($sql);
	}

	if(($wpdb->get_var("show tables like '{".$wpdb->mpcomicseries."}'") != $wpdb->mpcomicseries) || $newversion) {

		$sql = $wpdb->prepare("CREATE TABLE ". $wpdb->mpcomicseries ." (
				`series_id` bigint(20) NOT NULL AUTO_INCREMENT,
				`term_id` bigint(20) NOT NULL,
				PRIMARY KEY (`series_id`),
				UNIQUE KEY `term_id` (`term_id`)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");
	  	dbDelta($sql);
	}
}

function mangapress_uninstall() {
global $mp_options, $wpdb;
	
	check_admin_referer('mangapress-uninstall-form');
	$msg = "Removing Manga+Press...<br />";
	// delete comic options from database
	delete_option('comic_latest_default_category');
	delete_option('comic_latest_page');
	delete_option('comic_archive_page');
	delete_option('comic_order_by');
	delete_option('comic_front_page');
	delete_option('comic_make_banner');
	delete_option('banner_width');
	delete_option('banner_height');
	delete_option('comic_use_default_css');
	delete_option('comic_plugin_ver');
	delete_option('comic_db_ver');
	delete_option('comic_front_page');
	delete_option('insert_nav');
	delete_option('insert_banner');
	
	$msg .= "Manga+Press options have been removed...<br />";
	//
	//	remove comic tables
	$sql = $wpdb->prepare( "DROP TABLE ". $wpdb->mpcomics . ";" );
	$wpdb->query($sql);
	$msg .=  "$wpdb->mpcomics has been removed.<br />";

	$sql = $wpdb->prepare( "DROP TABLE ". $wpdb->mpcomicseries .";" );
	$wpdb->query($sql);
	$msg .=  "$wpdb->mpcomicseries has been removed.<br />";
	
	$msg .= "Be sure to remove all plugin files to complete the uninstall. ";
	
	return $msg;
}

function mangapress_upgrade() {
global $mp_options, $wpdb;

require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	check_admin_referer('mangapress-upgrade-form');
	
	$msg = "Manga+Press version ".get_settings('comic_plugin_ver')."<br />";
	if(get_settings('comic_plugin_ver') <> MP_VERSION) {
		update_option('comic_plugin_ver', MP_VERSION);
		$msg .= "Upgrading Manga+Press...<br />";
		//
		// Remove options from previous version
		if (delete_option('comic_banner_overlay_image'))
			$msg .= "Removed option <code>comic_banner_overlay_image</code>.<br />";
		if (delete_option('comic_use_overlay'))
			$msg .= "Removed option <code>comic_use_overlay</code>.<br />";
		if (delete_option('comic_make_banner'))
			$msg .= "Removed option <code>comic_make_banner</code>.<br />";
		//
		// Add new options...
		add_option('comic_front_page',	'0',	'',	'yes');
		$msg .= "Added option <code>comic_front_page</code>.<br />";
		add_option('insert_nav',		'0',	'',	'yes');
		$msg .= "Added option <code>insert_nav</code>.<br />";
		add_option('insert_banner',					'0',		'',	'yes');
		$msg .= "Added option <code>insert_banner</code>.<br />";		

		//
		// Make changes to databases...
		if(($wpdb->get_var("show tables like '{".$wpdb->mpcomics."}'") != $wpdb->mpcomics)) {
			// No database changes this version		
		}
		if(($wpdb->get_var("show tables like '{".$wpdb->mpcomicseries."}'") != $wpdb->mpcomicseries)) {
			// No database changes this version
		}
		$msg .= "Manga+Press has been upgraded to ".MP_VERSION."<br />";
	}
	return $msg;
}

function manga_press_admin_header() {
global $wp_version;

	echo "\n<meta name=\"Manga+Press\" content=\"".MP_VERSION."\" />\n";
	echo "<meta http-equiv=\"pragma\" content=\"no-cache\" />\n";
	echo "<link rel=\"stylesheet\" href=\"".MP_URLPATH."css/mp.admin.css\" />\n";
	
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('jquery-form');

	wp_admin_css( 'css/dashboard' );
}

function wp_comic_version() {
	
	echo MP_VERSION;	
}

if ( !function_exists('debug') ) {
					 
	function debug($varb){
	
		echo "<pre>";
		var_dump($varb);
		echo "</pre><br />";
	}
	
}
?>