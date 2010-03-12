<?php
/**
 * @package Manga_Press
 * @subpackage Manga_Press_Constants
 * @since 2.5
*/
$plugin_folder = str_replace('/includes','', plugin_basename( dirname(__FILE__) ) ); // remove /includes from the path structure

if (!defined('MP_VERSION')) define('MP_VERSION',	'2.6.2');
if (!defined('MP_DB_VERSION')) define('MP_DB_VERSION', '1.0');
if (!defined('MP_FOLDER')) define('MP_FOLDER', $plugin_folder );
if (!defined('MP_ABSPATH')) define('MP_ABSPATH', WP_CONTENT_DIR.'/plugins/'.$plugin_folder.'/' );
if (!defined('MP_URLPATH')) define('MP_URLPATH', WP_CONTENT_URL.'/plugins/'.$plugin_folder.'/' );

?>