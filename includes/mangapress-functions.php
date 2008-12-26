<?
/**		Manga+Press plugin Functions
		This is where the actual work gets done...
		
		@since:		0.1b
		@modified:	1.0 RC1

*/
/*
	update_options()

	returns true or a number based on error
	@Used by:	page-comic-options.php
	@since:		0.1b
	@modified:	0.5b
*/
function update_options($options, $file = ''){
global $mp_options, $wpdb;

	extract($options);
	
	switch($action):
	
		case 'update_options':
			$status = 1;
			update_option('comic_latest_default_category',	$options[latest],		'',	'yes');
			update_option('comic_order_by', 				$options[order_by],	'',	'yes');
			update_option('comic_latest_page',				$options[latest_page],'',	'yes');
			update_option('comic_archive_page',				$options[archive_page],'',	'yes');
			update_option('comic_use_default_css',			$options[nav_css],		'',	'yes');
			
			$mp_options[latestcomic_cat]	= get_option('comic_latest_default_category');
			$mp_options[order_by]			=	get_option('comic_order_by');
			$mp_options[latestcomic_page]	=	get_option('comic_latest_page');
			$mp_options[comic_archive_page]	= get_option('comic_archive_page');
			$mp_options[nav_css]			= get_option('comic_use_default_css');
		break;

		case 'set_dir':
			update_option('series_organize', $options[organize_by_series], '', 'yes');
			$mp_options[series_organize] = (int)get_option('series_organize');

			$status = 1;
			if ($options[new_dir] != ''){
				$status	=	set_new_dir($options[new_dir]);
			}
		
		break;

		case 'set_image_options':
		
			$status = 1;
			if ($mp_options[use_overlay]) {

				if ($file[overlay_image][name] != '') {
					$status	=	upload_overlay_image($file);
				}
			}
			update_option('comic_use_overlay', $options[use_overlay], '', 'yes');
			update_option('comic_make_banner', $options[make_banner],	'',	'yes');		
			$mp_options[use_overlay]		=	(bool)get_option('comic_use_overlay');
			$mp_options[make_banner]		=	(bool)get_option('comic_make_banner');
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
	set_new_dir()
	
	Used by page-comic-options.php to set a directory
	inside the wp-content directory for comic page
	uploads.
	
	@Used by:	page-comic-options.php
	@since:		0.1b
	@modified:	0.3b
*/
function set_new_dir($dir_name){
global $wpdb, $mp_options;

	if ($dir_name	==	''){
		return 5;
	} else {
		$new_dir	=	WP_CONTENT_DIR."/".$dir_name;
		$status	=	@realpath($new_dir);

		if ($status) {
			update_option('comic_default_dir',	$dir_name,	'',	'yes');
			return 6;
		} else {
			return 7;
		}
	}
}

/*
	upload_overlay_image()

	this function is pretty much identical to upload_comic()
	except the series name isn't used; the image is uploaded
	to the root comic directory.
	returns a message code used in page-comic-options.php
	to display a message indicating if image has been
	uploaded successfully, or if it has failed.
	
	@Used by:	page-comic-options.php
	@since:		0.1b
	@modified:	1.0 RC1
*/
function upload_overlay_image(&$file){
global $mp_options;
	
	// let's check and make sure the file uploaded alright
	$status = (int)file_upload_error_check($file, 'overlay_image');
	if ($status != 0) { // if $status doesn't equal 0 then there's an error
		return $status;
		exit ();
	}
	
	/*	so now we know that the file is uploaded and it is of the
		correct type, so now lets start the processing....
	*/
	 // directory that file is being uploaded to: /home/www/[wordpress-install]/wp-content/comic/
	 // unless the organize by series option is enabled, then create a folder that corresponds to the
	$uploaddir	= WP_CONTENT_DIR . "/" . $mp_options[comic_dir];

	// URL of the directory that file has been uploaded to: http://www.mydomain.com/wp-content/comic/
	$filepath	=	WP_CONTENT_URL . "/" . $mp_options[comic_dir];
	$uploadfile = 	$uploaddir . "/" . basename($file['overlay_image']['name']);
	
	// URL of the directory that file has been uploaded to: http://www.mydomain.com/wp-content/comic/
	$filepath	=	WP_CONTENT_URL . "/" . $mp_options[comic_dir];
	$fname		=	$filepath . "/" . basename($file['overlay_image']['name']);


	// since is_uploaded_file checked out alright, now move the file...
	$status = @move_uploaded_file($file['overlay_image']['tmp_name'], $uploadfile);
	
	// if $status returns true...	
	if($status){
		update_option('comic_banner_overlay_image', $fname, '', 'yes');

		list($width, $height)	=	getimagesize($fname);
		
		update_option('banner_width',	$width,		'',	'yes');
		update_option('banner_height',	$height,	'',	'yes');

		$mp_options[banner_overlay]		=	get_option('comic_banner_overlay_image');
		$mp_options[banner_width]		=	(int)get_option('banner_width');
		$mp_options[banner_height]		=	(int)get_option('banner_height');
		
	} else {
		return 10; // 10 is the unable to upload error code. if $status is false, then move_uploaded_file() failed.
	}
}

/*
	upload_comic()

	handles the uploading of a comic image and an optional
	banner for the comic page.
	returns a message code used in page-new-comic.php
	to display a message indicating a file error or
	if comic has been posted successfully.
	
	@Used by:	page-new-comic.php
	@since:		0.1b
	@modified:	0.5b
*/
function upload_comic(&$file){
global $mp_options;

	// let's check and make sure the file uploaded alright
	$status = (int)file_upload_error_check($file, 'userfile');
	if ($status != 0) { // if $status doesn't equal 0 then there's an error
		return $status;
		exit ();
	}

	/*	so now we know that the file is uploaded and it is of the
		correct type, so now lets start the processing....
	*/
	 // directory that file is being uploaded to: /home/www/[wordpress-install]/wp-content/comic/
	 // unless the organize by series option is enabled, then create a folder that corresponds to the
	 // category-name passed by $_POST[categories][2]
	
	if ($mp_options[series_organize]) {
		$cat	=	get_cat_name($_POST[categories][2]);
		$series_dir	=	sanitize_title_with_dashes($cat);
		$uploaddir	= WP_CONTENT_DIR . "/" . $mp_options[comic_dir] . "/" . $series_dir;

		// URL of the directory that file has been uploaded to: http://www.mydomain.com/wp-content/comic/
		$filepath	=	WP_CONTENT_URL . "/" . $mp_options[comic_dir] . "/" . $series_dir;
		//
		// check if uploading directory exists; if not..and if permissions allow...make it
		if (!is_dir($uploaddir)) {
			@mkdir($uploaddir);
		}
	} else {
		$uploaddir	= WP_CONTENT_DIR . "/" . $mp_options[comic_dir];
		// URL of the directory that file has been uploaded to: http://www.mydomain.com/wp-content/comic/
		$filepath	=	WP_CONTENT_URL . "/" . $mp_options[comic_dir];
	}
	 
	 // $uploadfile: combines $uploaddir with the filename of the uploaded file. needed for move_uploaded_file()
	$uploadfile = 	$uploaddir . "/" . basename($file['userfile']['name']);
	$fname		=	$filepath . "/" . basename($file['userfile']['name']);

	// since is_uploaded_file checked out alright, now move the file...
	$status = @move_uploaded_file($file['userfile']['tmp_name'], $uploadfile);
	
	// if $status returns true then add the comic to the database...	
	if($status){
		 // if GD library is present, create a banner from the uploaded file
		 // or an image uploaded with comic, if present
		if (function_exists('gd_info') && $mp_options[make_banner]){
			//
			// if the banner image has been uploaded successfully...
			$bannerstatus = (int)file_upload_error_check($file,'bannerfile');

			$bannerfile = 	$uploaddir . "/" . basename($file['bannerfile']['name']);
			$bannerfname =	$filepath . "/" . basename($file['bannerfile']['name']);
			//
			// since is_uploaded_file checked out alright, now move the file...
			$statusmoved = @move_uploaded_file($file['bannerfile']['tmp_name'], $bannerfile);
			if ($bannerstatus == 0 && $statusmoved) {
				$thmb_file	=	create_banner_image($bannerfile);
				$thmb_file	=	basename($thmb_file);
				$thmb_file	=	$filepath . "/" . $thmb_file;
			} else { // if neither checked out
				$thmb_file	=	create_banner_image($uploadfile);
				$thmb_file	=	basename($thmb_file);
				$thmb_file	=	$filepath . "/" . $thmb_file;
			}
		} else {
			$thmb_file	=	NULL;
		}
		$comic_post = array(
						"title" => $_POST[title], 
						"categories" => $_POST[categories], 
						"file_upload"	=> $uploadfile,
						"file_path" => $fname, 
						"file_thmb" => $thmb_file
						);
						
		add_comic($comic_post, $file['userfile']['type']);
	}
	
	return $status;
}
/*
	file_upload_error_check()

	This function is the error-checker for
	upload_comic() and upload_overlay_image()
	and returns the error codes	used by page-new-comic.php
	and page-comic-options.php
	
	@Used by:	upload_comic(), upload_overlay_image()
	@since:		0.3b
	@modified:	0.5b
*/
function file_upload_error_check(&$file, $filename = '') {
		// check for file size errors and return appropriate error code
	if ($file[$filename]['error'] == UPLOAD_ERR_FORM_SIZE) {
		return 9;
		exit ();
	}
	
	/* make sure that file has been uploaded via HTTP_POST...
	   If not, then terminate script and return error code
	*/
	if (!is_uploaded_file($file[$filename]['tmp_name'])){
		return 10;
		exit ();
	}
	
	// check to make sure that the right type of file is being uploaded...
	if (!check_mime_type($file[$filename]['type'])) {
		return 8;
		exit ();
	}
}
/*
	add_comic()
	
	this function adds the comic to the Wordpress database as a post
	using the Wordpress function wp_insert_page.
	
	@Used by:	upload_comic()
	@since:		0.1b
	@modified:	1.0 RC1
*/
function add_comic($comic, $type = ''){
global $mp_options, $userdata, $wpdb, $wp_rewrite;
	
	get_currentuserinfo(); // needed to retrieve ID of currently logged in user

	// initialize post object
	$wb_post = new comic_post();
	// since $categories[2] is our series comic,
	// we want to find out its parent is another series
	// if it is, we want to include that one as well
	$cat_parent = get_category($comic[categories][2]);
	
	if ($cat_parent->category_parent != $mp_options[latestcomic_cat]) {
		$comic[categories][3] = $cat_parent->category_parent;
	}
	
	$image	=	getimagesize($comic[file_path]);
	list($width, $height) = $image;
	extract( $image );
	
	$wb_post->post_title	= $comic[title];
	$wb_post->post_content	= "<img src=\"".$comic[file_path]."\" style=\"width:$width; height:$height; border: none;\" alt=\"".$comic[title]."\" title=\"".$comic[title]."\" />";
	$wb_post->post_status	= 'publish';
	$wb_post->post_category = $comic[categories];
	$wb_post->post_author	= $userdata->ID;
	$wb_post->post_date		= date('Y/m/d H:i:s');
	$wb_post->post_date_gmt = gmdate('Y/m/d H:i:s');
	$wp_rewrite->feeds = array( 'feed', 'rdf', 'rss', 'rss2', 'atom' ); // this is needed to keep from getting the "Wrong datatype for second argument" error

	if ($comic[file_thmb] != NULL){
		$wb_post->post_excerpt	= "<img src=\"".$comic[file_thmb]."\" style=\"width: $mp_options[banner_width]px; height: $mp_options[banner_height]px\" alt=\"$comic[title]\" />";
	}
	
	$post_id = wp_insert_post($wb_post); // let Wordpress handle the rest
	if ($post_id == 0){
		return 0;
	}else{
		// Now, we want Wordpress to add the image as an attachment
		$attach = new comic_post();
		$attach->post_title		= $comic[title];
		$attach->post_content	= "Attachment for ".$comic[title];
		$attach->post_status	= 'publish';
		$attach->post_type		= 'attachment';
		$attach->post_mime_type	= $mime;
		$attach->post_category	= $comic[categories];
		$attach->post_author	= $userdata->ID;
		$attach->guid			= $comic[file_path];
		$attach->post_date		= date('Y/m/d H:i:s');
		$attach->post_date_gmt	= gmdate('Y/m/d H:i:s');
		//$wp_rewrite->feeds = array( 'feed', 'rdf', 'rss', 'rss2', 'atom' ); // this is needed to keep from getting the "Wrong datatype for second argument" error
	
		wp_insert_attachment($attach, false, $post_id);
		
		add_post_meta($post_id, 'comic', '1'); // adds required meta data to the post
		$sql	=	$wpdb->prepare("INSERT INTO " . $wpdb->mpcomics . " (post_id, post_date) VALUES ('".$post_id."', '".$wb_post->post_date."') ;");
		$wpdb->query($sql);
		return $post_id; // return post_id if it works...if not, return 0
	}
}
/*
	check_mime_type()
	
	Used by upload_comic() and upload_overlay_image()
	to determine if file being uploaded is actually an
	image and not something else...
	returns false if the mime-types don't match.
	
	@Used by:	upload_comic(), upload_overlay_image()
	@since:		0.1b
	@modified:	0.2b
*/
function check_mime_type($type){

	switch($type){
		case "image/jpeg":
			return true;
		break;
		
		case "image/gif":
			return true;
		break;
		
		case "image/png":
			return true;
		break;
		
		default:
			return false;
		break;
	}
}
/*
	create_banner_image()
	
	Used by upload_comic() to generate a banner image
	from the comic or combine an uploaded banner with
	an alpha-transparency PNG "overlay" image.
	
	@Used by:	upload_comic()
	@since:		0.1b
	@modified:	0.3b
*/
function create_banner_image($file, $title = ''){
global $mp_options;

	// first, let's grab info from the image file
	list($width, $height, $type)	=	getimagesize($file);

	// then process the overlay file, if one is used...
	if ($mp_options[use_overlay]) {
		$file_png =	$mp_options[banner_overlay];
		list($banner_width, $banner_height)	=	getimagesize($file_png);
	} else {
		$banner_width	=	$mp_options[banner_width];
		$banner_height	=	$mp_options[banner_height];
	}
	
	if (($height > $banner_height ) && ($width > $banner_width)){ // if height & width are greater than the banner width, then shrink the image
		$res_height	=	$height * 0.75;
		$res_width	=	$width	* 0.75;
	} else {
		$res_height	=	$height;
		$res_width	=	$width;
	}
	
	// let's say that our banner is 230x30 so we want our x, y to reflect that
	// now, lets generate random x,  y coordinates from hieght & width
	$x	=	rand(1, $res_width - $banner_width);
	$y	=	rand(1, $res_height - $banner_height);
	$n_width	=	$x + $banner_width;
	$n_height	=	$y + $banner_height;
	
	
	
	
	$h_banner_img	=	imagecreatetruecolor($banner_width, $banner_height);
	if ($mp_options[use_overlay]){
		$h_banner_png	=	imagecreatefrompng($file_png);
	}
	
	if (!$h_banner_img) {
		return NULL;
		exit ();
	}
	
	$h_resampled	=	imagecreatetruecolor($res_width, $res_height);
	
	// now here is the fun part...dividing the function up by image type...
	switch($type){
		case IMAGETYPE_GIF:
			$himg2	=	imagecreatefromgif($file);	
			$thmb_file	=	$file.".thmb.gif";
		break;
		
		case IMAGETYPE_JPEG:
			$himg2	=	imagecreatefromjpeg($file);	
			$thmb_file	=	$file.".thmb.jpg";
		break;
		
		case IMAGETYPE_PNG:
			$himg2	=	imagecreatefrompng($file);	
			$thmb_file	=	$file.".thmb.png";
		break;
		
		default:
		break;
	}
	
	// resize and copy opened file $himg2 to dummy image $h_resampled...
	imagecopyresampled($h_resampled, $himg2, 0, 0, 0, 0, $res_width, $res_height, $width, $height);
	
	//	then, copy from $h_resampled to the banner image $h_banner_img
	imagecopy($h_banner_img, $h_resampled, 0, 0, $x, $y, $banner_width, $banner_height);
	
	if ($mp_options[use_overlay]){
		// finally, copy png over banner image...
		imagecopy($h_banner_img, $h_banner_png, 0, 0, 0, 0, $banner_width, $banner_height);
	}	
	// now, prepare to save to $h_banner_img...
	// once again, process according to file-type
	switch($type){
		case IMAGETYPE_GIF:
			imagegif($h_banner_img, $thmb_file);
		break;
		
		case IMAGETYPE_JPEG:
			imagejpeg($h_banner_img, $thmb_file, 80);
		break;
		
		case IMAGETYPE_PNG:
			imagepng($h_banner_img, $thmb_file);
		break;
		
		default:
		break;
	}
	
	// free up memory
	imagedestroy($h_banner_img);	// image handle created by imagecreatetruecolor()
	imagedestroy($h_resampled);		// image handle created by imagecreatetruecolor() for resampling/copying
	imagedestroy($himg2);			// image handle created from uploaded image

	if ($mp_options[use_overlay]){
		imagedestroy($h_banner_png);	// image handle created for the alpha PNG overlay
	}
	
	return $thmb_file;
}
/**		Manga+Press plugin Hook Functions
		
		These functions are used by add_action to run when
		certain Wordpress functions are called.
		
		@since:		0.1b
		@modified:	0.5b

*/
/*
	add_navigation_css()
	
	is used to add CSS for comic navigation to <head> section
	when the custom code option hasn't been specified.

	@Called by:	wp_head()
	@since:		0.5b
	@modified:	0.5b
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
	echo "\t ul.comic-nav a:link, .mycomic_navigation a:visited { color: #ccc;	text-decoration: none; }\n";
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
	@modified:	---
*/
function add_footer_info(){
	echo "Powered by <a href=\"http://www.dumpster-fairy.com/tag/mangapress\">Manga+Press</a> ".MP_VERSION;
}
/*
	add_meta_info()
	
	@Called by:	wp_meta()
	@since:		1.0 RC1
	@modified:	---
*/
function add_meta_info(){
	echo "<li><a href=\"http://www.dumpster-fairy.com/tag/mangapress\" title=\"Powered by Manga+Press ".MP_VERSION.", a revolutionary new web comic management system for Wordpress\">Manga+Press</a></li>";
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