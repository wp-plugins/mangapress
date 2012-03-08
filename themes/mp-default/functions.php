<?php
/**
 * @package Manga_Press_Templates
 * @subpackage Functions
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
add_action('admin_init', 'disable_options_init');

function disable_options_init() {
    add_action('mangapress_option_fields', 'disable_options');
}

function disable_options($options)
{
    // we're specifically looking for navigation...
    if (isset($options['nav']['insert_nav'])) {        
        unset($options['nav']['insert_nav']);
    }
    
    return $options;
    
}