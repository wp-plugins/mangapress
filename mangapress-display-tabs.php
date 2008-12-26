<?
/*--------- Display Option Tabs section -------
	Some plugin writers prefer to use echo statements to output the code for their options tab,
	I prefer to create seperate files and use include statements. Is much neater that way!
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
// displays the help page, which is located in the Plugins tab
function display_help(){
	include("admin/page-manga-help.php");
}

function manage_series(){
global $mp_options;

	include("admin/page-manage-series.php");
}
function theme_manager_page(){
	
	include("admin/page-theme-help.php");
}
?>