<?php
/**
 * @package Manga_Press
 * @subpackage Page_Main
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }?>
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
    <div class="dashboard-widget-content">A new version of Manga+Press is available for download: <a href="http://manga-press.silent-shadow.net/downloads/" target="_blank">Download here</a></div>
  </div>
  <?php } ?>
  <div id="latest-news">
    <h3><span class="alignleft title-text">Right Now</span><span class="alignright"><a href="?page=post_comic" class="rbutton"><strong>Post New Comic</strong></a> <a href="?page=comic-options" class="rbutton"><strong>Options</strong></a></span> <br class="clear" />
    </h3>
  </div>
  <br class="clear" />
  <div id="dashboard-widgets-wrap">
    <div id="dashboard-widgets">
      <div id="post-body">
        <div id="dashboard_primary" class="postbox">
          <h3 class="dashboard-widget-title"> <span>
            <?php _e('Latest News') ?>
            </span> </h3>
          <?php
				if (!defined('MAGPIE_CACHE_ON') ) { define('MAGPIE_CACHE_ON', 0); } // 2.7 Cache Bug 

				$rss = @fetch_rss('http://manga-press.silent-shadow.net/i/updates/feed/');

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
          <p><?php printf(__('Newsfeed could not be loaded.  Check the <a href="%s">front page</a> to check for updates.'), 'http://manga-press.silent-shadow.net/') ?></p>
          <?php } ?>
        </div>
        <div id="dashboard_secondary" class="postbox">
          <h3 class="dashboard-widget-title"><span>What's New in Manga+Press
            <?=MP_VERSION?>
            ?</span></h3>
			  <?php
				$rss = @fetch_rss('http://manga-press.silent-shadow.net/tags/whatsnew/feed/');
				
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
          <p><?php printf(__('Newsfeed could not be loaded.  Check the <a href="%s">front page</a> to check for updates.'), 'http://manga-press.silent-shadow.net/') ?></p>
          <?php } ?>

        </div>
        <?php unset($rss); ?>
          <div id="dashboard_featured" class="postbox">
          <h3 class="dashboard-widget-title"> <span>
            <?php _e('Featured Comics') ?>
            </span> </h3>
          <?php
				$rss = @fetch_rss('http://manga-press.silent-shadow.net/i/featured/feed/');
				
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
          <p><?php printf(__('Newsfeed could not be loaded.  Check the <a href="%s">front page</a> to check for updates.'), 'http://manga-press.silent-shadow.net/') ?></p>
          <?php } ?>
        </div>

      </div>
      <br class="clear" />
    </div>
    <br style="clear: both" />
  </div>
</div>
