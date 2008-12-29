<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }?>
<?php
	// get feed_messages
	require_once(ABSPATH . WPINC . '/rss.php');
	$mpCheck = new CheckPlugin();	
	$mpCheck->URL 	= MP_HOME_PAGE_URL;
	$mpCheck->version = MP_VERSION;
	$mpCheck->name 	= "mpPlugin";
	$mpCheck->period = 129600; // let's check every 36 hours...
?>

<div id="mangapress-wrap" class="wrap">
  <h2>Manga+Press Dashboard</h2>
  <br class="clear"/>
  <?php if ( $mpCheck->startCheck() ) { ?>
  <div id="newversion">
    <h3 class="dashboard-widget-title">New version of Manga+Press available !!!</h3>
    <div class="dashboard-widget-content">Manga+Press 1.0 RC2 is available for download: <a href="http://wordpress.org/extend/plugins/mangapress/" target="_blank">Download here</a></div>
  </div>
  <?php } ?>
  <div id="latest-news">
    <h3><span class="alignleft title-text">Right Now</span><span class="alignright"><a href="?page=post_comic" class="rbutton"><strong>Post New Comic</strong></a> <a href="?page=manage-series" class="rbutton"><strong>Manage Series</strong></a></span>
    <br class="clear" />
    </h3>
  </div>
  <br class="clear" />
  
  <div id="dashboard-widgets-wrap">
    <div id="dashboard-widgets">
      <div id="post-body" class="has-sidebar">
        <h3 class="dashboard-widget-title">
          <?php _e('Latest News') ?>
        </h3>
        <div id="dashboard_primary" class="postbox">
			<?php
				$rss = @fetch_rss('http://www.dumpster-fairy.com/?tag=mangapress&feed=rss2');
				
				if ( isset($rss->items) && 0 != count($rss->items) ) {
					$rss->items = array_slice($rss->items, 0, 4);
            ?>
			<ul>
			<?php foreach ($rss->items as $item): ?>
              <li><a class="rsswidget" title="" href='<?php echo wp_filter_kses($item['link']); ?>'><?php echo wp_specialchars($item['title']); ?></a> <span class="rss-date"><?php echo date("F jS, Y", strtotime($item['pubdate'])); ?></span>
                <div class="rssSummary"><strong><?php echo human_time_diff(strtotime($item['pubdate'], time())); ?></strong> - <?php echo $item['description']; ?></div>
              </li>
			<?php endforeach; ?>		
            </ul>
			<?php } else { ?>
          <p><?php printf(__('Newsfeed could not be loaded.  Check the <a href="%s">front page</a> to check for updates.'), 'http://www.dumpster-fairy.com/') ?></p>
          <?php } ?>
        </div>
      </div>
    </div>
    <div id="side-info-column" class="inner-sidebar">
      <h3 class="dashboard-widget-title">Installed Libraries:</h3>
      <ul>
        <?php
        if (function_exists('gd_info')) {
            $info	=	gd_info();
        ?>
        <li><strong>GD Library</strong> needed for comic banner creation.</li>
        <li><strong>Version</strong>:
          <?=$info['GD Version']?>
        </li>
        <li><strong>FreeType Support</strong>:
          <?= (bool)$info['FreeType Support'] ? 'Enabled' : 'Disabled. Some features in upcoming releases of this plugin may require the FreeType Library.' ?>
        </li>
        <li><strong>GIF Read Support</strong>:
          <?= (bool)$info['GIF Read Support'] ? 'Enabled' : 'Disabled' ?>
        </li>
        <li><strong>GIF Create Support</strong>:
          <?= (bool)$info['GIF Create Support'] ? 'Enabled' : 'Disabled, you can\'t create banners from GIF-based comic images.' ?>
        </li>
        <li><strong>JPG Support</strong>:
          <?= (bool)$info['JPG Support'] ? 'Enabled' : 'Disabled' ?>
        </li>
        <li><strong>PNG Support</strong>:
          <?= (bool)$info['PNG Support'] ? 'Enabled' : 'Disabled' ?>
        </li>
        <?
        } else {
        ?>
        <li>GD Library is not installed. Banner creation features are not available.</li>
        <? } ?>
      </ul>
    </div>
    <br style="clear: both" />
  </div>
</div>
