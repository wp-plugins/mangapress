<?
/**
 * Display Option Tabs section
 * Some plugin writers prefer to use echo statements to output the code for their options tab,
 * I prefer to create seperate files and use include statements. Is much neater that way!
 *
 * @package Manga_Press
 * @subpackage Display_Option_Tabs
 * @since 0.5
 *
 */
 
// displays the upload form for the Post Comic tab
function mangapress_post_comic(){
global $mp_options;
		
	if ( ! current_user_can('upload_files') )
		wp_die(__('You do not have sufficient permissions to manage options for this blog.'));
		
	if (count($_FILES) != 0 || count($_POST) != 0) { $status	=	mpp_add_comic($_FILES, $_POST); }
?>
	<?php if (isset($status)) : ?>
    <div id="message" class="updated fade"><p><?php echo $status; ?></p></div>
    <?php unset($status); ?>
    <?php endif; ?>
<div class="wrap">
<h2>Post New Comic</h2>
	<form enctype="multipart/form-data" action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
	<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
	<input type="hidden" name="action" value="wp_handle_upload" />
    <?php wp_nonce_field('mp_post-new-comic'); ?>
    
		<table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="title">Title:</label></th>
                    <td><input type="text" name="title" class="regular-text" id="title" size="30" /><span class="description">&nbsp;</span>
                    </td>
                </tr>
                <tr>
                    <th></th>
                    <td>
                    <fieldset>
                        <legend><h3>Series &amp; Chapters</h3><span class="description">Recommended: if you select a category that is a "chapter" of a series category, then the series category should be selected as well.</span></legend>
                        <ul>
                            <?php mpp_category_checklist(0, $mp_options['latestcomic_cat'], false ) ?>
                        </ul>
                    <input type="hidden" name="post_category[]" value="<?php echo $mp_options['latestcomic_cat'] ?>" />
                  </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="userfile">Comic:</label></th><td><input name="userfile" id="userfile" type="file" /></td>
                </tr>
                <tr>
                  <th scope="row">Description (Excerpt):</th>
                  <td><textarea name="excerpt" id="excerpt" cols="40" rows="7"></textarea></td>
                </tr>
                <tr>
                    <td colspan="2" align="left"><input type="submit" value="Update Comic" class="button-primary" /></td>
                </tr>
            </tbody>           
		</table>
	</form>
</div>
<?php
	
}
// displays the Comic Options tab, which is located in the Options tab
function mangapress_options_page(){
global $mp_options;

	if ( ! current_user_can('manage_options') )
		wp_die(__('You do not have sufficient permissions to manage options for this blog.'));

?>

<h2>Manga+Press Options</h2>
<div class="wrap">
  <form action="options.php" method="post" id="basic_options_form">
    <div id="basic_options">
      <h3>Basic Options</h3>
      <p class="description">This section sets the main options for Manga+Press.</p>
      <p class="submit">
        <input type="submit" class="button-primary" value="Update Options &raquo;" />
      </p>
      <?php settings_fields('mangapress-options'); ?>
      <table class="form-table">
        <tr>
          <th scope="col">Navigation CSS:</th>
          <td><select name="mangapress_options[nav_css]">
              <option value="default_css" <? if ($mp_options['nav_css'] == 'default_css'){ echo " selected=\"selected\""; } ?>>Default</option>
              <option value="custom_css" <? if ($mp_options['nav_css'] == 'custom_css'){ echo " selected=\"selected\"" ; } ?>>Custom</option>
            </select>
            Turn this off. you know you want to! </td>
        </tr>
        <tr>
          <th scope="col"></th>
          <td>Copy and paste this code into the <code>style.css</code> file of your theme.<br />
            <textarea style="width: 98%;" rows="10" cols="50">
        /* comic navigation */
        .comic-navigation { text-align:center; margin: 5px 0 10px 0; }
        .comic-nav-span { padding: 3px 10px;	text-decoration: none; }
        ul.comic-nav  { margin: 0; padding: 0; white-space: nowrap; }
        ul.comic-nav li { display: inline;	list-style-type: none; }
        ul.comic-nav a { text-decoration: none; padding: 3px 10px; }
        ul.comic-nav a:link, ul.comic-nav a:visited { color: #ccc;	text-decoration: none; }
        ul.comic-nav a:hover { text-decoration: none; }
        ul.comic-nav li:before{ content: ""; }
        </textarea></td>
        </tr>
        <tr>
          <th scope="col" class="th-full">Order by:</th>
          <td><select name="mangapress_options[order_by]">
              <option value="post_date" <?php selected( 'post_date', $mp_options['order_by']) ?>>Date</option><? //if ($mp_options['order_by'] == 'post_date'){ echo " selected=\"selected\""; } ?>
              <option value="post_id" <?php selected( 'post_id', $mp_options['order_by']) ?> >Post ID</option> <? //if ($mp_options['order_by'] == 'post_id'){ echo " selected=\"selected\"" ; } ?>
            </select></td>
        </tr>
        <tr>
          <th scope="col" class="th-full">Comic Navigation:</th>
          <td><label for="insert_nav">
              <input type="checkbox" name="mangapress_options[insert_nav]" id="insert_nav" value="1" <?php checked( '1', $mp_options['insert_nav'] ); ?>/>
              Automatically insert comic navigation code into comic posts.</label></td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <th scope="col" class="th-full"> Comic Category:</th>
          <td><select name="mangapress_options[latestcomic_cat]" id="latestcomic_cat">
              <option value="">&nbsp;</option>
              <?php
            $categories2	=  get_categories('hide_empty=0'); 
            $current_cat2 = $mp_options['latestcomic_cat'];
            foreach ($categories2 as $cat) {
				$sel = selected($current_cat2, $cat->cat_ID, false);

                $option = "\t\t\t<option value=\"".$cat->cat_ID."\" $sel>";
                $option .= $cat->cat_name;
                $option .= "</option>\n";
                echo $option;
            }
         ?>
            </select>
            <span class="description">this category is for use by the Latest Comic Page to display the most recent comic, as well as a place to store all child categories that represent series.</span></td>
        </tr>
        <tr>
          <th scope="col">&nbsp;</th>
          <td><label for="exclude_comic_cat">
              <input type="checkbox" name="mangapress_options[comic_front_page]" id="exclude_comic_cat" value="1" <?php checked( '1', $mp_options['comic_front_page'] ); ?> />
              Exclude comic category from front page.</label></td>
        </tr>
        <tr>
          <th scope="col" class="th-full">Latest Comic Page</th>
          <td><select name="mangapress_options[latestcomic_page]">
              <option value="">&nbsp;</option>
              <?php
            
            $pages	=  get_pages();
            $current_page = $mp_options['latestcomic_page'];
            foreach ($pages as $page) {
				$sel = selected($current_page, $page->ID, false);
                $option = "\t\t\t\t<option value=\"".$page->ID."\" $sel>";
                $option .= $page->post_title;
                $option .= "</option>\n";
                echo $option;
            }
         ?>
            </select>
            <span class="description">Sets a page for displaying the most recent comic.</span></td>
        </tr>
        <tr>
          <th scope="col" class="th-full">Comic Archive Page</th>
          <td><select name="mangapress_options[comic_archive_page]">
              <option value="">&nbsp;</option>
              <?php
            
            $pages	=  get_pages();
            $current_page = $mp_options['comic_archive_page'];
            foreach ($pages as $page) {
				$sel = selected($current_page, $page->ID, false);				
                $option = "\t\t\t\t<option value=\"".$page->ID."\" $sel>";
                $option .= $page->post_title;
                $option .= "</option>\n";
                echo $option;
            }
         ?>
            </select>
            <span class="description">Sets a page for displaying the comic archive page. CANNOT be the same as your Latest Comic page.</span></td>
        </tr>
      </table>
    </div>
    <div id="image_options">
      <h3>Image Options</h3>
      <p class="description">This section controls banner and thumbnail generation for comic pages.</p>
      <p class="submit">
        <input type="submit" class="button-primary" value="Update Options &raquo;" />
      </p>
      <table class="form-table">
        <tr>
          <th class="th-full"><label for="make_thumb">
              <input type="checkbox" name="mangapress_options[make_thumb]" id="make_thumb" value="1" <?php checked( '1', $mp_options['make_thumb'] ); ?> />
              Generate Thumbnail for Comic Page <span class="description">(thumbnail size can be set in <a href="options-media.php">Wordpress Settings &gt; Media</a>)</span></label></th>
        </tr>
        <tr>
          <th class="th-full"><label for="insert_banner">
              <input type="checkbox" name="mangapress_options[insert_banner]" id="insert_banner" value="1" <?php checked( '1', $mp_options['insert_banner']); ?> />
              Insert banner on home page.
            <span class="description">Automatically inserts comic banner html at the start of The Loop on the home page.</span></label></th>
        </tr>
      </table>
      <h4>Set Banner Width and Height</h4>
      <p class="description">Sets the size of the comic banner displayed on the front page. Remember to adjust any CSS sizing used to the values below!</p>
      <table class="form-table">
        <tr>
          <th><label for="banner_width">Banner Width:</label></th>
          <td><label>
              <input type="text" size="6" name="mangapress_options[banner_width]" id="banner_width" value="<?=$mp_options['banner_width']?>" />
              pixels</label></td>
        </tr>
        <tr>
          <th><label for="banner_height">Banner Height:</label></th>
          <td><label>
              <input type="text" size="6" name="mangapress_options[banner_height]" id="banner_height" value="<?=$mp_options['banner_height']?>" />
              pixels</label></td>
        </tr>
      </table>
    </div>
    <div id="update_notif">
      <h3>Comic Updates Notification</h3>
      <p class="description">This section is for custom code that you wish to insert into your comic post. For example, the custom html comments that <a href="http://www.onlinecomics.net/">OnlineComics.net</a> requires for its PageScan comic updates service.</p>
      <p class="submit">
        <input type="submit" class="button-primary" value="Update Options &raquo;" />
      </p>
      <table class="form-table">
        <tr>
          <th colspan="2"><h4>TheWebComicList.com Code:</h4></th>
        </tr>
        <tr>
          <td colspan="2"><span class="description">This options inserts an html comment which contains the date of the most recent comic near the beginning of the content section (usually The Loop). This is sometimes needed when TWC has a hard time detecting the status of the comic.</span></td>
        </tr>
        <tr>
          <td colspan="2"><label>
              <input name="mangapress_options[twc_code_insert]" type="checkbox" id="enable_twc_date_code" value="1" <?php checked('1', $mp_options['twc_code_insert']); ?>/>
              Enable TWC date stamp comment</label></td>
        </tr>
        <tr>
          <th colspan="2"><h4>OnlineComics.net Code: </h4></th>
        </tr>
        <tr>
          <td colspan="2"><span class="description">This option is to be used with comics that are listed in the OnlineComics.net directory <em>and</em> have the PageScan option enabled.</span></td>
        </tr>
        <tr>
          <td colspan="2"><label for="enable_onlinecomics_code">
              <input type="checkbox" name="mangapress_options[oc_code_insert]" id="enable_onlinecomics_code" value="1" <?php checked('1', $mp_options['oc_code_insert']); ?>/>
              Enable OnlineComics.net PageScan codes.</label></td>
        </tr>
        <tr>
          <th><label for="ocn_comic_ID">OnlineComics.net Comic ID: </label></th>
          <td><input type="text" name="mangapress_options[oc_comic_id]" id="ocn_comic_ID" size="6" onkeyup="jQuery('.ocn_ID').html(this.value)" value="<?php echo $mp_options['oc_comic_id']?>" /></td>
        </tr>
        <tr>
          <th>Opening Tag:</th>
          <td><code>&lt;!-- OnlineComics.net</code> <span class="ocn_ID" style="color:#063"><?php echo $mp_options['oc_comic_id']?></span> <code>start --&gt;</code></td>
        </tr>
        <tr>
          <th>Closing Tag:</th>
          <td><code>&lt;!-- OnlineComics.net</code> <span class="ocn_ID" style="color:#F00"><?php echo $mp_options['oc_comic_id']?></span> <code>end --&gt;</code></td>
        </tr>
        <tr>
        	<td colspan="2">&nbsp;</td>
        </tr>
      </table>
      </div>
  </form>
</div>

<?php
}

function remove_mangapress() {
	$msg = '';
	
	if ( ! current_user_can('delete_plugins') )
		wp_die(__('You do not have sufficient permissions to remove this plugin!'));

	if ( count( $_POST ) > 0 ){
		if($_POST['action'] == 'uninstall_mangapress') $msg = mangapress_uninstall();
	}
?>
<script type="text/javascript">
	jQuery(function() {
		jQuery('#row_confirm').hide();
	});
</script>
<div class="wrap">
    <div id="mangapress_uninstall">
    	<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" id="mangapress_uninstall_form">
        <?php wp_nonce_field('mangapress-uninstall-form'); ?>
		<h3>Uninstall Manga+Press</h3>
        <p class="description">Use this section to completely remove Manga+Press from your Wordpress database. Please be aware that this will remove all of Manga+Press's options
        as well as the tables Manga+Press creates in your database. Only use this option if you're sure you wish to remove Manga+Press from your Wordpress installation.</p>
       <fieldset class="options">
        <table class="form-table">
        <input type="hidden" name="action" value="uninstall_mangapress" />
            <tr>
                <td><label>Remove Manga+Press? <input type="button" name="uninstall" id="uninstall_btn" value="Yes" onclick="jQuery('#row_confirm').show('slow')" class="yes-btn" style=" background: #6F9; border: 1px solid green!important; width: 200px;" /></label></td>
            </tr>
            <tr>
            	<td><div id="row_confirm" style="border: 1px solid red; padding: 5px; margin: 5px; width: 50%;"><label>Confirm. Really Remove Manga+Press? <input type="submit" id="confirm_btn" value="Yes, Remove Manga+Press" class="remove-btn" style="background:#FCC; border: 1px solid red!important; width: 200px;" /></label> <input type="button" name="cancel" id="cancel_btn" value="Cancel" onclick="jQuery('#row_confirm').hide('slow')" /></div></td>
            </tr>
            <tr>
                <td><div id="confirm_message"><?php echo $msg; ?></div></td>
            </tr>
        </table>
        </fieldset>
        </form>
    </div>
</div>

<?php

}

function upgrade_mangapress() {
	$msg = '';
		
	if ( ! current_user_can('update_plugins') )
		wp_die(__('You do not have sufficient permissions to remove this plugin!'));

	if ( count($_POST) > 0 ){
		if ($_POST['action'] == 'upgrade_mangapress') $msg = mangapress_upgrade();
	}
?>
<script type="text/javascript">
	jQuery(function() {
		jQuery('#row_confirm').hide();
	});
</script>
<div class="wrap">
    <div id="mangapress_uninstall">
    	<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" id="mangapress_upgrade_form">
        <?php wp_nonce_field('mangapress-upgrade-form'); ?>
        <h3>Upgrade Manga+Press</h3>
        <p class="description">Use this section to upgrade Manga+Press from a previous version. Make sure to back up your Wordpress database before proceeding!</p>
        <fieldset class="options">
        <table class="form-table">
        <input type="hidden" name="action" value="upgrade_mangapress" />
            <tr>
                <td><label>Upgrade Manga+Press? <input type="button" name="upgrade" id="upgrade_btn" value="Yes" onclick="jQuery('#row_confirm').show('slow')" class="yes-btn" style="background: #6F9; border: 1px solid green!important; width: 200px;" /></label></td>
            </tr>
            <tr>
            	<td><div id="row_confirm" style="border: 1px solid red; padding: 5px; margin: 5px; width: 50%;"><label>Confirm. Upgrade Mangapress? <input type="submit" id="confirm_btn" value="Yes, Upgrade" class="remove-btn" style="background:#FCC; border: 1px solid red!important; width: 200px;" /></label> <input type="button" name="cancel" id="cancel_btn" value="Cancel" onclick="jQuery('#row_confirm').hide('slow')" /></div></td>
            </tr>
            <tr>
                <td><div id="confirm_message"><?php echo $msg; ?></div></td>
            </tr>
        </table>
        </fieldset>
        </form>
    </div>
</div>

<?php 
}
?>