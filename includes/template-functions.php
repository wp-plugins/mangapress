<?php
/**
 * @package Manga_Press
 * @subpackage Includes
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
/**
 * @package Includes
 * @subpackage Manga_Press_Template_Functions
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */

/**
 * is_comic()
 *
 * Used to detect if post contains a comic.
 * @since 0.1
 *
 * @global object $wpdb
 * @global array $mp_options
 * @global object $post
 * @return bool Returns true if post contains a comic, false if not.
 */
if (!function_exists('is_comic')) {
    function is_comic($post = null)
    {
        if (is_integer($post)) {
            $post = get_post($post);
        };

        if (is_null($post)) {
            global $post;
        }

        $post_type = get_post_type($post);

        return ($post_type == 'mangapress_comic');
    }
}
/**
 * @since 1.0 RC1
 *
 * @global array $mp_options
 * @return bool
 */
if (!function_exists('is_comic_page')) {
    function is_comic_page()
    {
        global $mp, $wp_query;
        
        $mp_options = $mp->get_options(); 
        $query      = $wp_query->get_queried_object();
        
        return ($wp_query->is_page && ($query->post_name == $mp_options['basic']['latestcomic_page']));

    }
}
/**
 *
 * @since 1.0 RC1
 *
 * @global array $mp_options
 * @return bool
 */
if (!function_exists('is_comic_archive_page')) {
    function is_comic_archive_page()
    {
        global $mp, $wp_query;

        $mp_options = $mp->get_options(); 
        $query      = $wp_query->get_queried_object();

        $is_comic_archive_page
            = ($wp_query->is_page && ($query->post_name
                                        == $mp_options['basic']['comicarchive_page']));

        return $is_comic_archive_page;

    }
}

/**
 * wp_comic_navigation()
 *
 * Displays navigation for post specified by $post_id.
 *
 * @since 0.1b
 *
 * @global object $wpdb
 * @param WP_Query $query Query for post object or page.
 * @param array $args Arguments for navigation output
 * @param bool $echo Specifies whether to echo comic navigation or return it as a string
 * @return string Returns navigation string if $echo is set to false.
 */
function mangapress_comic_navigation(WP_Query $query = null, $args = array(), $echo = true)
{
    global $mp;

    $mp_options = $mp->get_options();

    $defaults = array(
        'container'      => 'nav',
        'container_attr' => array('id' => 'comic-navigation'),
        'items_wrap'     => '<ul%1$s>%2$s</ul>',
        'items_wrap_attr' => array('class' => 'comic-nav'),
        'link_wrap'      => 'li',
        'link_before'    => '',
        'link_after'     => '',
    );

    $args = wp_parse_args($args, $defaults);
    $args = apply_filters('mangapress_comic_navigation_args', $args);
    $args = (object) $args;



    if (is_null($query)) {
        global $wp_query;

        $query = $wp_query;

        $is_comic = ($query->post->post_type == "mangapress_comic");

        if ($query->is_post_type_archive && $is_comic) {
            $query->set('posts_per_page', '1');
        } elseif ($query->is_single && $is_comic) {
            global $post;

            $group = (bool)$mp_options['basic']['group_comics'];

            $next_post  = mpp_get_adjacent_comic($group, 'mangapress_series', null, false);
            $prev_post  = mpp_get_adjacent_comic($group, 'mangapress_series', null, true);
            $last_post  = mpp_get_boundary_comic($group, 'mangapress_series', null, false);
            $first_post = mpp_get_boundary_comic($group, 'mangapress_series', null, true);

            $current_page = $post->ID; // use post ID this time.

            $next_page = !isset($next_post->ID)
                       ? $current_page : $next_post->ID;

            $prev_page = !isset($prev_post->ID)
                       ? $current_page : $prev_post->ID;

            $last      = !isset($last_post[0]->ID)
                       ? $current_page : $last_post[0]->ID;

            $first     = !isset($first_post[0]->ID)
                       ? $current_page : $first_post[0]->ID;

            $first_url = get_permalink($first);
            $last_url  = get_permalink($last);
            $next_url  = get_permalink($next_page);
            $prev_url  = get_permalink($prev_page);

        } else {
            return false;
        }
    } else {

        if ($mp_options['group_comics']) {
            $term = wp_get_object_terms($query->post->ID, 'series');
            $query->set(
                'tax_query',
                array(
                    'relation' => 'AND',
                    array(
                        'taxonomy'   => 'series',
                        'field'      => 'slug',
                        'terms'      => $term[0]->slug,
                    ),
                )
            );

            $query->get_posts();
        }

        // we'll use WordPress's paging system to generate the required navigation
        $first     = $query->max_num_pages; // last is most recent
        $last      = (float)1;

        //
        // Current page will help us determine the previous and next pages
        $paged        = $query->get('paged');
        $current_page = ($paged == 0) ? $last : $paged;
        $next_page    = ($current_page == $last) ? $last : $current_page - 1;
        $prev_page    = ($current_page == $first) ? $first : $current_page + 1;

        $first_url = get_pagenum_link($first);
        $last_url = get_pagenum_link($last);
        $next_url = get_pagenum_link($next_page);
        $prev_url = get_pagenum_link($prev_page);
    }

    $show_container = false;
    $comic_nav      = "";
    if ( $args->container ) {

        $show_container = true;
        $attr           = "";
        if (!empty($args->container_attr)) {
            $attr_arr = array();
            foreach ($args->container_attr as $name => $value) {
                $attr_arr[] = "{$name}=\"" . esc_attr($value) . "\"";
            }

            $attr = " " . implode(" ", $attr_arr);
        }

        $comic_nav .= "<{$args->container}$attr>";
    }

    $items_wrap_attr = "";
    if (!empty($args->items_wrap_attr)) {
        $items_attr_arr = array();
        foreach ($args->items_wrap_attr as $name => $value) {
            $items_attr_arr[] = "{$name}=\"" . esc_attr($value) . "\"";
        }

        $items_wrap_attr = " " . implode(" ", $items_attr_arr);
    }

    $items = array();

    // Here, we start processing the urls.
    // Let's do first page first.
    $first_html = "<{$args->link_wrap}>" . ( ($first == $current_page)
                ? '<span class="comic-nav-span">' . __('First', 'mangapress') . '</span>'
                : '<a href="' . $first_url . '">' . __('First', 'mangapress') . '</a>' )
             . "</{$args->link_wrap}>";

    $last_html = "<{$args->link_wrap}>" .
                ( ($last == $current_page)
                    ? '<span class="comic-nav-span">' . __('Last', 'mangapress') . '</span>'
                    : '<a href="' . $last_url . '">'. __('Last', 'mangapress') . '</a>')
                . "</{$args->link_wrap}>";

    $next_html = "<{$args->link_wrap}>" . ( ($next_page == $current_page)
                ? '<span class="comic-nav-span">' . __('Next', 'mangapress') . '</span>'
                : '<a href="' . $next_url . '">'. __('Next', 'mangapress') . '</a>' )
            . "</{$args->link_wrap}>";

    $prev_html = "<{$args->link_wrap}>" . ( ($prev_page == $current_page)
                ? '<span class="comic-nav-span">' . __('Prev', 'mangapress') . '</span>'
                : '<a href="' . $prev_url . '">'. __('Prev', 'mangapress') . '</a>' )
            . "</{$args->link_wrap}>";

    $items['first'] = apply_filters('mangapress_comic_navigation_first', $first_html, $args);
    $items['prev']  = apply_filters('mangapress_comic_navigation_prev', $prev_html, $args);
    $items['next']  = apply_filters('mangapress_comic_navigation_next', $next_html, $args);
    $items['last']  = apply_filters('mangapress_comic_navigation_last', $last_html, $args);

    $items_str      = implode(" ", apply_filters( 'mangapress_comic_navigation_items', $items, $args ));

    $comic_nav .= sprintf( $args->items_wrap, $items_wrap_attr, $items_str );

    if ($show_container){
        $comic_nav .= "</{$args->container}>";
    }

    if ($echo){
        echo $comic_nav;
    } else {
        return $comic_nav;
    }

}
?>