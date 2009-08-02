<?php
/**
 * @package Manga_Press
 * @subpackage Page_Comic_Options
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
?>
<div id="mp_back" class="wrap">
<?php
if (count($_POST) != 0 && $_POST[action] != 'uninstall_mangapress') { $status = update_options($_POST); }
	
$messages[0] = 'Options not updated.';
$messages[1] = 'Options updated.';

if (isset($status)) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$status]; ?></p></div>
<?php unset($status); ?>
<?php endif; ?>

<script type="text/javascript">
	/* <![CDATA[ */
	jQuery(function() {
		jQuery(document).ready(function(){
			jQuery('#mp_back').tabs({ fxFade: true, fxSpeed: 'fast' });
		});
	});
	/* ]]> */
</script>
    <h2>Manga+Press Options</h2>   

    <ul id="tabs" class="ui-tabs-nav">
        <li><a href="#basic_options">Basic Options</a></li>
        <li><a href="#image_options">Image Options</a></li>
        <li><a href="#update_notif">Comic Updates Notification</a></li>
    </ul>
    <div id="basic_options" class="ui-tabs-panel">
        <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST" id="basic_options_form">
        <fieldset class="options">
        <h3>Basic Options</h3>
        <p class="submit"><input type="submit" value="Update Options &raquo;" /></p>
        <table class="form-table">
            <input type="hidden" name="action" value="update_options" />
            <?php wp_nonce_field('mp_basic-options-form'); ?>
            <tr>
                <th scope="col">Navigation CSS:</th>
                <td>
                    <select name="nav_css">
                        <option value="default_css" <? if ($mp_options[nav_css] == 'default_css'){ echo " selected=\"selected\""; } ?>>Default</option>
                        <option value="custom_css" <? if ($mp_options[nav_css] == 'custom_css'){ echo " selected=\"selected\"" ; } ?>>Custom</option>
                    </select> 
                    Turn this off. you know you want to!
                </td>
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
        </textarea>
                </td>
            </tr>
            <tr>
                <th scope="col" class="th-full">Order by:</th>
                <td>
                    <select name="order_by">
                        <option value="post_date" <? if ($mp_options[order_by] == 'post_date'){ echo " selected=\"selected\""; } ?>>Date</option>
                        <option value="post_id" <? if ($mp_options[order_by] == 'post_id'){ echo " selected=\"selected\"" ; } ?>>Post ID</option>
                    </select>
                </td>
            </tr>
            <tr>
              <th scope="col" class="th-full">&nbsp;</th>
              <td><label for="insert_nav"><input type="checkbox" name="insert_nav" id="insert_nav" value="1" <?=($mp_options[insert_nav] == 1) ? 'checked="checked" ' : ''; ?> />
              Automatically insert comic navigation code into comic posts.</label></td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <th scope="col" class="th-full"> Comic Category:</th>
                    <td>
                    <select name="latest">
        <?php
            $categories2	=  get_categories('hide_empty=0'); 
            $current_cat2 = $mp_options[latestcomic_cat];
            foreach ($categories2 as $cat) {
                if ($current_cat2 == $cat->cat_ID) {
                    $sel	=	"selected=\"selected\"";
                } else {
                    $sel	=	"";
                }
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
                  <td><label for="exclude_comic_cat"><input type="checkbox" name="exclude_comic_cat" id="exclude_comic_cat" value="1" <?=($mp_options[comic_front_page] == 1) ? 'checked="checked" ' : ''; ?> />Exclude comic category from front page.</label></td>
                </tr>
                
                <tr>
                  <th scope="col" class="th-full">Latest Comic Page</th>
                  <td>
                    <select name="latest_page">
                        <option value="">&nbsp;</option>
        <?php
            
            $pages	=  get_pages();
            $current_page = $mp_options[latestcomic_page];
            foreach ($pages as $page) {
                if ($current_page == $page->ID) {
                    $sel	=	"selected=\"selected\"";
                } else {
                    $sel	=	"";
                }
        
                $option = "\t\t\t\t<option value=\"".$page->ID."\" $sel>";
                $option .= $page->post_title;
                $option .= "</option>\n";
                echo $option;
            }
         ?>
                    </select>
         
                  <span class="description">Sets a page for displaying the most recent comic.</span>
                  
                  </td>
                </tr>
                <tr>
                  <th scope="col" class="th-full">Comic Archive Page</th>
                  <td>
                    <select name="archive_page">
                        <option value="">&nbsp;</option>
        <?php
            
            $pages	=  get_pages();
            $current_page = $mp_options[comic_archive_page];
            foreach ($pages as $page) {
                if ($current_page == $page->ID) {
                    $sel	=	"selected=\"selected\"";
                } else {
                    $sel	=	"";
                }
        
                $option = "\t\t\t\t<option value=\"".$page->ID."\" $sel>";
                $option .= $page->post_title;
                $option .= "</option>\n";
                echo $option;
            }
         ?>
                    </select>
         
                  <span class="description">Sets a page for displaying the comic archive page. CANNOT be the same as your Latest Comic page.</span>
                  
                  </td>
                </tr>
        
        </table>	
        </fieldset>
        </form>
    </div>   
    <div id="image_options" class="ui-tabs-panel ui-tabs-hide">
        <form action="<?=$_SERVER['REQUEST_URI']?>" method="post" id="image_options_form">
        <?php wp_nonce_field('mp_basic-options-form'); ?>        
        <fieldset class="options" >
        <legend><h3>Image Options</h3></legend>
        <p class="description">This section controls banner and thumbnail generation for comic pages.</p>
        <p class="submit"><input type="submit" value="Set Image Options &raquo;" /></p>
          <table class="form-table">
            <input type="hidden" name="action" value="set_image_options" />
          	<tr>
            	<th class="th-full"><label for="make_thumb"><input type="checkbox" name="make_thumb" id="make_thumb" value="1" <?=($mp_options[make_thumb] == 1) ? 'checked="checked" ' : ''; ?> />
                Generate Thumbnail for Comic Page (thumbnail size can be set in <a href="options-media.php">Wordpress Settings &gt; Media</a>)</label></th>
            </tr>
          	<tr>
          	  <th class="th-full"><label for="insert_banner"><input type="checkbox" name="insert_banner" id="insert_banner" value="1" <?=($mp_options[insert_banner] == 1) ? 'checked="checked" ' : ''; ?> /> 
          	    Insert banner on home page. </label><span class="description">Automatically inserts comic banner html at the start of The Loop on the home page.</span></th>
       	    </tr>
          </table>
        </fieldset>
        <fieldset class="options">
          <legend><h4>Set Banner Width and Height</h4></legend>
          <p class="description">Sets the size of the comic banner displayed on the front page. Remember to adjust any CSS sizing used to the values below!</p>
          <table class="form-table">
          <tr>
                <th><label for="banner_width">Banner Width:</label></th><td><label><input type="text" size="6" name="banner_width" id="banner_width" value="<?=$mp_options[banner_width]?>" /> pixels</label></td>
            </tr>
            <tr>
                <th><label for="banner_height">Banner Height:</label></th><td><label><input type="text" size="6" name="banner_height" id="banner_height" value="<?=$mp_options[banner_height]?>" /> pixels</label></td>
            </tr>
          </table>
         </fieldset>
        </form>
    </div>
    <div id="update_notif" class="ui-tabs-panel ui-tabs-hide">
        <form action="<?=$_SERVER['REQUEST_URI']?>" method="post" id="comic_updates_options_form">
        <?php wp_nonce_field('mp_basic-options-form'); ?>

        	<input type="hidden" name="action" value="set_comic_updates" />
            <fieldset class="options" >
                <legend><h3>Comic Updates Notification</h3></legend>
                <p class="description">This section is for custom code that you wish to insert into your comic post. For example, the custom html comments that <a href="http://www.onlinecomics.net/">OnlineComics.net</a> requires for it's PageScan comic updates service.</p>
                <p class="submit"><input type="submit" value="Save Comic Update Notifications &raquo;" /></p>
					<table class="form-table">
                    	<tr>
                    	  <th colspan="2"><h4>TheWebComicList.com Code:</h4></th>
                  	  </tr>
                    	<tr>
                    	  <td colspan="2"><span class="description">This options inserts an html comment which contains the date of the most recent comic near the beginning of the content section (usually The Loop). This is sometimes needed when TWC has a hard time detecting the status of the comic.</span></td>
                  	  </tr>
                    	<tr>
                    	  <td colspan="2"><label>
                    	    <input name="enable_twc_date_code" type="checkbox" id="enable_twc_date_code" value="1" <?=((bool)$mp_options[twc_code_insert])?'checked="checked"':''?>  />
                   	      Enable TWC date stamp comment</label></td>
                  	  </tr>
                    	<tr>
                    	  <td colspan="2">&nbsp;</td>
                  	  </tr>
                    	<tr>
                        	<th colspan="2"><h4>OnlineComics.net Code: </h4></th>
                        </tr>
                        <tr>
                          <td colspan="2"><span class="description">This option is to be used with comics that are listed in the OnlineComics.net directory <em>and</em> have the PageScan option enabled.</span></td>
                        </tr>
                        <tr><td colspan="2"><label for="enable_onlinecomics_code"><input type="checkbox" name="enable_onlinecomics_code" id="enable_onlinecomics_code" value="1" <?=((bool)$mp_options[oc_code_insert])?'checked="checked"':''?> />
                          Enable OnlineComics.net PageScan codes.</label></td></tr>
                        <tr>
                        	<th><label for="ocn_comic_ID">OnlineComics.net Comic ID: </label></th>
                            <td><input type="text" name="ocn_comic_ID" id="ocn_comic_ID" size="6" onkeyup="jQuery('.ocn_ID').html(this.value)" value="<?php echo $mp_options[oc_comic_id]?>" /> </td>
						</tr>
                    	<tr>
                        	<th>Opening Tag:</th>
                            <td><code>&lt;!-- OnlineComics.net</code> <span class="ocn_ID" style="color:#063"><?php echo $mp_options[oc_comic_id]?></span> <code>start --&gt;</code></td>
                        </tr>
                    	<tr>
                        	<th>Closing Tag:</th>
                            <td><code>&lt;!-- OnlineComics.net</code> <span class="ocn_ID" style="color:#F00"><?php echo $mp_options[oc_comic_id]?></span> <code>end --&gt;</code></td>
                        </tr>
					</table>                
            </fieldset>
        </form>
    </div>
</div>