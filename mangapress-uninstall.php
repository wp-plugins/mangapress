<?
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']) && $_POST[action] != 'uninstall_mangapress' ) { die('You are not allowed to call this page directly.'); }
?>
<?php
function remove_all() {
	
	$msg = 'succeeded!';
	
	return $msg;
}
?>