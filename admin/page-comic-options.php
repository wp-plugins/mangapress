<?
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
?>
<?php
if (count($_POST) != 0) { $status = update_options($_POST, $_FILES); }

$messages[0] = 'Options not updated.';
$messages[1] = 'Options updated.';
$messages[2] = 'Series not added.';
$messages[3] = 'Series added.';
$messages[4] = 'Series has already been added.';
$messages[5] = 'Directory name not supplied.';
$messages[6] = 'New directory has been set.';
$messages[7] = 'Directory does not exist.';
$messages[8] = 'Mime-type not allowed.';
$messages[9] = 'File is too big. Must not be larger than 150kb.';
$messages[10] = 'Overlay image not uploaded.';
$messages[11] = 'No file specified.';

if (isset($status)) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$status]; ?></p></div>
<?php unset($status); ?>
<?php endif; ?>
<script type="text/javascript">
	jQuery(function() {
		jQuery('#mp_back > h2 > ul').tabs();
	});
</script>
<div id="mp_back" class="wrap">
    <h2>Manga+Press Options
        <ul id="tabs" class="ui-tabs-nav">
            <li><a href="#basic_options">Basic Options</a></li>
            <li><a href="#comic_dir">Set Comic Directory</a></li>
            <li><a href="#image_options">Banner Options</a></li>
            <li><a href="#image_dimensions">Banner Dimensions</a></li>
        </ul>
    </h2>   
    <div id="basic_options" class="ui-tabs-panel">
        <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST" id="basic_options_form">
        <fieldset class="options">
        <h3>Basic Options</h3>
        <p class="submit"><input type="submit" value="Update Options &raquo;" /></p>
        <table class="form-table">
            <input type="hidden" name="action" value="update_options" />
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
        ul.comic-nav a:link, .mycomic_navigation a:visited { color: #ccc;	text-decoration: none; }
        ul.comic-nav a:hover { text-decoration: none; }
        ul.comic-nav li:before{ content: ""; }
        </textarea>
                </td>
            </tr>
            <tr>
                <th scope="col">Order by:</th>
                <td>
                    <select name="order_by">
                        <option value="post_date" <? if ($mp_options[order_by] == 'post_date'){ echo " selected=\"selected\""; } ?>>Date</option>
                        <option value="post_id" <? if ($mp_options[order_by] == 'post_id'){ echo " selected=\"selected\"" ; } ?>>Post ID</option>
                    </select>
                </td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <th scope="col"> Comic Category:</th>
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
                    this category is for use by the Latest Comic Page to display the most recent comic, as well as a place to store all child categories that represent series.</td>
                </tr>
                <tr>
                  <th scope="col">&nbsp;</th>
                  <td>&nbsp;</td>
                </tr>
                
                <tr>
                  <th scope="col">Latest Comic Page</th>
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
         
                  sets a page for displaying the most recent comic
                  
                  </td>
                </tr>
                <tr>
                  <th scope="col">Comic Archive Page</th>
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
         
                  sets a page for displaying the comic archive page. CANNOT be the same as your Latest Comic page.
                  
                  </td>
                </tr>
        
        </table>	
        </fieldset>
        </form>
    </div>

    <div id="comic_dir" class="ui-tabs-panel ui-tabs-hide">
        <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST" id="comic_dir_form">
        <fieldset class="options">
        <legend><h3>Set Comic Directory</h3></legend>
        <p>New directories can be created through FTP, and <strong>must</strong> be inside your <code>wp-content</code> folder. </p>
        <p class="submit"><input type="submit" value="Set Comic Directory Options &raquo;" /></p>
        <table class="form-table">
            <input type="hidden" name="action" value="set_dir" />
            <tr>
                <th>Current Directory:</th><td>/<?=$mp_options[comic_dir]?></td>
           </tr>
           <tr>
                <th>New Directory:</th><td><input type="text" size="35" name="new_dir" />(no leading slashes) leave blank if you don't wish to change the default directory</td>
            </tr>
            <tr>
                <?
                    $ischecked	=	($mp_options[series_organize] == 1) ? 'checked="checked" ' : '';
                ?>
                <td colspan="2" align="center"><label for="organize_by_series"><input type="checkbox" name="organize_by_series" id="organize_by_series" value="1" <?=$ischecked;?>/>
                Organize comics into series-based folders</label></td> 
            </tr>
        </table>
        </fieldset>
        </form>
    </div>
    <? if (function_exists('gd_info')) { ?>
    <div id="image_options" class="ui-tabs-panel ui-tabs-hide">
        <form enctype="multipart/form-data" action="<?=$_SERVER['../REQUEST_URI']?>" method="post" id="image_options_form">
        <fieldset class="options" >
        <legend>
        <h3>Banner Options</h3></legend>
        <p>GD Library must be enabled to use the banner creation options. If an overlay image is uploaded, the overlay image will determine the size of the banner. Banner Skin must be a 24bit Alpha transparency PNG. </p>
        <p class="submit"><input type="submit" value="Set Banner Options &raquo;" /></p>
          <table class="form-table">
            <input type="hidden" name="action" value="set_image_options" />
            <input type="hidden" name="MAX_FILE_SIZE" value="150000" />
          <?
            //$isdisabled = ($mp_options[make_banner] == 0) ? 'disabled="disabled" ' : '';
            //$isdisabled	= ($mp_options[use_overlay] == 1) ? 'disabled="disabled" ' : '';
            if (!$mp_options[make_banner]){ $hasoptions = 'disabled="disabled" '; }
            if (!$mp_options[make_banner]) { $dimensions = 'disabled="disabled" '; }
          ?>
            <tr>
              <th colspan="2" class="th-full"><label for="make_banner"><input type="checkbox" name="make_banner" id="make_banner" value="1" <?=($mp_options[make_banner] == 1) ? 'checked="checked" ' : '';?>/>
              Create Banner.</label></th>
            </tr>
            <tr>
                <th colspan="2" class="th-full"><label for="use_overlay_image"><input type="checkbox" name="use_overlay" id="use_overlay_image" value="1" <?=($mp_options[use_overlay] == 1) ? 'checked="checked" ' : '';?> <?=$hasoptions?>/>
                Use Banner Skin.</label></th> 
            </tr>
            <tr>
              <th>Current Banner Skin:</th>
              <td>
        <? 
            if ($mp_options[use_overlay]) {
                echo empty($mp_options[banner_overlay]) ? 'No banner skin image specified. Please upload an image.' : '<img src="'.$mp_options[banner_overlay].'" />';
            } else {
                echo "Banner skin are disabled.";
            }
        ?>	 </td>
            </tr>
            <tr>
              <th>Upload Banner Skin:</th>
              <td><input name="overlay_image" id="overlay_image" type="file" <?=$hasoptions?>/></td>
            </tr>
          </table>
        </fieldset>
        </form>
    </div>

    <div id="image_dimensions" class="ui-tabs-panel ui-tabs-hide">
        <form action="<?=$_SERVER['REQUEST_URI']?>" method="post" id="image_dimensions_form">
        <fieldset class="options">
        <legend><h3>Set Banner Width and Height</h3></legend>
        <p>This section is used only when a banner skin has not been specified.</p>
        <p class="submit"><input type="submit" value="Set Banner Dimensions &raquo;" <?=$dimensions?>/></p>
          <table class="form-table">
          <input type="hidden" name="action" value="set_image_dimensions" />
            <tr>
                <th>Banner Width:</th><td><input type="text" size="6" name="banner_width" value="<?=$mp_options[banner_width]?>" <?=$dimensions?>/></td>
            </tr>
            <tr>
                <th>Banner Height:</th><td><input type="text" size="6" name="banner_height" value="<?=$mp_options[banner_height]?>" <?=$dimensions?>/></td>
            </tr>
          </table>
         </fieldset>
        </form>
    </div>
    <? } ?>
</div>