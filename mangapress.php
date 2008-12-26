<?php
/*
Plugin Name: Manga+Press Comic Manager
Plugin URI: http://manga-press.silent-shadow.net/
Description: Turns Wordpress into a full-featured Webcomic Manager. Make sure to read both the "<a href="admin.php?page=webcomic_help">Manga+Press Help</a>" and "<a href="admin.php?page=theme-help">Template Tags</a>" sections understand how to configure and use the plugin with your own themes.
Version: 1.0 RC1
Author: Jessica Green
Author URI: http://www.dumpster-fairy.com
*/
/*
	(c) 2008 Jessica C Green
    
	This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
	
*/
/*
	Changelog
	0.1b	-	initial launch
	0.2b	-	10/14/08 Updated SQL queries to use $wpdb->prepare to help prevent SQL injection attacks.
				also found a workaround for the "Wrong datatype for second argument" error thrown by in_array in
				post.php when wp_insert_post() is called.
	0.3b	-	11/30/08 Added an option in the add comic area to add an optional banner image.
	0.5b	-	12/10/08 Cleaning up and streamlining code. An almost total re-write from the original 
				Wordpress Webcomic Manager Plugin back in February/March 2008. Worked out bugs involving
				comic posting feature, sort-by-date (the comics table needed a post_date column.
	1.0 RC1	-	General maintenance, fixing up look-and-feel of admin side. Putting together companion theme.
*/
global $wp_rewrite, $wpdb, $wp_version, $mp_options, $messages;

include_once(ABSPATH . "/wp-includes/pluggable.php");
include_once("includes/mangapress-classes.php");
include_once("includes/mangapress-functions.php");
include_once("includes/mangapress-template-functions.php");
include_once("mangapress-display-tabs.php");

if (!defined('MP_VERSION')) {
	define('MP_VERSION',	'1.0 RC1');
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
$mp_options[comic_dir]			=	get_option('comic_default_dir');
$mp_options[order_by]			=	get_option('comic_order_by');
$mp_options[series_organize]	=	(int)get_option('series_organize');
$mp_options[banner_overlay]		=	get_option('comic_banner_overlay_image');
$mp_options[use_overlay]		=	(bool)get_option('comic_use_overlay');
$mp_options[make_banner]		=	(bool)get_option('comic_make_banner');
$mp_options[banner_width]		=	(int)get_option('banner_width');
$mp_options[banner_height]		=	(int)get_option('banner_height');
$mp_options[nav_css]			=	get_option('comic_use_default_css');

register_activation_hook( __FILE__, 'webcomicplugin_activate' );
register_deactivation_hook( __FILE__ , 'webcomicplugin_deactivate' );

add_action('admin_menu', 'web_init');
add_action('delete_post', 'delete_comic');
add_action('create_category', 'add_series');
add_action('delete_category', 'delete_series');

if ($mp_options[nav_css] == 'default_css') {
	add_action('wp_head', 'add_navigation_css');
}
add_action('wp_head', 'add_header_info');
add_action('wp_footer', 'add_footer_info');
add_action('wp_meta', 'add_meta_info');

function webcomicplugin_activate(){
global $mp_options, $wpdb;
require_once(ABSPATH . 'wp-admin/upgrade-functions.php');


	// add comic options to database
	add_option('comic_latest_default_category',	'1',		'',	'yes');
	add_option('comic_latest_page',				'',			'',	'yes');
	add_option('comic_archive_page',			'',			'',	'yes');
	add_option('comic_default_dir',				'uploads',	'',	'yes');
	add_option('comic_plugin_ver',				MP_VERSION,		'',	'yes');
	add_option('comic_order_by',				'ID',		'',	'yes');
	add_option('series_organize',				'1',		'',	'yes');
	add_option('comic_make_banner',				'0',		'',	'yes');
	add_option('comic_use_default_css',			'default_css',	'',	'yes');
	add_option('comic_db_ver',					MP_DB_VERSION,	'',	'yes');
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
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
	  	dbDelta($sql);
	}

	if(($wpdb->get_var("show tables like '{".$wpdb->mpcomicseries."}'") != $wpdb->mpcomicseries) || $newversion) {

		$sql = $wpdb->prepare("CREATE TABLE ". $wpdb->mpcomicseries ." (
				`series_id` bigint(20) NOT NULL AUTO_INCREMENT,
				`term_id` bigint(20) NOT NULL,
				PRIMARY KEY (`series_id`),
				UNIQUE KEY `term_id` (`term_id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
	  	dbDelta($sql);
	}
}
function webcomicplugin_deactivate (){
global $mp_options, $wpdb;
require_once(ABSPATH . 'wp-admin/upgrade-functions.php');

	// delete comic options from database
	delete_option('comic_latest_default_category');
	delete_option('comic_latest_page');
	delete_option('comic_archive_page');
	delete_option('comic_default_dir');
	delete_option('comic_order_by');
	delete_option('series_organize');
	delete_option('comic_banner_overlay_image');
	delete_option('comic_use_overlay');
	delete_option('comic_make_banner');
	delete_option('banner_width');
	delete_option('banner_height');
	delete_option('comic_use_default_css');
	delete_option('comic_plugin_ver');
	delete_option('comic_db_ver');
	//
	//	remove comic tables
	$sql = $wpdb->prepare( "DROP TABLE ". $wpdb->mpcomics . ";" );
	$wpdb->query($sql);

	$sql = $wpdb->prepare( "DROP TABLE ". $wpdb->mpcomicseries .";" );
  	$wpdb->query($sql);

}
function web_init() {
global $mp_options;

	add_menu_page	("Manga+Press Options",			"Manga+Press",		10,	MP_FOLDER,			"main_page");
	add_submenu_page(MP_FOLDER,	"Manga+Press Options",	"Post Comic",		10,	'post_comic',		"upload_form");
	add_submenu_page(MP_FOLDER,	"Manga+Press Options",	"Manage Series",	10,	'manage-series',	'manage_series');
	add_submenu_page(MP_FOLDER,	"Manga+Press Options",	"Comic Options",	10,	'comic-options',	"comic_options");
	add_submenu_page(MP_FOLDER,	"Manga+Press Options",	"Manga+Press Help",	10, 'webcomic_help',	"display_help");
	add_submenu_page(MP_FOLDER,	"Manga+Press Options",	"Template Tags",	10,	'theme-help',	'theme_manager_page');

	add_action("admin_print_scripts", 'manga_press_admin_header');

}

function manga_press_admin_header() {
global $wp_version;

	echo "\n<meta name=\"Manga+Press\" content=\"".MP_VERSION."\" />\n";
	echo "\n<meta http-equiv=\"pragma\" content=\"no-cache\" />\n";
	echo "<link rel=\"stylesheet\" href=\"".MP_URLPATH."css/mp.admin.css\" />\n";
	
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-tabs');

	wp_admin_css( 'css/dashboard' );

}

function debug($varb){

	echo "<pre>";
	var_dump($varb);
	echo "</pre><br />";
}

function wp_comic_version() {
	
	echo MP_VERSION;	
}
?>