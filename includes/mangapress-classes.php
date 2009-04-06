<?
/*
	A very simple Wordpress Post class for simplifying
	the posting of comics. Used by add_comic()
*/
class WP_ComicPost {
    var $post_title;
    var $post_content;
    var $post_status;
    var $post_author;    /* author user id (optional) */
    var $post_name;      /* slug (optional) */
    var $post_type;      /* 'page' or 'post' (optional, defaults to 'post') */
	var $post_date;
	var $post_date_gmt;
	var $post_mime_type;
	var $guid;
    var $comment_status; /* open or closed for commenting (optional) */
	var $post_category;
	var $post_excerpt;
	
	function WP_ComicPost($_post, $file, $banner = NULL, $_post_type = 'post') {
		global $mp_options, $userdata;

		get_currentuserinfo(); // needed to retrieve ID of currently logged in user
		

		$this->post_title		= $_post[title];
		$this->post_content		= ($_post_type == 'post') ? $this->get_comicpage_html($file) : "Attachment for ".$_post[title];
		$this->post_excerpt		= ($_post_type == 'post') ? $this->get_comicpage_html($banner, true) : '';
		$this->post_status		= 'publish';
		$this->post_type		= ($_post_type == 'post') ? 'post' : 'attachment';
		$this->post_mime_type	= ($_post_type == 'post') ? '' : $file['type'];
		$this->post_category	= $_post[categories];
		$this->post_author		= $userdata->ID;
		$this->guid				= ($_post_type == 'post') ? '' : $file['url'];
		$this->post_date		= current_time('mysql');
		$this->post_date_gmt	= current_time('mysql', 1);

	}
	
	private function get_comicpage_html($image, $excerpt = false) {
		
		list($width, $height, $type, $attr)	=	getimagesize($image['file']);
		
		$excerpt ? $classes = 'comic-banner' : $classes = 'comic-page';
		$html = '<img src="'.$image['url'].'" '.$attr.' class="aligncenter size-full '.$classes.'" alt="[image]" />';
		
		return $html;
		
	}
}
/**
 * WordPress PHP class to check for a new version.
 * @author Alex Rabe & Joern Kretzschmar
 * @orginal from Per Søderlind
 * @copyright 2007 / free for every usage
 *
 // Dashboard update notification example
	function myPlugin_update_dashboard() {
	  $Check = new CheckPlugin();	
	  $Check->URL 	= "YOUR URL";
	  $Check->version = "1.00";
	  $Check->name 	= "myPlugin";
	  if ($Check->startCheck()) {
 	    echo '<h3>Update Information</h3>';
	    echo '<p>A new version is available</p>';
	  } 
	}
	
	add_action('activity_box_end', 'myPlugin_update_dashboard', '0');
 *
 */
if ( !class_exists( "CheckPlugin" ) ) {  
	class CheckPlugin {
		/**
		 * URL with the version of the plugin
		 * @var string
		 */
		var $URL = 'myURL';
		/**
		 * Version of thsi programm or plugin
		 * @var string
		 */
		var $version = '1.00';
		/**
		 * Name of the plugin (will be used in the options table)
		 * @var string
		 */
		var $name = 'myPlugin';
		/**
		 * Waiting period until the next check in seconds
		 * @var int
		 */
		var $period = 86400;					
					
		function startCheck() {
			/**
			 * check for a new version, returns true if a version is avaiable
			 */
			
			// use wordpress snoopy class
			require_once(ABSPATH . WPINC . '/class-snoopy.php');
			
			$check_intervall = get_option( $this->name."_next_update" );

			if ( ($check_intervall < time() ) or (empty($check_intervall)) ) {
				if (class_exists(snoopy)) {
					$client = new Snoopy();
					$client->_fp_timeout = 10;
					if (@$client->fetch($this->URL) === false) {
						return false;
					}
					
				   	$remote = $client->results;
				   	
					$server_version = unserialize($remote);
					if (is_array($server_version)) {
						if ( version_compare($server_version[$this->name], $this->version, '>') )
						 	return true;
					} 
					
					$check_intervall = time() + $this->period;
					update_option( $this->name."_next_update", $check_intervall );
					return false;
				}				
			}
		}
	}
}
?>