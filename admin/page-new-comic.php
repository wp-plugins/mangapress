<?
/**
 * @package Manga_Press
 * @subpackage Page_New_Comic
 */
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
?>
<?php
if (count($_FILES) != 0 || count($_POST) != 0) { $status	=	add_comic($_FILES, $_POST); }

if (isset($status)) : ?>
<div id="message" class="updated fade"><p><?php echo $status; ?></p></div>
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

<div id="mp_back" class="wrap">
<h2>Post New Comic</h2>
	<form enctype="multipart/form-data" action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
	<input type="hidden" name="MAX_FILE_SIZE" value="500000" />
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
                            <?php generate_category_checklist(0, $mp_options[latestcomic_cat], false ) ?>
                        </ul>
                    <input type="hidden" name="post_category[]" value="<?=$mp_options[latestcomic_cat]?>" />
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