<?
/**
 * A very simple Wordpress Post class for simplifying the posting of comics. Used by add_comic()
 *
 * @package Manga_Press
 * @subpackage WP_ComicPost
 * @since 0.1
 * @see add_comic()
 */
class WP_ComicPost {
    /**
     * Title of post to be added.
     * 
     * @since 0.1
     * @access public
     * @var string
     */
    var $post_title;
    /**
     * Content of post.
     * 
     * @since 0.1
     * @access public
     * @var string 
     */
    var $post_content;
    /**
     * Post status (draft/published/private)
     * 
     * @since 0.1
     * @access public
     * @var string 
     */
    var $post_status;
    /**
     * Post author id
     * 
     * @since 0.1
     * @access public
     * @var int 
     */
    var $post_author;
    /**
     * Name (slug) of post. Used as url when permalinks are enabled
     * 
     * @since 0.1
     * @access public
     * @var string 
     */
    var $post_name;
    /**
     * Type of post: 'page', 'post' or 'attachment'. Specifies if the post is
     * a normal post, a page or an attachment. If attachment, then mime-type is
     * specified by $post_mime_type.
     * 
     * @since 0.1
     * @access public
     * @var string 
     */
    var $post_type;
    /**
     * Post date and time.
     * 
     * @since 0.1
     * @access public
     * @var datetime 
     */
	var $post_date;
    /**
     * Post date and time in Greenwich mean
     * 
     * @since 0.1
     * @access public
     * @var datetime 
     */
	var $post_date_gmt;
    /**
     * Specifies mime-type if $post_type is set to attachment.
     * 
     * @since 2.0
     * @access public
     * @var string
     */
	var $post_mime_type;
    /**
     * @since 2.0
     * @access public
     * @var string
     */
	var $guid;
    var $comment_status; // open or closed for commenting (optional)
    /**
     * Sets the post categor(ies)
     *
     * @since 0.1
     * @access public
     * @var array
     */
	var $post_category;
    /**
     * Specifies an optional short blurb for post
     *
     * @since 0.1
     * @access public
     * @var string
     */
	var $post_excerpt;
	
	/**
     * Post Object creation handler. Puts together the information fed by add_comic() into a usable object
     * for Wordpress function wp_insert_post()
     * @see function add_comic()
     *
     * @since 2.0
     * @access public
     * 
     * @global array $mp_options
     * @global int $userdata
     * @param array $_post. Array that contains the majority of the post information.
     * @param array $file. Array that contains information returned by $_FILES.
     * @param string $_post_type. Optional. Specifies type of post, ie 'post' or 'attachment'. Defaults to 'post'
     */
    function WP_ComicPost($_post, $file, $_post_type = 'post') {
		global $mp_options, $userdata;

		get_currentuserinfo(); // needed to retrieve ID of currently logged in user
		

		$this->post_title		= $_post['title'];
		$this->post_content		= ($_post_type == 'post') ? $this->get_comicpage_html($file) : "Attachment for ".$_post['title'];
		$this->post_excerpt		= ($_post_type == 'post') ? $_post['excerpt'] : '';
		$this->post_status		= 'publish';
		$this->post_type		= ($_post_type == 'post') ? 'post' : 'attachment';
		$this->post_mime_type	= ($_post_type == 'post') ? '' : $file['type'];
		$this->post_category	= $_post['post_category'];
		$this->post_author		= $userdata->ID;
		$this->guid				= ($_post_type == 'post') ? '' : $file['url'];
		$this->post_date		= current_time('mysql');
		$this->post_date_gmt	= current_time('mysql', 1);

	}
	
	/**
     * @see WP_ComicPost()
     * @since 2.0
     * @access private
     * 
     * @param array $image Array that contains path and url of image file.
     * @param bool $excerpt Optional. Not used.
     * @return string $html String that contains the html to be placed in $this->post_content.
     */
    private function get_comicpage_html($image, $excerpt = false) {
		
		list($width, $height, $type, $attr)	=	getimagesize($image['file']);
		
		$html = '<img src="'.$image['url'].'" '.$attr.' class="aligncenter size-full comic-page" alt="[image]" />';
		
		return $html;
		
	}
}
?>