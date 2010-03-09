<?
/**
 * @package Manga_Press
 * @subpackage Core_Functions
 * @since	0.1b
 * 
 * Manga+Press plugin Functions
 * This is where the actual work gets done...
 * 
*/
/**
 * Updates multiple options from page-comic-options.php
 * 
 * @since 2.6
 *
 * Originally update_options. Was modified and renamed in
 * Manga+Press 2.6
 *
 * @global array $mp_options
 * @param array $options
 * @return string
 */
function update_mangapress_options($options){
global $mp_options;
	
	// validate string options...
	$nav_css_values = array( 'default_css', 'custom_css');
	$order_by_values = array( 'post_date', 'post_id' );
	//
	// if the value of the option doesn't match the correct values in the array, then 
	// the value of the option is set to its default.
	in_array( $mp_options['nav_css'], $nav_css_values ) ? $mp_options['nav_css'] = strval( $options['nav_css'] ) : $mp_options['nav_css'] = 'default_css';
	in_array( $mp_options['order_by'], $order_by_values ) ? $mp_options['order_by'] = strval( $options['order_by'] ) : $mp_options['order_by'] = 'post_date';
	//
	// Converting the values to their correct data-types should be enough for now...
	$mp_options['insert_nav']			=	intval( $options['insert_nav'] );
	$mp_options['latestcomic_cat']		=	intval( $options['latestcomic_cat'] );
	$mp_options['comic_front_page']		=	intval( $options['comic_front_page'] );
	$mp_options['latestcomic_page']		=	intval( $options['latestcomic_page'] );	
	$mp_options['comic_archive_page']	=	intval( $options['comic_archive_page'] );
	$mp_options['make_thumb']			=	intval( $options['make_thumb'] );
	$mp_options['insert_banner']		=	intval( $options['insert_banner'] );
	$mp_options['banner_width']			=	intval( $options['banner_width'] );
	$mp_options['banner_height']		=	intval( $options['banner_height'] );
	$mp_options['twc_code_insert']		=	intval( $options['twc_code_insert'] );
	$mp_options['oc_code_insert']		=	intval( $options['oc_code_insert'] );
	$mp_options['oc_comic_id']			=	intval( $options['oc_comic_id'] );
	
	return serialize( $mp_options );

}
/**
 * mpp_add_comic()
 * 
 * This function adds the comic to the Wordpress database as a post
 * using the Wordpress function wp_insert_page. Was expanded in the
 * beta release of the 2.0 branch to take over the functionality of
 * upload_comic()Used by:	post-new-comic.php
 * 
 * @link http://php.net/manual/en/reserved.variables.files.php $_FILES
 * @link http://php.net/manual/en/reserved.variables.post.php $_POST
 * @since 0.1b
 * 
 * @global array $mp_options
 * @global object $wpdb
 * @global object $wp_rewrite
 * @param array $file Array passed by $_FILES.
 * @param array $post_info Array passed by $_POST
 * @return string 
 */
function mpp_add_comic(&$file, $post_info){
global $mp_options, $wpdb, $wp_rewrite, $add_comic_fired;
	
	check_admin_referer('mp_post-new-comic');
	
	$add_comic_fired = true;
	
	if ($post_info['title'] == '') { return '<strong>Empty Title-field!</strong> Comic not added.'; }
	$now = current_time('mysql'); // let's grab the time...need this for later on...
	
	$comicfile = wp_handle_upload($file['userfile'], false, $now); // use Wordpress's native upload functions...makes more sense
	if (isset( $comicfile['error']) ) {
		return $comicfile['error'];
	} else {		
 	
		// Create a new Comic Post object to pass to wp_insert_post()....
		$newcomic = new WP_ComicPost($post_info, $comicfile);
		
		// this is needed to keep from getting the "Wrong datatype for second argument" error		
		$wp_rewrite->feeds = array( 'feed', 'rdf', 'rss', 'rss2', 'atom' );
		
		$post_id = wp_insert_post($newcomic); // let Wordpress handle the rest
		
		// if wp_insert_post() succeeds, now we add the comic file as an attachment to the post...
		if ($post_id != 0){
			$attach = new WP_ComicPost($post_info, $comicfile, 'attachment');
			$attachID = wp_insert_attachment($attach, $comicfile['file'], $post_id);
			if ($attachID != 0) {
				wp_update_attachment_metadata( $attachID, wp_generate_attachment_metadata( $attachID, $comicfile['file'] ) );
			}
			
			add_post_meta($post_id, 'comic', '1'); // adds required meta data to the post
			$sql	=	$wpdb->prepare("INSERT INTO " . $wpdb->mpcomics . " (post_id, post_date) VALUES ('".$post_id."', '".$newcomic->post_date."') ;");
			$wpdb->query($sql);
			return 'Comic Added!'; // return post_id if it works...if not, return 0
		} else {
			return 'Error! Comic not added...';
		}
	}
}

/**
 * generate_category_checklist()
 *
 * A customized clone of Wordpress function wp_category_checklist()
 * 
 * @since 2.5
 *
 * @param int $post_id
 * @param int $descendants_and_self
 * @param bool $selected_cats
 * @param bool $popular_cats
 * @param object $walker 
 */
function mpp_category_checklist( $post_id = 0, $descendants_and_self = 0, $selected_cats = false, $popular_cats = false, $walker = null ) {
	if ( empty($walker) || !is_a($walker, 'Walker') )
		$walker = new Walker_Category_Checklist;

	$descendants_and_self = (int) $descendants_and_self;

	$args = array();

	if ( is_array( $selected_cats ) )
		$args['selected_cats'] = $selected_cats;
	elseif ( $post_id )
		$args['selected_cats'] = wp_get_post_categories($post_id);
	else
		$args['selected_cats'] = array();

	if ( is_array( $popular_cats ) )
		$args['popular_cats'] = $popular_cats;
	else
		$args['popular_cats'] = get_terms( 'category', array( 'fields' => 'ids', 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => true ) );

	if ( $descendants_and_self ) {
		$categories = get_categories( "child_of=$descendants_and_self&hierarchical=1&hide_empty=0" );
	} else {
		$categories = get_categories('get=all');
	}

	// Post process $categories rather than adding an exclude to the get_terms() query to keep the query the same across all posts (for any query cache)
	$checked_categories = array();
	$keys = array_keys( $categories );

	foreach( $keys as $k ) {
		if ( in_array( $categories[$k]->term_id, $args['selected_cats'] ) ) {
			$checked_categories[] = $categories[$k];
			unset( $categories[$k] );
		}
	}

	// Put checked cats on top
	echo call_user_func_array(array(&$walker, 'walk'), array($checked_categories, 0, $args));
	// Then the rest of them
	echo call_user_func_array(array(&$walker, 'walk'), array($categories, 0, $args));
}
#############################################################
#		Manga+Press plugin Hook Functions					#
#															#
#		These functions are used by add_action to run when	#
#		certain Wordpress functions are called.				#
#															#
#############################################################

/**
 * add_navigation_css()
 * is used to add CSS for comic navigation to <head> section
 * when the custom code option hasn't been specified. Called by: wp_head()
 *
 * @link http://codex.wordpress.org/Hook_Reference/wp_head
 * @since	0.5b
 * 
 */
function mpp_add_nav_css(){
	echo "<!-- Begin Manga+Press Navigation CSS -->\n";
	echo "<style type=\"text/css\">\n";
	echo "\t/* comic navigation */\n";
	echo "\t .comic-navigation { text-align:center; margin: 5px 0 10px 0; }\n";
	echo "\t .comic-nav-span { padding: 3px 10px;	text-decoration: none; }\n";
	echo "\t ul.comic-nav  { margin: 0; padding: 0; white-space: nowrap; }\n";
	echo "\t ul.comic-nav li { display: inline;	list-style-type: none; }\n";
	echo "\t ul.comic-nav a { text-decoration: none; padding: 3px 10px; }\n";
	echo "\t ul.comic-nav a:link, ul.comic-nav a:visited { color: #ccc;	text-decoration: none; }\n";
	echo "\t ul.comic-nav a:hover { text-decoration: none; }\n";
	echo "\t ul.comic-nav li:before{ content: \"\"; }\n";
	echo "</style>\n";
	echo "<!-- End Manga+Press Navigation CSS -->\n";
}
/**
 * add_header_info(). Called by:	wp_head()
 * 
 * @link http://codex.wordpress.org/Hook_Reference/wp_head
 * @since	0.5b
 *
 */
function mpp_add_header_info() {
	echo "<meta name=\"Manga+Press\" content=\"".MP_VERSION."\" />\n";
}
/**
 * add_footer_info(). Called by: 	wp_footer()
 * 
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference/wp_footer
 * @since	1.0 RC1
 * 
 * @global bool $suppress_footer Optional boolean flag for controlling the appearance of the footer info
 */
function mpp_add_footer_info(){
	global $suppress_footer;
	
	if (!$suppress_footer)
		echo "<br />Powered by <a href=\"http://manga-press.silent-shadow.net\">Manga+Press</a> ".MP_VERSION;
}
/**
 * add_meta_info(). Called by:	wp_meta()
 * 
 * @since 1.0 RC1
 * 
 * @global bool $suppress_meta Optional @see $suppress_footer
 */
function mpp_add_meta_info(){
	global $suppress_meta;
	
	if (!$suppress_meta)
		echo "<li><a href=\"http://manga-press.silent-shadow.net\" title=\"Powered by Manga+Press ".MP_VERSION.", a revolutionary new web comic management system for Wordpress\">Manga+Press</a></li>";
}

/**
 * mpp_add_comic_post(). Called by publish_post()
 *
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference publish_post
 * @since 2.5
 * 
 * @global array $mp_options
 * @global object $wpdb
 * @param int $id
 */
function mpp_add_comic_post($id) {
	global $mp_options, $wpdb, $add_comic_fired;
	
	$cats = wp_get_post_categories($id);
	
	if (!$add_comic_fired) {
		if ( in_array($mp_options['latestcomic_cat'], $cats) ) {
			
			if ( !(bool)get_post_meta($id, 'comic') ) {
				add_post_meta($id, 'comic', '1');
				$post = get_post( $id );
				$sql	=	$wpdb->prepare("INSERT INTO " . $wpdb->mpcomics . " (post_id, post_date) VALUES ('".$id."', '".$post->post_date."') ;");
				$wpdb->query($sql);
			}
		}
	}
	
	$add_comic_fired = false;
	return;
	
}
/**
 * delete_comic_post()
 * is used to delete comic from the comics DB table
 * when comic is deleted via Manage Posts or Edit Post
 *
 * @since	0.1b
 * @global object $wpdb Wordpress database object.
 * @param int $post_id Integer of post to be added to comics database.
 * @see	delete_post()
 * 
 */
function mpp_delete_comic_post($post_id){
global $wpdb;

	$sql	=	$wpdb->prepare("DELETE FROM ".$wpdb->mpcomics." WHERE post_id=".$post_id.";");
	$wpdb->query($sql);
}
/**
 * edit_comic_post(). Called by edit_post()
 *
 * @link http://codex.wordpress.org/Plugin_API/Action_Reference edit_post
 * @since 2.6
 * 
 * @global array $mp_options
 * @global object $wpdb
 * @param int $id
 */
function mpp_edit_comic_post($id) {
	global $mp_options, $wpdb;
	
	$cats = wp_get_post_categories($id);
	$value = (int)get_post_meta($id, 'comic', true);
	//
	// post has been edited, comic removed from comic categories...	
	if ( !in_array($mp_options['latestcomic_cat'], $cats) && $value ) {
		$sql	=	$wpdb->prepare("DELETE FROM ".$wpdb->mpcomics." WHERE post_id=".$id.";");
		$wpdb->query($sql);
		delete_post_meta( $id, 'comic' );
		return;
	} elseif ( in_array($mp_options['latestcomic_cat'], $cats) && $value ) {
		if ( !is_comic($id) ) { // has meta value but if its not in the database, then add it
			$post = get_post($id);
			$sql	=	$wpdb->prepare("INSERT INTO " . $wpdb->mpcomics . " (post_id, post_date) VALUES ('".$id."', '".$post->post_date."') ;");
			$wpdb->query($sql);
			return;
		}
	}
}
/**
 * filter_posts_frontpage()
 *
 * Filters comic posts from front page. Hooked to wp().
 * 
 * @since 2.5
 * 
 * @global object $wpdb Wordpress database object. Not used.
 * @global int $id Post/Page id. Not used.
 * @global int $cat Category id. Not used.
 * @global int $post Post/Page object. Used in place of $id.
 * @global array $mp_options Array containing Manga+Press options.
 */
function mpp_filter_posts_frontpage() {
	global $wpdb, $id, $cat, $page, $post, $mp_options, $query_string;

	if (is_home() && $mp_options['comic_front_page'] ) {
		query_posts( $query_string."&cat=-".$mp_options['latestcomic_cat'] );
	}
}
/**
 * filter_latest_comicpage()
 *
 * Makes changes to the_content() for Latest Comic Page. Hooked to the_content().
 * 
 * @since 2.5
 * 
 * @global object $wp Global WordPress query object.
 * @global array $mp_options Array containing Manga+Press options.
 */
function mpp_filter_latest_comicpage($content) {
	global $mp_options, $wp;
	
	$page = get_page( $mp_options['latestcomic_page'] );
	
	if ( get_option('show_on_front') == 'page' && is_front_page() ) {
		$front_page_id = get_option('page_on_front');
		$front_page = get_page( $front_page_id );
		$comic_page = $front_page->post_name;
	} else {
		$comic_page = @$wp->query_vars['pagename'];
	}
	
	if ( $comic_page === $page->post_name ) {
		$start = '';
		$end = '';
		$nav = '';
		$ptitle = '';
		$twc_code = '';
		//
		// Now grab the most recent comic ID...
		$latest = wp_comic_last();
		//
		// ...and its navigation...
		$nav = wp_comic_navigation( $latest, false, false);
		//
		// ...and its post content, and set it up...
		$post = get_post( $latest );
		setup_postdata( $post );
		$ptitle = '<h2 class="comic-title">'.$post->post_title.'</h2>';
		//
		// If OnlineComics PageScan code is enabled...
		if ($mp_options['oc_code_insert']) {
			$start = "\n<!-- OnlineComics.net ".$mp_options['oc_comic_id']." start -->\n";
			$end = "\n<!-- OnlineComics.net ".$mp_options['oc_comic_id']." end -->\n";
		}
		//
		// If TWC.com update code is enabled...
		if ($mp_options['twc_code_insert']) {
			$twc_code = "\n<!--Last Update: ".date('d/m/Y', strtotime($post->post_date))."-->\n";
		}
		
		$content = $twc_code.$start.$ptitle.$nav.$post->post_content.$end;
	}
		
	return $content;
}
/**
 * filter_comic_archivepage()
 *
 * Makes changes to the_content() for Comic Archive Page. Hooked to the_content().
 * 
 * @since 2.6
 * 
 * @global object $wp Global WordPress query object.
 * @global array $mp_options Array containing Manga+Press options.
 */
function mpp_filter_comic_archivepage($content){
	global $mp_options, $wp;
	
	$page = get_page( $mp_options['comic_archive_page'] );
	if ( @$wp->query_vars['pagename'] === $page->post_name ) {
		$parchives = '';
		if ($mp_options['twc_code']) {
			$recent_post = get_post( wp_comic_last() );
			setuppost_date( $recent_post );
			
			$parchives = "\n<!--Last Update: ".date('d/m/Y', strtotime($recent_post->post_date))."-->\n";
		}
		//
		// Grab all available comic posts...
		// Yes, this is sort of a "mini Loop"
		$args = array( 'showposts'=>'10', 'cat'=>wp_comic_category_id(), 'orderby'=>'post_date' );
		$posts = get_posts( $args );
		if ( have_comics() ) :
			
			$parchives .= "<ul class=\"comic-archive-list\">\n";
			
			$c = 0;
			foreach( $posts as $post) :	setup_postdata( $post );
				
				$c++;
				$parchives .= "\t<li class=\"list-item-$c\">".date('m-d-Y', strtotime( $post->post_date ) )." <a href=\"".get_permalink( $post->ID )."\">$post->post_title</a></li>\n";
			
			endforeach;
			
			$parchives .= "</ul>\n";

		else:
			
			$parchives = "No comics found";
			
		endif;
		$content = $parchives;
	}
		
	return $content;
	
}
/**
 * comic_insert_navigation()
 *
 * Inserts comic navigation at the beginning of The Loop. Hooked to loop_start
 * 
 * @since 2.5
 * 
 * @global object $post Wordpress post object.
 * @global int $id Post ID. Not used.
 * @global int $cat Category ID. Not used.
 * @global array $mp_options Array containing Manga+Press options. 
 */
function mpp_comic_insert_navigation() {
	global $post, $id, $cat, $mp_options;
	
	if ( is_comic() && !is_category() && !is_front_page() && !is_archive() ) {	
		wp_comic_navigation($post->ID);
	}
	
}

/**
 * comic_insert_banner()
 *
 * Inserts comic banner at the start of The Loop on the home page.
 * Hooked to loop_start.
 *
 * @since 2.5
 */
function mpp_comic_insert_banner() {
	if ( is_home() || is_front_page() ){
		get_latest_comic_banner(true);
	}
}

/**
 * comic_insert_twc_update_code()
 *
 * Inserts a Last Update html comment at the start of The Loop on the either
 * the home page, the main comic page or the archive page. Hooked to loop_start.
 *
 * @since 2.5
 * @version 1.0
 */
function mpp_comic_insert_twc_update_code() {
	if ( is_home() || is_comic_archive_page() ){
		$latest = wp_comic_last();
		$post_latest = get_post($latest);
		echo "\n<!--Last Update: ".date('d/m/Y', strtotime($post_latest->post_date))."-->\n";
	}
}
/**
 * mpp_comic_version()
 *
 * @since 2.0 beta
 *
 * echoes the current version of Manga+Press.
 */
function mpp_comic_version() {
	
	echo MP_VERSION;	
}
?>