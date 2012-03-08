<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('You are not allowed to call this page directly.');

if ( ! current_user_can('manage_options') )
    wp_die(
        __(
            'You do not have sufficient permissions '
            . 'to manage options for this blog.',
            'mangapress'
        )
    );

    $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'basic' );
?>
<script type="text/javascript">
     SyntaxHighlighter.all()
</script>
<div class="wrap">
    <?php $this->options_page_tabs() ?>
    
    <form action="options.php" method="post" id="mangapress_options_form">
        <?php settings_fields('mangapress_options'); ?>
        
        <?php do_settings_sections("mangapress_options-{$tab}"); ?>
                
        <p>
            <input name="mangapress_options[submit-<?php echo $tab; ?>]" type="submit" class="button-primary" value="<?php esc_attr_e('Save Settings', 'mangapress'); ?>" />
            <input name="mangapress_options[reset-<?php echo $tab; ?>]" type="submit" class="button-secondary" value="<?php esc_attr_e('Reset Defaults', 'mangapress'); ?>" />
        </p>
        
    </form>
</div>
