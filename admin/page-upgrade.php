<?
/**
 * @package Manga_Press
 * @subpackage Page_Upgrade
 */
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
?>
<?php
if ($_POST[action] == 'upgrade_mangapress') {$msg = mangapress_upgrade(); }
?>
<script type="text/javascript">
	jQuery(function() {
		jQuery('#row_confirm').hide();
	});
</script>
<div id="mp_back" class="wrap">
    <div id="mangapress_uninstall">
    	<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" id="mangapress_upgrade_form">
        <?php wp_nonce_field('mangapress-upgrade-form'); ?>
        <fieldset class="options">
        <legend><h3>Upgrade Manga+Press</h3></legend>
        <p>Use this section to upgrade Manga+Press from a previous version. Make sure to back up your Wordpress database before proceeding!</p>
        <table class="form-table">
        <input type="hidden" name="action" value="upgrade_mangapress" />
            <tr>
                <td><label>Upgrade Manga+Press? <input type="button" name="upgrade" id="upgrade_btn" value="Yes" onclick="jQuery('#row_confirm').show('slow')" class="yes-btn" /></label></td>
            </tr>
            <tr>
            	<td><div id="row_confirm"><label>Confirm. Upgrade Mangapress? <input type="submit" id="confirm_btn" value="Yes, Upgrade" class="remove-btn" /></label> <input type="button" name="cancel" id="cancel_btn" value="Cancel" onclick="jQuery('#row_confirm').hide('slow')" /></div></td>
            </tr>
            <tr>
                <td><div id="confirm_message"><?=$msg?></div></td>
            </tr>
        </table>
        </fieldset>
        </form>
    </div>
</div>