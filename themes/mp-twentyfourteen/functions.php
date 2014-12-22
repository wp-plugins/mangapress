<?php
/**
 * @package Manga_Press_Templates
 * @subpackage Functions
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
add_action('admin_init', '_disable_options_init');
/**
 * Run the action that disables the insert_nav option.
 * 
 * @access private
 * @return void
 */
function _disable_options_init() {
    add_action('mangapress_option_fields', '_disable_options');
}

/**
 * Remove the insert_nav option from the options array.
 * 
 * @access private
 * @param array $options
 * @return array
 */
function _disable_options($options)
{
    // we're specifically looking for navigation...
    if (isset($options['nav']['insert_nav'])) {        
        unset($options['nav']['insert_nav']);
    }
    
    return $options;
    
}

add_action('wp_enqueue_scripts', 'mangapress_theme_load_twentyfourteen_css');
/**
 * Load the stylesheet from the TwentyEleven Theme
 * 
 * @return void
 */
function mangapress_theme_load_twentyfourteen_css()
{
    $src = get_template_directory_uri() . '/style.css';
    wp_register_style('twentyfourteen', $src, null, MP_VERSION);
    
    wp_enqueue_style('twentyfourteen');
}