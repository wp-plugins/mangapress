<?php

if (!defined('MP_VERSION')) define('MP_VERSION',	'2.6');
if (!defined('MP_DB_VERSION')) define('MP_DB_VERSION', '1.0');
if (!defined('MP_FOLDER')) define('MP_FOLDER', plugin_basename( dirname(__FILE__)) );
if (!defined('MP_ABSPATH')) define('MP_ABSPATH', WP_CONTENT_DIR.'/plugins/'.plugin_basename( dirname(__FILE__)).'/' );
if (!defined('MP_URLPATH')) define('MP_URLPATH', WP_CONTENT_URL.'/plugins/'.plugin_basename( dirname(__FILE__)).'/' );

?>