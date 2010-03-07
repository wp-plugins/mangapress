<?php
/**
 * @package Manga_Press
 * @version 2.6b
 * @author Jessica Green <jgreen@psy-dreamer.com>
 *
 * @todo Manga+Press website: Create Help Wiki.
 */
/*
 Plugin Name: Manga+Press Comic Manager
 Plugin URI: http://manga-press.silent-shadow.net/
 Description: Turns Wordpress into a full-featured Webcomic Manager. Be sure to visit <a href="http://manga-press.silent-shadow.net/">Manga+Press</a> for more info.
 Version: 2.6
 Author: Jessica Green
 Author URI: http://www.dumpster-fairy.com
*/
/*
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
 */
/**
 * Global variables section
 *
 * @global object $wp_rewrite. WP_Rewrite object. @link http://codex.wordpress.org/Function_Reference/WP_Rewrite
 * @global object $wpdb. WPDB (Wordpress Database) Class object. @link http://codex.wordpress.org/Function_Reference/wpdb_Class
 * @global string $wp_version. Wordpres version declaration.
 * @global array $mp_options. Manga+Press options array.
 */ 
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
//ini_set('error_reporting', E_ALL);

include_once(ABSPATH . "/wp-includes/pluggable.php");
include_once("includes/mangapress-constants.php");
include_once("includes/mangapress-classes.php");
include_once("includes/mangapress-functions.php");
include_once("includes/mangapress-template-functions.php");
include_once("includes/mangapress-pages.php");

global $wp_rewrite, $wpdb, $wp_version, $wp_roles, $mp_options;

$wpdb->mpcomics = $wpdb->prefix . 'comics';
$mp_options = unserialize( get_option('mangapress_options') );

// Adjusting version number from 2.9 to 2.6
// Will be removed with 2.6.5 or 2.7
$installed_ver = strval( get_option('mangapress_ver') ); // version 2.5 and older; will be changed in later upgrades
if ( version_compare( $installed_ver, '2.9', '==' ) ) {
	update_option('mangapress_ver', MP_VERSION, '', 'no');	
}

add_action('admin_init', 'mangapress_options_init');
add_action('admin_menu', 'mangapress_admin_init');
add_action('delete_post', 'mpp_delete_comic_post');
add_action('publish_post', 'mpp_add_comic_post');
add_action('edit_post', 'mpp_edit_comic_post' );

if ($mp_options['nav_css'] == 'default_css') { add_action('wp_head', 'mpp_add_nav_css');}
add_action('wp_head',	'mpp_add_header_info');
add_action('wp_footer', 'mpp_add_footer_info');
add_action('wp_meta',	'mpp_add_meta_info');
add_action('wp', 'mpp_filter_posts_frontpage');

if ( (bool)$mp_options['latestcomic_page'] ) add_action('the_content', 'mpp_filter_latest_comicpage');
if ( (bool)$mp_options['comic_archive_page'] ) add_action('the_content', 'mpp_filter_comic_archivepage');
if ($mp_options['twc_code_insert']) add_action('loop_start', 'mpp_comic_insert_twc_update_code');
if ($mp_options['insert_banner']) add_action('loop_start', 'mpp_comic_insert_banner'); 
if ($mp_options['insert_nav']) add_action('loop_start', 'mpp_comic_insert_navigation'); 

/**
 * mangapress_admin_init()
 *
 * @since 2.6
 *
 * Loads Manga+Press Options Pages
 *
 */
function mangapress_admin_init() {
global $mp_options;

	add_options_page("Manga+Press Options",	"Manga+Press Options",	'manage_options',	'mangapress-options',	'mangapress_options_page');
	add_submenu_page('edit.php', "Manga+Press Options", "Post New Comic" ,'administrator', 'post-comic', 'mangapress_post_comic');
	
	$uninstall	=	add_submenu_page("plugins.php",	"Manga+Press Options",	"Manga+Press Uninstall",	'administrator',	'uninstall',	'remove_mangapress');
	if ( get_option('mangapress_upgrade') == 'yes' ){
		$upgrade =  add_submenu_page("plugins.php",	"Manga+Press Options",	"Manga+Press Upgrade",		'administrator',	'upgrade', 		'upgrade_mangapress');
	}
}

/**
 * mangapress_options_init()
 *
 * @since 2.6b
 *
 * Registers Manga+Press settings
 *
 */
function mangapress_options_init(){
	// Adding new options...
	register_setting( 'mangapress-options', 'mangapress_options', 'update_mangapress_options' );
}

/**
 * mangapress_activate()
 *
 * @since 0.1b
 *
 * Manga+Press activation hook. Was originally webcomicplugin_activate()
 *
 */
function mangapress_activate(){
global $mp_options, $wpdb , $wp_roles, $wp_version, $wp_rewrite;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	// Check for capability
	if ( !current_user_can('activate_plugins') ) 
		wp_die( __('Sorry, you do not have suffient permissions to activate this plugin.') );
	
	// Get the capabilities for the administrator
	$role = get_role('administrator');
	
	// Must have admin privileges in order to activate.
	if ( empty($role) )
		wp_die( __('Sorry, you must be an Administrator in order to use Manga+Press') );

	//
	// On activation, check if tables already exist. If they don't, create them
	// add charset & collate like wp core
	$charset_collate = '';

	if ( version_compare(mysql_get_server_info(), '4.1.0', '>=') ) {
		if ( ! empty($wpdb->charset) )
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) )
			$charset_collate .= " COLLATE $wpdb->collate";
	}
		
	if ( $wpdb->get_var("show tables like '$wpdb->mpcomics'") != $wpdb->mpcomics ) {

		$sql = $wpdb->prepare("CREATE TABLE ". $wpdb->mpcomics ." (
				`id` bigint(20) unsigned NOT NULL auto_increment,
				`post_id` mediumint(9) unsigned NOT NULL default '0',
				`post_date` datetime NOT NULL,
				PRIMARY KEY  (`id`),
				UNIQUE KEY `post_id` (`post_id`)
				) $charset_collate;");
	  	dbDelta($sql);
	}
	//
	// Pull the current Manga+Press options from the database.
	// If it's empty, either this is a first-time install or is an
	// upgrade from Manga+Press 2.5 where the options were stored
	// in seperate rows in the database...
	$options = get_option('mangapress_options');
	
	// set the default settings, if we didn't upgrade
	if ( empty( $options ) )
		mangapress_set_options();
			
   	$wp_rewrite->flush_rules();
}

/**
 * mangapress_deactivate()
 *
 * @since 2.6
 *
 * Manga+Press deactivation hook. Does the clean-up after 
 * uninstall has run.
 *
 */
function mangapress_deactivate(){
global $mp_options, $wpdb , $wp_roles, $wp_version, $wp_rewrite;
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

   	$wp_rewrite->flush_rules();
	
}
/**
 * mangapress_set_options()
 *
 * @since 2.6b
 *
 * Sets default options if activation wasn't an upgrade or
 * copies old options over to new options if it is an upgrade
 *
 */
function mangapress_set_options() {
	
	$installed_ver = strval( get_option('comic_plugin_ver') ); // version 2.5 and older; will be changed in later upgrades
	
 	if ( version_compare( $installed_ver, '2.6', '<' ) && !($installed_ver == '') ) { // will only validate if $installed_ver is not empty
		$mp_options['nav_css']			=	(string)get_option('comic_use_default_css');
		$mp_options['order_by']			=	(string)get_option('comic_order_by');
		$mp_options['insert_nav']			=	(bool)get_option('insert_nav');
		$mp_options['latestcomic_cat']	=	(int)get_option('comic_latest_default_category');
		$mp_options['comic_front_page']	=	(bool)get_option('comic_front_page');
		$mp_options['latestcomic_page']	=	(int)get_option('comic_latest_page');
		$mp_options['comic_archive_page']	=	(int)get_option('comic_archive_page');
		$mp_options['make_thumb']			=	(int)get_option('comic_make_thmb');
		$mp_options['insert_banner']		=	(bool)get_option('insert_banner');
		$mp_options['banner_width']		=	(int)get_option('banner_width');
		$mp_options['banner_height']		=	(int)get_option('banner_height');
		$mp_options['twc_code_insert']	=	(bool)get_option('twc_code_insert');
		$mp_options['oc_code_insert']		=	(bool)get_option('oc_code_insert');
		$mp_options['oc_comic_id']		=	(int)get_option('oc_comic_id');
		
		add_option( 'mangapress_upgrade', 'yes', '', 'no'); //  Manga+Press checks for this to display the upgrade page
	}elseif ($installed_ver == '') { // if $installed_ver is empty
		// add comic options to database	
		$mp_options['nav_css']							=	'default_css';
		$mp_options['order_by']							=	'post_date';
		$mp_options['insert_nav']						=	false;
		$mp_options['latestcomic_cat']					=	0;
		$mp_options['comic_front_page']					=	false;
		$mp_options['latestcomic_page']					=	0;
		$mp_options['comic_archive_page']				=	0;
		$mp_options['make_thumb']						=	false;
		$mp_options['insert_banner']					=	false;
		$mp_options['banner_width']						=	0;
		$mp_options['banner_height']					=	0;
		$mp_options['twc_code_insert']					=	false;
		$mp_options['oc_code_insert']					=	false;
		$mp_options['oc_comic_id']						=	0;
		
		add_option('mangapress_ver',		MP_VERSION,				'', 'no');
		add_option('mangapress_db_ver',		MP_DB_VERSION,			'',	'no');	
	}
	
	add_option( 'mangapress_options', serialize( $mp_options ), '', 'no' );
}
/**
 * mangapress_upgrade()
 *
 * @since 2.0 beta
 *
 * Handles the process of upgrading from previous versions by
 * copying over old options to new options and deleting old
 * options. Also handles any changes to database schema.
 * 
 */
function mangapress_upgrade() {
global $mp_options, $wpdb;

require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	check_admin_referer('mangapress-upgrade-form');
	
	$msg = "Manga+Press version ".MP_VERSION."<br />"; // this is the old option from 2.5.1
	if(get_option('mangapress_upgrade') == 'yes') {
		
		$wpdb->mpcomicseries	= $wpdb->prefix . 'comics_series';

		$msg .= "Upgrading Manga+Press...<br />";
		
		// these two options are new in 2.6	
		add_option('mangapress_ver',		MP_VERSION,				'', 'no');
		add_option('mangapress_db_ver',		MP_DB_VERSION,			'',	'no');
		
		$msg .= 'Deleting old options....<br/>';
		//
		// Remove options from previous version, which would be versions 1.0 to 2.5
		delete_option('comic_latest_default_category');
		delete_option('comic_latest_page');
		delete_option('comic_archive_page');
		delete_option('comic_plugin_ver');
		delete_option('comic_order_by');
		delete_option('banner_width');
		delete_option('banner_height');
		delete_option('comic_make_thmb');
		delete_option('comic_use_default_css');
		delete_option('comic_db_ver');
		delete_option('comic_front_page');
		delete_option('insert_nav');
		delete_option('insert_banner');
		delete_option('twc_code_insert');
		delete_option('oc_code_insert');
		delete_option('oc_comic_id');
		delete_option('comic_front_page');
		delete_option('insert_banner');
		
		delete_option('mangapress_upgrade');
		$msg .= 'Old options have been deleted from the database.<br/>';
		//
		// Make changes to databases...
		if(($wpdb->get_var("show tables like '".$wpdb->mpcomics."'") != $wpdb->mpcomics)) {
			// No database changes this version		
		}
		if( $wpdb->get_var("show tables like '".$wpdb->mpcomicseries."'") ) {
			$sql = $wpdb->prepare( "DROP TABLE ". $wpdb->mpcomicseries .";" );
			$wpdb->query($sql);
			$msg .=  "$wpdb->mpcomicseries has been removed.<br />";
		}
		$msg .= "Manga+Press has been upgraded to ".MP_VERSION."<br />";
	}
	return $msg;
}
/**
 * mangapress_uninstall()
 *
 * @since 1.0 beta
 *
 * Manga+Press uninstall function. Handles the process
 * of removing Manga+Press options and extra tables before
 * Manga+Press is deactivated.
 *
 */
function mangapress_uninstall() {
global $mp_options, $wpdb;
	
	check_admin_referer('mangapress-uninstall-form');
	$msg = "Removing Manga+Press...<br />";
	//
	// delete comic options from database
	delete_option('mangapress_options');
	delete_option('mangapress_ver');
	delete_option('mangapress_db_ver');
	$msg .= "Manga+Press options have been removed...<br />";
	//
	//	remove comic tables
	$sql = $wpdb->prepare( "DROP TABLE ". $wpdb->mpcomics . ";" );
	$wpdb->query($sql);
	$msg .=  "$wpdb->mpcomics has been removed.<br />";

	$msg .= "Be sure to remove all plugin files to complete the uninstall. ";
	
	return $msg;
}
function mpp_debug($varb){
	
	echo "<pre style='height: 400px; overflow: scroll; text-align: left; font-family: fixed; font-size: 12px;'>";
	var_dump($varb);
	echo "</pre><br />";
}

register_activation_hook( __FILE__, 'mangapress_activate' );
register_deactivation_hook( __FILE__, 'mangapress_deactivate' );
register_uninstall_hook( __FILE__, 'mangapress_uninstall' );
?>