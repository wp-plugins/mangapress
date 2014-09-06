<?php
/**
 * @package Manga_Press
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 *
 */
/*
 Plugin Name: Manga+Press Comic Manager
 Plugin URI: http://www.manga-press.com/
 Description: Turns WordPress into a full-featured Webcomic Manager. Be sure to visit <a href="http://www.manga-press.com/">Manga+Press</a> for more info.
 Version: 2.8.1
 Author: Jessica Green
 Author URI: http://www.jes.gs
*/
/*
 * (c) 2014 Jessica C Green
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
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('You are not allowed to call this page directly.');

$plugin_folder = plugin_basename(dirname(__FILE__));

if (!defined('MP_VERSION'))
    define('MP_VERSION', '2.8.1');

if (!defined('MP_FOLDER'))
    define('MP_FOLDER', $plugin_folder);

if (!defined('MP_ABSPATH'))
    define('MP_ABSPATH', plugin_dir_path(__FILE__));

if (!defined('MP_URLPATH'))
    define('MP_URLPATH', plugin_dir_url(__FILE__));

if (!defined('MP_LANG'))
    define('MP_LANG', $plugin_folder . '/lang');

if (!defined('MP_DOMAIN'))
    define('MP_DOMAIN', $plugin_folder);

require_once MP_ABSPATH . 'includes/lib/form/element.php';
require_once MP_ABSPATH . 'includes/lib/helper.php';
require_once MP_ABSPATH . 'includes/lib/post-type.php';
require_once MP_ABSPATH . 'includes/lib/taxonomy.php';
require_once MP_ABSPATH . 'includes/functions.php';
require_once MP_ABSPATH . 'includes/template-functions.php';
require_once MP_ABSPATH . 'mangapress-install.php';
require_once MP_ABSPATH . 'mangapress-admin.php';
require_once MP_ABSPATH . 'mangapress-options.php';
require_once MP_ABSPATH . 'mangapress-posts.php';

$install = MangaPress_Install::get_instance();

register_activation_hook(__FILE__, array($install, 'do_activate'));
register_deactivation_hook(__FILE__, array($install, 'do_deactivate'));

add_action('plugins_loaded', array('MangaPress_Bootstrap', 'load_plugin'));

/**
 * Plugin bootstrap class.
 *
 * @package MangaPress
 * @subpackage MangaPress_Bootstrap
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class MangaPress_Bootstrap
{

    /**
     * Options array
     *
     * @var array
     */
    protected $_options;

    /**
     * Instance of MangaPress_Bootstrap
     *
     * @var MangaPress_Bootstrap
     */
    protected static $_instance;

    /**
     * MangaPress Posts object
     *
     * @var \MangaPress_Posts
     */
    protected $_posts_helper;

    /**
     * Options helper object
     *
     * @var \MangaPress_Options
     */
    protected $_options_helper;
    
    /**
     * Admin page helper
     * 
     * @var MangaPress_Admin
     */
    protected $_admin_helper;
    
    /**
     * Static function used to initialize Bootstrap
     *
     * @return void
     */
    public static function load_plugin()
    {
        self::$_instance  = new self();
    }

    /**
     * Get instance of MangaPress_Bootstrap
     *
     * @return MangaPress_Bootstrap
     */
    public static function get_instance()
    {
        if (null == self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * PHP5 constructor method
     *
     * @return void
     */
    protected function __construct()
    {

        load_plugin_textdomain(MP_DOMAIN, false, MP_LANG);

        $this->set_options();

        add_action('setup_theme', array($this, 'setup_theme'));
        add_action('init', array($this, 'init'), 50);        
        add_action('template_include', 'mpp_comic_single_page');        
    }

    /**
     * Because register_theme_directory() can't run on init.
     *
     * @return void
     */
    public function setup_theme()
    {
        register_theme_directory('plugins/' . MP_FOLDER . '/themes');
    }

    /**
     * Run init functionality
     *
     * @see init() hook
     * @return void
     */
    public function init()
    {
        $this->_admin_helper   = new MangaPress_Admin();
        $this->_options_helper = new MangaPress_Options();
        $this->_posts_helper   = new MangaPress_Posts();

        $this->_load_current_options();
        
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        
        if (get_option('mangapress_upgrade') == 'yes') {
            MangaPress_Install::do_upgrade();
        }
    }
    
    /**
     * Get a MangaPress helper
     * 
     * @param string $helper_name Allowed values: admin, options, posts
     * @return \MangaPress_Admin|\MangaPress_Options|\MangaPress_Posts|\WP_Error
     */
    public function get_helper($helper_name)
    {
        $helper = "_{$helper_name}_helper";
        if (property_exists($this, $helper)) {
            return $this->$helper;
        }
        
        return new WP_Error('_mangapress_helper_access', 'No helper exists by that name');
    }

    /**
     * Set MangaPress options. This method should run every time
     * MangaPress options are updated.
     *
     * @uses init()
     * @see MangaPress_Bootstrap::init()
     *
     * @return void
     */
    public function set_options()
    {
        $this->_options = maybe_unserialize(get_option('mangapress_options'));
    }

    /**
     * Get MangaPress options
     *
     * @return array
     */
    public function get_options()
    {
        return $this->_options;
    }

    /**
     * Load current plugin options
     *
     * @return void
     */
    private function _load_current_options()
    {
        $mp_options = $this->get_options();

        /*
         * Disable/Enable Default Navigation CSS
         */
        if ($mp_options['nav']['nav_css'] == 'default_css')
            add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));

        /*
         * Comic Navigation
         */
        if ($mp_options['nav']['insert_nav'])
            add_action('the_content', 'mpp_comic_insert_navigation');

        /*
         * Lastest Comic Page
         */
        if ((bool)$mp_options['basic']['latestcomic_page']
                && !(bool)$mp_options['basic']['latestcomic_page_template']) {
            add_filter('the_content', 'mpp_filter_latest_comic');
        }

        /*
         * Latest Comic Page template override
         */        
        if ((bool)$mp_options['basic']['latestcomic_page_template']) {
            add_filter('template_include', 'mpp_latest_comic_page');
        }
        /*
         * Comic Archive Page
         */
        if ((bool)$mp_options['basic']['comicarchive_page']
                && !(bool)$mp_options['basic']['comicarchive_page_template']) {
            add_filter('the_content', 'mpp_filter_comic_archivepage');
        }

        /*
         * Comic Archive Page template override
         */
        if ((bool)$mp_options['basic']['comicarchive_page_template']) {
            add_filter('template_include', 'mpp_comic_archivepage');
        }
        
        /*
         * Comic Page size
         */
        if ($mp_options['comic_page']['generate_comic_page']){
            add_image_size(
                'comic-page',
                $mp_options['comic_page']['comic_page_width'],
                $mp_options['comic_page']['comic_page_height'],
                false
            );
        }

        /*
         * Comic Thumbnail size for Comics Listing screen
         */
        add_image_size('comic-admin-thumb', 60, 80, true);

    }

    /**
     * Enqueue default navigation stylesheet
     *
     * @return void
     */
    public function wp_enqueue_scripts()
    {
        /*
         * Navigation style
         */
        wp_register_style(
            'mangapress-nav',
            MP_URLPATH . 'assets/css/nav.css',
            null,
            MP_VERSION,
            'screen'
        );

        wp_enqueue_style('mangapress-nav');
    }
    
    
    /**
     * Enqueue admin-related styles
     * 
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        wp_enqueue_style(
            'mangapress-icons',
            plugins_url('assets/css/font.css', __FILE__),
            null,
            MP_VERSION,
            'screen'
        );
    }
}
