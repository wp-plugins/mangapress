<?
/**
* This file contains all of the custom template tags for
* displaying comics and navigation properly.
**/

/**
* This section handles the display of the comic navigation.
*
* The code for the comic navigation is based on
* the MyComic Wordpress plugin found at
* http://borkweb.com/story/wordpress-plugin-mycomic-browser
* Manga+Press follows the same philosophy but automatically 
* adds the required meta-data for the plugin to work properly
**/
##############################################################
/** 
* have_comics()
*
* Like have_posts(), returns true if there's comics;
* false if there isn't any...
*
* @since:		1.0 RC1
* @modified:	--- 
**/
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
* @changed:		wp_is_comic($id)
* @since:		0.1b
* @modified:	1.0 RC1 
**/
function is_comic(){
global $wpdb, $mp_options, $id;

	$sql = $wpdb->prepare("SELECT id FROM " . $wpdb->mpcomics . " WHERE post_id=$id;");
	return (bool)$wpdb->get_col($sql);
}
/** 
* is_comic_page()
*
* Custom Conditional Template Tag
*
* @added:		1.0 RC1
* @modified:	-- 
**/
function is_comic_page(){
global $mp_options, $id;

	return (bool)($id == $mp_options[latestcomic_page]);
}
/** 
* is_comic_archive_page()
*
* Custom Conditional Template Tag
*
* @added:		1.0 RC1
* @modified:	-- 
**/
function is_comic_archive_page(){
global $mp_options, $id;

	return (bool)($id == $mp_options[comic_archive_page]);
}
/** 
* is_series_cat()
*
* Custom Conditional Template Tag
* Checks to see if category is a series category
*
* @added:		1.0 RC1
* @modified:	-- 
**/
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
* @added:		1.0 RC1
* @modified:	-- 
**/
function is_comic_cat() {
global $cat, $mp_options;
	$id = (int)$cat;
	
	return  (bool)($id == $mp_options[latestcomic_cat]);
}
/** 
* the_series()
*
* Custom template tag
*
* @since:		---
* @modified:	--- 
**/
function the_series() { }
/** 
* get_comic_post()
*
*
* @since:		1.0 RC1
* @modified:	---
**/
function get_comic_post($id) {
global $comic_page, $post_date, $post_content, $post_title, $post_excerpt;

	$comic_page = get_post($id, ARRAY_A);

	extract ( $comic_page );
	//debug ( $comic_page );
	setup_postdata ($comic_page);
	rewind_posts();
	
	return $comic_page;
}
/** 
* get_comic_series()
*
* Custom template tag
*
* @since:		---
* @modified:	--- 
**/
function get_comic_series($id) { }
/** 
* get_comic_feed()
*
* handles the trouble of getting a link
* to the Latest Comic category feed.
*	
* Defaults to rss2 when no parameters are specified
*  
* @since:		1.0 RC1
* @modified:	-- 
**/
function get_comic_feed($feed = 'rss2') {
global $mp_options;

	return get_category_feed_link($mp_options[latestcomic_cat], $feed);
}
/** 
* get_comic_feed()
*
* handles the trouble of getting a link
* to the Latest Comic category feed.
*	
* Defaults to rss2 when no parameters are specified
*  
* @since:		1.0 RC1
* @modified:	-- 
**/
function get_series_feed($series, $feed = 'rss2') {

	return get_category_feed_link($series, $feed);
	
}
/** 
* wp_comic_first()
*
* Retrieves the first comic posted.
*
* @called by:	wp_comic_navigation()
*
* @since:		0.1b
* @modified:	0.5b 
**/
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
* @called by:	wp_comic_navigation()
*
* @since:		0.1b
* @modified:	0.5b 
**/
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
* @called by:	--
*
* @since:		0.1b
* @modified:	1.0 RC1 
**/
function wp_comic_navigation($post_id) {
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
* @called by:	wp_comic_navigation()
*
* @since:		0.1b
* @modified:	0.5b 
**/
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
* @called by:	wp_comic_navigation()
*
* @since:		0.1b
* @modified:	0.5b 
**/
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
* @since:		1.0 RC1
* @modified:	-- 
**/
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
*  
* @since:		1.0 RC1
* @modified:	-- 
**/
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
*  
* @since:		1.0 RC1
* @modified:	-- 
**/
function wp_comic_archive_page_id() {
global $mp_options;

	return $mp_options[comic_archive_page];
}
?>