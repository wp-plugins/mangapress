<?
/**
 * Display Option Tabs section
 * Some plugin writers prefer to use echo statements to output the code for their options tab,
 * I prefer to create seperate files and use include statements. Is much neater that way!
 *
 * @package Manga_Press
 * @subpackage Display_Option_Tabs
 * @since 0.5
 *
 */
 
// displays the upload form for the Post Comic tab
function main_page() {
global $mp_options;
	include("admin/page-main.php");
}
function upload_form(){
global $mp_options;

	include("admin/page-new-comic.php");
	
}
// displays the Comic Options tab, which is located in the Options tab
function comic_options(){
global $mp_options;

	include("admin/page-comic-options.php");

}

function series_list(){
global $mp_options;

	include("admin/page-manage-series.php");
}

function upgrade_mangapress() {
	include("admin/page-upgrade.php");
}

function remove_mangapress() {
	include("admin/page-uninstall.php");
}
?>