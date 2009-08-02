<?
/**
 * This file contains all of the custom template tags for
 * displaying comics and navigation properly.
 * 
 * @package Manga_Press
 * @subpackage Manga_Press_Template_Functions
 * @since 0.1
 * 
 */

/**
* This section handles the display of the comic navigation.
*
* The code for the comic navigation is based on
* the MyComic Wordpress plugin found at
* http://borkweb.com/story/wordpress-plugin-mycomic-browser
* Manga+Press follows the same philosophy but automatically 
* adds the required meta-data for the plugin to work properly
**/

/**
 * have_comics()
 * 
 * Like have_posts(), returns true if there's comics;
 * false if there isn't any...
 * 
 * @since 1.0 RC1
 * 
 * @global object $wpdb
 * @global array $mp_options
 * @return bool 
 */
function have_comics(){
global $wpdb, $mp_options;

	$sql = $wpdb->prepare("SELECT * FROM " . $wpdb->mpcomics.";");
	return (bool)$wpdb->get_col($sql);
}

/**
 * is_comic()
 *
 * Custom Conditional Template Tag
 * generally used in the single.php template file
 * to check if article is actually a comic, and if
 * so, display the navigation
 *
 * @since 0.1
 *
 * @global object $wpdb
 * @global array $mp_options
 * @global object $post
 * @return bool
 */
function is_comic(){
global $wpdb, $mp_options, $post;
	
	$sql = $wpdb->prepare("SELECT $post->ID FROM " . $wpdb->mpcomics . " WHERE post_id=$post->ID;");
	return (bool)$wpdb->get_col($sql);
}
/** 
* is_comic_page()
*
* Custom Conditional Template Tag
*
* @since 1.0 RC1
*
* @global array $mp_options
* @global object $post
* @return bool
*/
function is_comic_page(){
global $mp_options, $wp_query;

	//debug( $wp_query );
	//$wp_query->post->ID == $mp_options[latestcomic_page]
	if ( is_page( $mp_options[latestcomic_page] ) ) { return true; }
	else { return false; }	
}
/** 
* is_comic_archive_page()
*
* Custom Conditional Template Tag
*
* @since 1.0 RC1
*
* @global array $mp_options
* @global object $post
* @return bool
*/
function is_comic_archive_page(){
global $mp_options, $post;

	if ($post->ID == $mp_options[comic_archive_page]) { return true; }
	else { return false; }
}
/** 
* is_series_cat()
*
* Custom Conditional Template Tag
* Checks to see if category is a series category
*
* @since 1.0 RC1
*
* @global object $wpdb
* @global integer $cat
* @return bool
*/
function is_series_cat() {
global $wpdb, $cat;
	$cat = (int)$cat;
	
	$sql = $wpdb->prepare("SELECT term_id FROM ".$wpdb->mpcomicseries." WHERE term_id=".$cat.";");
	return (bool)$wpdb->query($sql);
}
/** 
* is_comic_cat()
*
* Custom Conditional Template Tag
* Checks to see if category is a series category
*
* @since 1.0 RC1
*
* @global integer $cat
* @global array $mp_options
* @global object $wp_query
* @return bool
*/
function is_comic_cat() {
global $cat, $mp_options, $wp_query;

	if ($wp_query->is_category) {
		$cat_obj = $wp_query->get_queried_object();  
		return  (bool)($cat_obj->term_id == $mp_options[latestcomic_cat]);
	} else {
		return false;
	}
}
/** 
* the_comic()
*
* Custom template tag
*
* @since 1.0 RC1
*
* @global string $post_content
*/
function the_comic(){
	global $post_content;
	
	echo $post_content;
}
/** 
* the_banner()
*
* Custom template tag
*
* @since 2.0 beta
*
* @global string $post_excerpt
*/
function the_banner() {
	global $post_excerpt;
	
	echo $post_excerpt;
}
/** 
* get_comic_post()
*
*
* @since 1.0 RC1
*
* @global object $comic_page
* @global datetime $post_date
* @global string $post_content
* @global string $post_title
* @global string $post_excerpt
* @param int $id
* @return object
*/
function get_comic_post($id) {
global $comic_page, $post_date, $post_content, $post_title, $post_excerpt;

	$comic_page = get_post($id, OBJECT);

	extract ( get_object_vars( $comic_page ) );

	return get_object_vars( $comic_page );
}
/** 
* get_latest_comic_banner()
*
* Custom template tag
*
* @since 2.1
*
* @global objecy $wpdb
* @global array $mp_options
* @global string $post_excerpt
* @param bool $nav. Whether or not to display comic navigation below banner
*/
function get_latest_comic_banner($nav = false) {
	global $wpdb, $mp_options, $post_excerpt;

	$latest = wp_comic_last();

	if ((int)$latest) {
		$child = &get_posts( array( 'post_parent'=>$latest, 'post_type'=>'attachment', 'post_mime_type'=>'image', 'numberposts'=>1 ) );
		$image = wp_get_attachment_image_src( $child[0]->ID, 'full' );
		get_comic_post ( $latest );
?>
<div class="comic-banner">
	<h1><a href="<?php echo get_permalink( $latest )?>" title="<?php echo get_the_title( $latest )?>" class="new"><?php echo get_the_title( $latest )?></a></h1>
	<span class="comic-banner-wrap">
        <img src="<?php bloginfo( 'url' ); ?>/wp-content/plugins/mangapress/timthumb.php?src=<?=$image[0]?>&amp;w=<?=$mp_options[banner_width]?>&amp;h=<?=$mp_options[banner_height]?>&amp;zc=1" class="comic-banner-image" />
        <span class="comic-banner-overlay">&nbsp;</span>
	</span>
<? if ($nav) { wp_comic_navigation( $latest ); } ?>
</div>
<?
	}
}
/** 
 * get_comic_feed()
 *
 * handles the trouble of getting a link to the Latest Comic category feed.
 * @link http://codex.wordpress.org/Function_Reference/get_category_feed_link More information
 *	
 * @since 1.0 RC1
 *
 * @global array $mp_options
 * @param string $feed Type of feed. Defaults to rss2 
 * @return string Returns link to category
 */
function get_comic_feed($feed = 'rss2') {
global $mp_options;

	return get_category_feed_link($mp_options[latestcomic_cat], $feed);
}
/** 
 * get_comic_feed()
 *
 * handles the trouble of getting a link to the series category feed.
 * @link http://codex.wordpress.org/Function_Reference/get_category_feed_link More information
 *  
 * @since 1.0 RC1
 *
 * @param int $series ID of the series category.
 * @param string $feed Type of feed. Defaults to rss2
 * @return string Returns link to category.
 */
function get_series_feed($series, $feed = 'rss2') {

	return get_category_feed_link($series, $feed);
	
}
/** 
 * wp_comic_first()
 *
 * Retrieves the first comic posted.
 *
 * @since 0.1b
 *
 * @global object $wpdb
 * @global array $mp_options
 * @return int Returns post_ID on success. 0 on failure.
 */
function wp_comic_first(){
global $wpdb, $mp_options;
	
	$mp_options[orderby] = ($mp_options[order_by])?$mp_options[order_by]:'post_id';
	$sql = $wpdb->prepare("SELECT post_id FROM " . $wpdb->mpcomics . " ORDER BY ".$mp_options[order_by]." ASC LIMIT 1;");
	$rows = $wpdb->get_results($sql);

	if(count($rows)) {
		return $rows[0]->post_id;
	} else {
		return 0;
	}

}
/** 
 * wp_comic_last()
 *
 * Retrieves the last (most recent) comic posted.
 *
 * @since 0.1b
 *
 * @global object $wpdb
 * @global array $mp_options
 * @return int Returns post_ID on success. 0 on failure.
 */
function wp_comic_last(){
global $wpdb, $mp_options;
	
	$mp_options[order_by] = ($mp_options[order_by])?$mp_options[order_by]:'post_id';
	$sql = $wpdb->prepare("SELECT post_id FROM " . $wpdb->mpcomics . " ORDER BY ".$mp_options[order_by]." DESC LIMIT 1;");
	$rows = $wpdb->get_results($sql);

	if(count($rows)) {
		return $rows[0]->post_id;
	} else {
		return 0;
	}

}
/** 
 * wp_comic_navigation()
 *
 * Template Tag
 *
 * @since 0.1b
 *
 * @global object $wpdb
 * @param int $post_id ID of the comic post.
 * @param bool $banner_nav Not used.
 */
function wp_comic_navigation($post_id, $banner_nav = false) {
global $wpdb; 
	
	$first = wp_comic_first();
	$first = ($first == $post_id || !$first)?'<span class="comic-nav-span">First</span>':'<a href="'.get_permalink($first).'">First</a>';
	
	$last = wp_comic_last();
	$last = ($last == $post_id || !$last)?'<span class="comic-nav-span">Last</span>':'<a href="'.get_permalink($last).'">Last</a>';
	
	$next = wp_comic_next($post_id);
	$next = ($next == $post_id || !$next)?'<span class="comic-nav-span">Next</span>':'<a href="'.get_permalink($next).'">Next</a>';
	
	$previous = wp_comic_previous($post_id);
	$previous = ($previous==$post_id || !$previous)?'<span class="comic-nav-span">Previous</span>':'<a href="'.get_permalink($previous).'">Previous</a>';

	$navigation='
		<div class="comic-navigation">
			<ul class="comic-nav">
				<li class="comic-nav-first">'.$first.'</li>
				<li class="comic-nav-prev">'.$previous.'</li>
				<li class="comic-nav-next">'.$next.'</li>
				<li class="comic-nav-last">'.$last.'</li>
			</ul>
		</div>
	';

	echo $navigation;
}
/** 
 * wp_comic_next()
 *
 * Retrieves the next comic from the
 * current comic $post_id
 *
 * @since 0.1b
 *
 * @global object $wpdb
 * @global array $mp_options
 * @param int $post_id
 * @return int Returns post_ID on success. 0 on failure.
 */
function wp_comic_next($post_id) {
global $wpdb, $mp_options;

	$mp_options[order_by] = ($mp_options[order_by])?$mp_options[order_by]:'post_id';
	$sql = $wpdb->prepare("SELECT post_id FROM " . $wpdb->mpcomics . " WHERE post_id>$post_id ORDER BY ".$mp_options[order_by]." ASC LIMIT 1;");
	$rows = $wpdb->get_results($sql);

	if(count($rows)) {
		return $rows[0]->post_id;
	} else {
		return 0;
	}
}
/** 
 * wp_comic_previous()
 *
 * Retrieves the previous comic from the
 * current comic $post_id
 *
 * @since 0.1b
 *
 * @global object $wpdb
 * @global array $mp_options
 * @param int $post_id
 * @return int Returns post_ID on success. 0 on failure.
 */
function wp_comic_previous($post_id) {
global $wpdb, $mp_options;

	$mp_options[order_by] = ($mp_options[order_by])?$mp_options[order_by]:'post_id';
	$sql = $wpdb->prepare("SELECT post_id FROM " . $wpdb->mpcomics . " WHERE post_id<$post_id ORDER BY ".$mp_options[order_by]." DESC LIMIT 1;");
	$rows = $wpdb->get_results($sql);

	if(count($rows)) {
		return $rows[0]->post_id;
	} else {
		return 0;
	}
}
/** 
 * wp_comic_category_id()
 *
 * Returns the value of $mp_options[latestcomic_cat]
 * for use in templates
 *
 *
 * @since 1.0 RC1
 * @deprecated
 *
 * @global array $mp_options
 * @return int ID of Latest Comic catergory
 */
function wp_comic_category_id() {
global $mp_options;

	return $mp_options[latestcomic_cat];
}
/** 
 * wp_comic_page_id()
 *
 * Returns the value of $mp_options[latestcomic_page]
 * for use in templates
 *
 * @since 1.0 RC1
 * @deprecated
 *
 * @global array $mp_options
 * @return int ID of Latest Comic page
 */
function wp_comic_page_id() {
global $mp_options;

	return $mp_options[latestcomic_page];
}
/** 
 * wp_comic_archive_page_id()
 *
 * Returns the value of $mp_options[comic_archive_page]
 * for use in templates
 *
 * @since 1.0 RC1
 * @deprecated
 *
 * @global array $mp_options
 * @return int ID of Comic Archives page
 */
function wp_comic_archive_page_id() {
global $mp_options;

	return $mp_options[comic_archive_page];
}
/**
 * wp_sidebar_comic()
 *
 * displays a recent comic thumbnail in the sidebar
 *
 * @since 2.0
 */
function wp_sidebar_comic() {
	
	$ID = wp_comic_last();
	if ($ID) {
		$images =& get_children( 'post_type=attachment&post_mime_type=image&post_parent=' . $ID );
		foreach( $images as $imageID => $imagePost )
			$image = wp_get_attachment_metadata( $imageID );
			$imgurl = wp_get_attachment_thumb_url( $imagePost->ID );
			$res = getimagesize( $imgurl );
			echo '<div class="comic-sidebar"><a href="'.get_permalink( $ID ).'" title="Latest Comic"><img src="'.$imgurl.'"'.$res[3].' style="border: none; " /></a></div>';
			echo '<div class="comic-sidebar-link"><a href="'.get_permalink( $ID ).'" title="Latest Comic">Latest Comic</a></div>';
	}
}
?>