<?
/**
 * Manga+Press plugin Functions
 * This is where the actual work gets done...
 * 
 * @package Manga_Press
 * @subpackage Core_Functions
 * @since	0.1b
 * 
*/
/**
 * Updates multiple options from page-comic-options.php
 * 
 * @since 0.1b
 *
 * @global array $mp_options
 * @param array $options
 * @return int $status
 */
function update_options($options){
global $mp_options;

	check_admin_referer('mp_basic-options-form');
	
	extract( $options ); // so we can get $action
		
	switch($action):
	
		case 'update_options':
			$error['Latest Comic Category']	= $status = (bool)update_option('comic_latest_default_category',	$options[latest],		'',	'yes');
			$error['Order By']				= $status = (bool)update_option('comic_order_by', 				$options[order_by],		'',	'yes');
			$error['Latest Comic Page']		= $status = (bool)update_option('comic_latest_page',			$options[latest_page],	'',	'yes');
			$error['Comic Archive Page']	= $status = (bool)update_option('comic_archive_page',			$options[archive_page],	'',	'yes');
			$error['Use CSS']				= $status = (bool)update_option('comic_use_default_css',		$options[nav_css],		'',	'yes');
			$error['Exclude from Home Page']= $status = (bool)update_option('comic_front_page',				$options[exclude_comic_cat],'',	'yes');
			$error['Insert Comic Nav']		= $status = (bool)update_option('insert_nav',					$options[insert_nav],	'',	'yes');
			$mp_options[latestcomic_cat]	=	get_option('comic_latest_default_category');
			$mp_options[order_by]			=	get_option('comic_order_by');
			$mp_options[latestcomic_page]	=	get_option('comic_latest_page');
			$mp_options[comic_archive_page]	=	get_option('comic_archive_page');
			$mp_options[nav_css]			=	get_option('comic_use_default_css');
			$mp_options[comic_front_page]	=	get_option('comic_front_page');
			$mp_options[insert_nav]			=	get_option('insert_nav');
			$status = in_array(true, $error);
		break;

		case 'set_image_options':
		
			$error['Make Thumbnails']	= $status = update_option('comic_make_thmb', 	$options[make_thumb],	'',	'yes');
			$error['Banner Width']		= $status = update_option('banner_width',	$options[banner_width],		'',	'yes');
			$error['Banner Height'] 	= $status = update_option('banner_height',	$options[banner_height],	'',	'yes');
			$error['Insert Banner'] 	= $status = update_option('insert_banner',	$options[insert_banner],'',	'yes');
			$mp_options[make_thumb]			=	(bool)get_option('comic_make_thmb');	
			$mp_options[banner_width]		=	(int)get_option('banner_width');
			$mp_options[banner_height]		=	(int)get_option('banner_height');
			$mp_options[insert_banner]		=	get_option('insert_banner');
			$status = in_array(true, $error);
			
		break;
		
		case 'set_comic_updates':
		
			$error['Insert TWC code']		= $status = (bool)update_option('twc_code_insert',				$options[enable_twc_date_code],		'',	'yes');
			$error['Insert OC.net code']	= $status = (bool)update_option('oc_code_insert',				$options[enable_onlinecomics_code],	'',	'yes');
			$error['OC.net Comic ID']		= $status = (bool)update_option('oc_comic_id',					$options[ocn_comic_ID],		'',	'yes');
			$mp_options[twc_code_insert]	=	get_option('twc_code_insert');
			$mp_options[oc_code_insert]		=	get_option('oc_code_insert');
			$mp_options[oc_comic_id]		=	get_option('oc_comic_id');
			
			$status = in_array(true, $error);
		break;
		
		default:
		break;
	
	endswitch;

	return (int)$status;

}
/**
 * add_comic()
 * 
 * This function adds the comic to the Wordpress database as a post
 * using the Wordpress function wp_insert_page. Was expanded in the
 * beta release of the 2.0 branch to take over the functionality of
 * upload_comic(.)Used by:	post-new-comic.php
 * 
 * @since 0.1b
 * 
 * @global array $mp_options
 * @global object $wpdb
 * @global object $wp_rewrite
 * @param array $file Array passed by $_FILES.
 * @param array $post_info Array passed by $_POST
 * @return string 
 */
function add_comic(&$file, $post_info){
global $mp_options, $wpdb, $wp_rewrite, $add_comic_fired;
	
	check_admin_referer('mp_post-new-comic');
	
	$add_comic_fired = true;
	
	if ($post_info[title] == '') { return '<strong>Empty Title-field!</strong> Comic not added.'; }
	$now = current_time('mysql'); // let's grab the time...need this for later on...
	
	$comicfile = wp_handle_upload($file[userfile], false, $now); // use Wordpress's native upload functions...makes more sense
	$error = $comicfile[error];
	
	// let's check for errors.
	if ($error != '') {
		return $error;
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
			return 'Comic Added!'.$msg; // return post_id if it works...if not, return 0
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
function generate_category_checklist( $post_id = 0, $descendants_and_self = 0, $selected_cats = false, $popular_cats = false, $walker = null ) {
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
		//$self = get_category( $descendants_and_self );
		//array_unshift( $categories, $self );
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
function add_navigation_css(){
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
function add_header_info() {
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
function add_footer_info(){
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
function add_meta_info(){
	global $suppress_meta;
	
	if (!$suppress_meta)
		echo "<li><a href=\"http://manga-press.silent-shadow.net\" title=\"Powered by Manga+Press ".MP_VERSION.", a revolutionary new web comic management system for Wordpress\">Manga+Press</a></li>";
}

/**
 * add_comic(). Called by publish_post()
 *
 * @since 2.5
 * 
 * @global array $mp_options
 * @global object $wpdb
 * @param int $id
 */
function add_comic_post($id) {
	global $mp_options, $wpdb, $add_comic_fired;
	
	//$post = get_post($id);
	$cats = wp_get_post_categories($id);
	//add_post_meta($id, 'debug', '1');
	
	if (!$add_comic_fired) {
		if ( in_array($mp_options[latestcomic_cat], $cats) ) {
			
			if ( !(bool)get_post_meta($id, 'comic') ) {
				//add_post_meta($id, 'debug', '2');
				add_post_meta($id, 'comic', '1');
				$sql	=	$wpdb->prepare("INSERT INTO " . $wpdb->mpcomics . " (post_id, post_date) VALUES ('".$id."', '".current_time('mysql')."') ;");
				$wpdb->query($sql);
			}
		}
	}
	
	$add_comic_fired = false;
	return;
	
}
/**
 * delete_comic()
 * is used to delete comic from the comics DB table
 * when comic is deleted via Manage Posts or Edit Post
 *
 * @since	0.1b
 * @global object $wpdb Wordpress database object.
 * @param int $post_id Integer of post to be added to comics database.
 * @see	delete_post()
 * 
 */
function delete_comic($post_id){
global $wpdb;

	$sql	=	$wpdb->prepare("DELETE FROM ".$wpdb->mpcomics." WHERE post_id=".$post_id.";");
	$wpdb->query($sql);
}
/**
 * add_series()
 * is used to add category to the series table
 * 
 * @since		1.0 RC1
 * @global object $wpdb Wordpress database object.
 * @global array $mp_options Array containing options for Manga+Press.
 * @param int $cat_ID Integer of category to be added to series database.
 * @see	create_category()
 * 
 */
function add_series($cat_ID) {
global $wpdb, $mp_options;

	$cat = get_category($cat_ID);
	if ($cat->category_parent == $mp_options[latestcomic_cat]) {

		$sql	=	$wpdb->prepare("INSERT INTO ".$wpdb->mpcomicseries."(term_id) VALUES ('".$cat_ID."');");
		$wpdb->query($sql);

	}
}
/**
 * delete_series()
 * is used to delete category from the series DB table
 * when category is deleted via Manage Categories
 * 
 * @since 1.0 RC1
 * @see	delete_category()
 */
function delete_series($cat_ID) {
global $wpdb;

	$sql	=	$wpdb->prepare("DELETE FROM ".$wpdb->mpcomicseries." WHERE term_id=".$cat_ID.";");
	$wpdb->query($sql);
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
function filter_posts_frontpage() {
	global $wpdb, $id, $cat, $page, $post, $mp_options, $query_string;

	if (is_home() && $mp_options[comic_front_page] ) {
		query_posts( $query_string."&cat=-".$mp_options[latestcomic_cat] );
	}
}
/**
 * filter_latest_comicpage()
 *
 * Makes changes to the_content() for Latest Comic Page. Hooked to the_post().
 * 
 * @since 2.5
 * 
 * @global object $wpdb Wordpress database object. Not used.
 * @global int $id Post/Page id. Not used.
 * @global int $cat Category id. Not used.
 * @global int $post Post/Page object. Used in place of $id.
 * @global array $mp_options Array containing Manga+Press options.
 */
function filter_latest_comicpage() {
	global $wpdb, $id, $cat, $post, $mp_options;
	
	if ( $post->ID == (int)$mp_options[latestcomic_page] ) {
		$latest = wp_comic_last();
		$post = get_post( $latest );
		setup_postdata( $post );
	}
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
function comic_insert_navigation() {
	global $post, $id, $cat, $mp_options;
	
	if ( is_comic() && !is_category() && !is_front_page() ) {	
		wp_comic_navigation($post->ID);
	}elseif ( is_comic_page() ) {
		$latest = wp_comic_last();
		wp_comic_navigation($latest);
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
function comic_insert_banner() {
	if ( is_home() ){
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
function comic_insert_twc_update_code() {
	if ( is_comic_page() || is_home() || is_comic_archive_page() ){
		$latest = wp_comic_last();
		$post_latest = get_post($latest);
		echo "\n<!--Last Update: ".date('d/m/Y', strtotime($post_latest->post_date))."-->\n";
	}
}
/**
 * comic_insert_oc_update_code()
 *
 * Inserts PageScan code for OnlineComics.net
 *
 * @global array $mp_options
 * @global object $wp
 * @param string $content
 */
function comic_insert_oc_update_code($content) {
	global $mp_options, $wp;
	
	$page = get_page( $mp_options[latestcomic_page] );
	if ( $wp->query_vars[pagename] === $page->post_name ) {
		$start = "\n<!-- OnlineComics.net ".$mp_options[oc_comic_id]." start -->\n";
		$end = "\n<!-- OnlineComics.net ".$mp_options[oc_comic_id]." end -->\n";
	}
		
	$ret = $start.$content.$end;
	return $ret;
}

?>