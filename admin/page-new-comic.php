<?
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
?>
<?php
if (count($_FILES) != 0 || count($_POST) != 0) { $status	=	upload_comic($_FILES); }

$messages[1] = 'Comic added.';
$messages[8] = 'Mime-type not allowed.';
$messages[9] = 'File is too big. Must not be larger than 500kb.';
$messages[10] = 'Comic not added. Unable to upload file.';

if (isset($status)) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$status]; ?></p></div>
<?php unset($status); ?>
<?php endif; ?>
<div class="wrap">
<h2>Post New Comic</h2>
	<form enctype="multipart/form-data" action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
		<table width="80%" cellpadding="2" cellspacing="2" border="0">
			<tr>
				<th>Title:</th><td><input type="text" name="title" id="title" /><input type="hidden" name="MAX_FILE_SIZE" value="500000" />
				<input type="hidden" name="action" value="upload" /></td>
			</tr>
			<tr>
				<th>Series:</th>
				<td>
					<select name="categories[2]">
<?php
	$categories	=  get_categories('child_of='.$mp_options[latestcomic_cat].'&hide_empty=0'); 
	foreach ($categories as $cat) {
		$option = "\t\t\t\t\t\t<option value=\"".$cat->cat_ID."\"$sel>";
		$option .= $cat->cat_name;
		$option .= "</option>\n";
		echo $option;
	}
 ?>
					</select>
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