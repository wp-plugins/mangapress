<?
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
?>
<?php
if (count($_FILES) != 0 || count($_POST) != 0) { $status	=	add_comic($_FILES, $_POST); }

if (isset($status)) : ?>
<div id="message" class="updated fade"><p><?php echo $status; ?></p></div>
<?php unset($status); ?>
<?php endif; ?>
<div class="wrap">
<h2>Post New Comic</h2>
	<form enctype="multipart/form-data" action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
	<input type="hidden" name="MAX_FILE_SIZE" value="500000" />
	<input type="hidden" name="action" value="wp_handle_upload" />
		<table>
			<tr>
				<th>Title:</th><td><input type="text" name="title" id="title" /></td>
			</tr>
			<tr>
				<th>Series:</th>
				<td>
                <?php wp_dropdown_categories('name=categories[2]&child_of='.$mp_options[latestcomic_cat].'&hierarchical=1&hide_empty=0&depth=1'); ?> 
				<input type="hidden" name="categories[0]" value="<?=$mp_options[latestcomic_cat]?>" />
				</td>
			</tr>
			<tr>
				<th>Comic:</th><td><input name="userfile" id="userfile" type="file" /></td>
			</tr>
			<tr>
			  <th>Banner:</th>
			  <td><input name="bannerfile" type="file" /></td>
		  </tr>
			<tr>
				<td colspan="2" align="left"><input type="submit" value="Update Comic" /></td>
			</tr>
		</table>
	</form>
</div>