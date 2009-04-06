<?
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
?>
<?php
if ($_POST[action] == 'uninstall_mangapress') {$msg = mangapress_uninstall(); }
?>
<script type="text/javascript">
	jQuery(function() {
		jQuery('#row_confirm').hide();
	});
</script>
<div id="mp_back" class="wrap">
    <div id="mangapress_uninstall">
    	<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" id="mangapress_uninstall_form">
        <fieldset class="options">
        <legend><h3>Uninstall Manga+Press</h3></legend>
        <p>Use this section to completely remove Manga+Press from your Wordpress database. Please be aware that this will remove all of Manga+Press's options
        as well as the tables Manga+Press creates in your database. Only use this option if you're sure you wish to remove Manga+Press from your Wordpress installation.</p>
        <table class="form-table">
        <input type="hidden" name="action" value="uninstall_mangapress" />
            <tr>
                <td><label>Remove Manga+Press? <input type="button" name="uninstall" id="uninstall_btn" value="Yes" onclick="jQuery('#row_confirm').show('slow')" class="yes-btn" /></label></td>
            </tr>
            <tr>
            	<td><div id="row_confirm"><label>Confirm. Really Remove Manga+Press? <input type="submit" id="confirm_btn" value="Yes, Remove Manga+Press" class="remove-btn" /></label> <input type="button" name="cancel" id="cancel_btn" value="Cancel" onclick="jQuery('#row_confirm').hide('slow')" /></div></td>
            </tr>
            <tr>
                <td><div id="confirm_message"><?=$msg?></div></td>
            </tr>
        </table>
        </fieldset>
        </form>
    </div>
</div>