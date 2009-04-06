<?
/**		Manga+Press plugin Functions
		This is where the actual work gets done...
		
		@since:		0.1b
		@modified:	2.0 beta

*/
/*
	update_options()

	returns true or a number based on error
	@Used by:	page-comic-options.php
	@since:		0.1b
	@modified:	2.0 beta
*/
function update_options($options, $file = ''){
global $mp_options;
	
	extract( $options ); // so we can get $action
	
	switch($action):
	
		case 'update_options':
			$status = 1;
			update_option('comic_latest_default_category',	$options[latest],		'',	'yes');
			update_option('comic_order_by', 				$options[order_by],		'',	'yes');
			update_option('comic_latest_page',				$options[latest_page],	'',	'yes');
			update_option('comic_archive_page',				$options[archive_page],	'',	'yes');
			update_option('comic_use_default_css',			$options[nav_css],		'',	'yes');
			
			$mp_options[latestcomic_cat]	=	get_option('comic_latest_default_category');
			$mp_options[order_by]			=	get_option('comic_order_by');
			$mp_options[latestcomic_page]	=	get_option('comic_latest_page');
			$mp_options[comic_archive_page]	=	get_option('comic_archive_page');
			$mp_options[nav_css]			=	get_option('comic_use_default_css');
		break;

		case 'set_image_options':
		
			$status = 1;
			if ($mp_options[use_overlay]) {

				if ($file[overlay_image][name] != '') {
					$status	=	upload_overlay_image($file);
				}
			}
			update_option('comic_use_overlay',	$options[use_overlay],	'', 'yes');
			update_option('comic_make_banner',	$options[make_banner],	'',	'yes');
			update_option('comic_make_thmb', 	$options[make_thumb],	'',	'yes');
			$mp_options[use_overlay]		=	(bool)get_option('comic_use_overlay');
			$mp_options[make_banner]		=	(bool)get_option('comic_make_banner');
			$mp_options[make_thumb]			=	(bool)get_option('comic_make_thmb');
		break;

		case 'set_image_dimensions':
			
			update_option('banner_width',	$options[banner_width],	'',	'yes');
			update_option('banner_height',	$options[banner_height],	'',	'yes');
			$mp_options[banner_width]		=	(int)get_option('banner_width');
			$mp_options[banner_height]		=	(int)get_option('banner_height');
			
			$status = 1;		
		break;
				
		default:
		break;
	
	endswitch;

	return $status;

}
/*
	add_comic()
	
	This function adds the comic to the Wordpress database as a post
	using the Wordpress function wp_insert_page. Was expanded in the
	beta release of the 2.0 branch to take over the functionality of
	upload_comic()
	
	@Used by:	post-new-comic.php
	@since:		0.1b
	@modified:	2.0 beta
*/
function add_comic(&$file, $post_info){
global $mp_options, $wpdb, $wp_rewrite;
	
	if ($post_info[title] == '') { return '<strong>Empty Title-field!</strong> Comic not added.'; }
	$now = current_time('mysql'); // let's grab the time...need this for later on...
	
	$comicfile = wp_handle_upload($file[userfile], false, $now); // use Wordpress's native upload functions...makes more sense
	$error = $comicfile[error];
	
	// let's check for errors.
	if ($error != '') {
		return $error;
	} else {
		
		// if the comic page was uploaded successfully, 
		// check for option to make banners AND if GD Library is available...
		if (function_exists('gd_info') && $mp_options[make_banner]) {
			
			$banner_width = $mp_options[banner_width];
			$banner_height = $mp_options[banner_height];
			
			$bannerfile = wp_handle_upload($file[bannerfile], false, $now);

			if ($bannerfile[error] == '') { // no errors, banner was uploaded successfully...
			
				if ($mp_options[use_overlay] && $mp_options[banner_overlay][path] != ''){
					
					$bnfile = create_banner_image($bannerfile);
					if ($bnfile == NULL) { $msg = "<br />Warning: Banner not generated!"; }
				}else{
					// let's get all of the information together and
					// put it into an array...
					$file = image_resize($bannerfile['file'], $banner_width, $banner_height, true );
					if ($file == '') { $file = $bannerfile['file']; }
					$url	= dirname( $comicfile['url'] ).'/'.basename( $file );
					$img	= getimagesize($file);
					$mime	= image_type_to_mime_type($img[2]);
					$bnfile = compact( explode(' ', 'file url mime banner_width banner_height') );																																															
				}
			} else { // either banner upload didn't succeed, or there wasn't a banner uploaded...
				// in that case, use the comic itself...
				if ($mp_options[use_overlay] && $mp_options[banner_overlay][path] != '') { // if banner skins are enabled...

					$bnfile = create_banner_image($comicfile);
					if ($bnfile == NULL) { $msg = "<br />Warning: Banner not generated!"; }

				} else { // otherwise...use image_resize()...			
				
					$file = image_resize($comicfile['file'], $banner_width, $banner_height, true );
					$url	= dirname( $comicfile['url'] ).'/'.basename( $file );
					$img	= getimagesize($file);
					$mime	= image_type_to_mime_type($img[2]);
					$bnfile = compact( explode(' ', 'file url mime banner_width banner_height') );																																															
				}
			}
		}

		// $medium_side = get_option('medium_size_w');

		// This will do for now...
		//if ($mp_options[make_thumb]){
		//	$thumb_side	= get_option('thumbnail_size_w');
		//	$thmb_path	= wp_create_thumbnail( $comicfile['file'], $thumb_side	); // make small thumbnail...
		//}
		
		// Create a new Comic Post object to pass to wp_insert_post()....
		$newcomic = new WP_ComicPost($post_info, $comicfile, $bnfile);
		
		// this is needed to keep from getting the "Wrong datatype for second argument" error		
		$wp_rewrite->feeds = array( 'feed', 'rdf', 'rss', 'rss2', 'atom' );
		
		$post_id = wp_insert_post($newcomic); // let Wordpress handle the rest
		
		// if wp_insert_post() succeeds, now we add the comic file as an attachment to the post...
		if ($post_id != 0){
			$attach = new WP_ComicPost($post_info, $comicfile, NULL, 'attachment');
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
/*
	upload_overlay_image()

	returns a message code used in page-comic-options.php
	to display a message indicating if image has been
	uploaded successfully, or if it has failed.
	
	@Used by:	page-comic-options.php
	@since:		0.1b
	@modified:	2.0 beta
*/
function upload_overlay_image(&$file){
global $mp_options;
	// let's check and make sure the file uploaded alright
	$status = (int)file_upload_error_check($file['overlay_image']);
	if ($status != 0) { // if $status doesn't equal 0 then there's an error
		return $status;
	} else {
		$siteurl = get_settings('siteurl');
		
		// prepend ABSPATH to $dir and $siteurl to $url if they're not already there
		$path = str_replace(ABSPATH, '', trim(get_settings('upload_path')));
		$dir = ABSPATH . $path;
		$url = trailingslashit($siteurl) . $path;
		
		if ( $dir == ABSPATH ) { // the option was empty
			$dir = ABSPATH . 'wp-content/uploads';
		}
		
		if ( defined('UPLOADS') ) {
			$dir = ABSPATH . UPLOADS;
			$url = trailingslashit($siteurl) . UPLOADS;
		}
	
		/*	so now we know that the file is uploaded and it is of the
			correct type, so now lets start the processing....
		*/
		// PATH of the file...
		$upload_dir	= 	$dir . "/" . basename($file['overlay_image']['name']);
		
		// URL of the file...
		$upload_url	=	$url . "/" . basename($file['overlay_image']['name']);
	
		// since is_uploaded_file checked out alright, now move the file...
		$status = @move_uploaded_file($file['overlay_image']['tmp_name'], $upload_dir);
		
		// if $status returns true...	
		if($status){
			$stat = stat( dirname( $upload_dir ));
			$perms = $stat['mode'] & 0000666;
			@ chmod( $uploadfile, $perms );
	
			// let's pack everything into an array for easy storage...
			$skin_options = array ("path" => $upload_dir, "url" => $upload_url);
			update_option('comic_banner_overlay_image', serialize($skin_options), '', 'yes');
	
			list($width, $height)	=	getimagesize($upload_dir);
			
			update_option('banner_width',	$width,		'',	'yes');
			update_option('banner_height',	$height,	'',	'yes');
	
			$mp_options[banner_overlay]		=	unserialize(get_option('comic_banner_overlay_image'));
			$mp_options[banner_width]		=	(int)get_option('banner_width');
			$mp_options[banner_height]		=	(int)get_option('banner_height');
			
		} else {
			return 10; // 10 is the "unable to upload" error code. if $status is false, then move_uploaded_file() failed.
		}
	}
}
/*
	mime_check()
	
	Used by upload_overlay_image() to determine
	if file being uploaded is actually an image
	and not something else...
	returns false if the mime-types don't match.
	
	@Used by:	upload_overlay_image()
	@since:		0.1b
	@modified:	2.0 beta
*/
function mime_check($type){

	$types = array ( "image/jpeg", "image/gif", "image/png" );

	if (!in_array($type, $types) ) {
		return false;
	} else {
		return true;
	}
}
/*
	file_upload_error_check()

	This function is the error-checker for
	upload_overlay_image() and returns the
	error codes	used by page-comic-options.php
	
	@Used by:	upload_overlay_image()
	@since:		0.3b
	@modified:	2.0 beta
*/
function file_upload_error_check(&$file) {
	// check for file size errors and return appropriate error code
	if ($file['error'] == UPLOAD_ERR_FORM_SIZE) {
		return 9;
	}
	
	/* make sure that file has been uploaded via HTTP_POST...
	   If not, then terminate script and return error code
	*/
	if (!is_uploaded_file($file['tmp_name'])){
		return 10;
	}
	
	// check to make sure that the right type of file is being uploaded...
	if (!mime_check($file['type'])) {
		return 8;
	}
}
/***
 *	create_banner_image()
 *	
 *	Used by add_comic() to generate a banner image
 *	from the comic and/or combine an uploaded banner with
 *	an alpha-transparency PNG banner skin image.
 *	
 *	@Used by:	add_comic()
 *	@since:		0.1b
 *	@modified:	2.0 beta
***/
function create_banner_image($file){
global $mp_options;

	// first, let's grab info from the image file
	list($width, $height, $type)	=	getimagesize($file['file']);

	// load banner skin...
	$file_png =	$mp_options[banner_overlay][path];
	list($banner_width, $banner_height)	=	getimagesize($file_png);
	
	// this is the holder image for the banner file	
	$h_banner_img	=	@imagecreatetruecolor($banner_width, $banner_height);
	 // holder for banner skin...	
	$h_banner_png	=	@imagecreatefrompng($file_png);

	// if one of the two imagecreate functions fail, return NULL
	if (!$h_banner_img || !$h_banner_png) {
		return NULL;
	} else {
		
		// now here is the fun part...dividing the function up by image type...
		// call_user_func() is very handy for this sort of thing...
		$types	= array(IMAGETYPE_JPEG => "jpeg", IMAGETYPE_PNG => "png", IMAGETYPE_GIF => "gif");
		$ext	= array(IMAGETYPE_JPEG => ".jpg", IMAGETYPE_PNG => ".png", IMAGETYPE_GIF => ".gif");
		$fname	= basename( $file['file'] );
		$fpath	= dirname( $file['file'] );
		
		$himg2 = call_user_func( 'imagecreatefrom'.$types[$type], $file['file'] );
		$thmb_file = $fpath . '/' .str_replace(array('.jpeg','.jpe','.jpg', '.gif', '.png'), '', $fname).".thmb".$ext[$type];

		// let's do some math and get an aspect ratio...
		$ratio = (float)($height / $width);
		$res_width = $banner_width; // we want the image to be as wide as the banner...
		$res_height = $banner_width * $ratio; // and keeping the proportions of the original image...
		$h_resampled	=	imagecreatetruecolor($res_width, $res_height);
		$src_y			=	($res_height - $banner_height) / 2;

		// resize and copy opened file $himg2 to dummy image $h_resampled...
		imagecopyresampled($h_resampled, $himg2, 0, 0, 0, 0, $res_width, $res_height, $width, $height);
		
		//	then, copy from $h_resampled to the banner image $h_banner_img
		imagecopy($h_banner_img, $h_resampled, 0, 0, 0, $src_y, $banner_width, $banner_height);
		
		// finally, copy png over banner image...
		imagecopy($h_banner_img, $h_banner_png, 0, 0, 0, 0, $banner_width, $banner_height);

		// now, prepare to save $h_banner_img...
		// once again, process according to file-type
		if ($type == IMAGETYPE_JPEG) {
			imagejpeg($h_banner_img, $thmb_file, 80); // eventually, there will be an option in Comic Settings to set JPG quality
		} else {
			call_user_func( 'image'.$types[$type], $h_banner_img, $thmb_file );
		}
		
		// free up memory
		imagedestroy($h_banner_img);	// image handle created by imagecreatetruecolor()
		imagedestroy($h_resampled);		// image handle created by imagecreatetruecolor() for resampling/copying
		imagedestroy($himg2);			// image handle created from uploaded image
		imagedestroy($h_banner_png);	// image handle created for the alpha PNG overlay
	
		$stat = stat( dirname( $thmb_file ));
		$perms = $stat['mode'] & 0000666;
		@ chmod( $thmb_file, $perms );
	
		$url	= dirname( $file['url'] ).'/'.basename( $thmb_file );
		$file	= $thmb_file;
		$mime	= image_type_to_mime_type($type);
		$banner = compact( explode(' ', 'file url mime banner_width banner_height') );
		
		return $banner;
	}
}
#############################################################
#		Manga+Press plugin Hook Functions					#
#															#
#		These functions are used by add_action to run when	#
#		certain Wordpress functions are called.				#
#															#
#		@since:		0.1b									#
#		@modified:	1.0 RC2									#
#############################################################

/*
	add_navigation_css()
	
	is used to add CSS for comic navigation to <head> section
	when the custom code option hasn't been specified.

	@Called by:	wp_head()
	@since:		0.5b
	@modified:	1.0
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
/*
	add_header_info()
	
	@Called by:	wp_head()
	@since:		0.5b
	@modified:	0.5b
*/
function add_header_info() {
	echo "<meta name=\"Manga+Press\" content=\"".MP_VERSION."\" />\n";
}
/*
	add_footer_info()
	
	@Called by:	wp_footer()
	@since:		1.0 RC1
	@modified:	1.0 RC2
*/
function add_footer_info(){
	echo "<br />Powered by <a href=\"http://manga-press.silent-shadow.net\">Manga+Press</a> ".MP_VERSION;
}
/*
	add_meta_info()
	
	@Called by:	wp_meta()
	@since:		1.0 RC1
	@modified:	---
*/
function add_meta_info(){
	echo "<li><a href=\"http://manga-press.silent-shadow.net\" title=\"Powered by Manga+Press ".MP_VERSION.", a revolutionary new web comic management system for Wordpress\">Manga+Press</a></li>";
}
/*
	delete_comic()
	
	is used to delete comic from the comics DB table
	when comic is deleted via Manage Posts or Edit Post

	@Called by:	delete_post()
	@since:		0.1b
	@modified:	0.3b
*/
function delete_comic($post_id){
global $wpdb;

	$sql	=	$wpdb->prepare("DELETE FROM ".$wpdb->mpcomics." WHERE post_id=".$post_id.";");
	$wpdb->query($sql);
}
/*
	add_series()
	
	is used to add category to 
	the series table

	@Called by:	create_category
	@since:		1.0 RC1
	@modified:	---
*/
function add_series($cat_ID) {
global $wpdb, $mp_options;

	$cat = get_category($cat_ID);
	if ($cat->category_parent == $mp_options[latestcomic_cat]) {

		$sql	=	$wpdb->prepare("INSERT INTO ".$wpdb->mpcomicseries."(term_id) VALUES ('".$cat_ID."');");
		$wpdb->query($sql);

	}
}
/*
	delete_series()
	
	is used to delete category from the series DB table
	when category is deleted via Manage Categories

	@Called by:	delete_category
	@since:		1.0 RC1
	@modified:	---
*/
function delete_series($cat_ID) {
global $wpdb;

	$sql	=	$wpdb->prepare("DELETE FROM ".$wpdb->mpcomicseries." WHERE term_id=".$cat_ID.";");
	$wpdb->query($sql);
}
?>