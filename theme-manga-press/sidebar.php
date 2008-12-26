<?php if (is_home() || is_archive()):?>
	
<a href="<?php bloginfo('rss2_url')?>" rel="rss bookmark" title="Subscribe to news-feed!" class="subscribe-link"><img src="<?php bloginfo('stylesheet_directory') ?>/images/feed.png" class="feed-icon" alt="Subscribe!" /> Subscribe to RSS for news and updates!</a><br />

	<?php get_calendar(false); ?>
<?php endif; ?>
<ul>
 <li id="categories"><h3><?php _e('Categories'); ?></h3>
	<ul>
	 <?php wp_list_categories('title_li=&depth=-1&hide_empty=0'); ?>
	</ul>
 </li>

 <li id="archives"><h3><?php _e('Archives'); ?></h3>
	<ul>
	 <?php wp_get_archives('type=monthly'); ?>
	</ul>
 </li>
 
 <li id="meta"><h3><?php _e('Meta'); ?></h3>
	<ul>
		<?php wp_register(); ?>
		<li><?php wp_loginout(); ?></li>
		<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS'); ?>" class="rss"><?php _e('<abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
		<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('The latest comments to all posts in RSS'); ?>" class="rss"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
		<li><a href="http://validator.w3.org/check/referer" title="<?php _e('This page validates as XHTML 1.0 Transitional'); ?>" class="valid"><?php _e('Valid <abbr title="eXtensible HyperText Markup Language">XHTML</abbr>'); ?></a></li>
		<li><a href="http://gmpg.org/xfn/"><abbr title="XHTML Friends Network">XFN</abbr></a></li>
		<li><a href="http://wordpress.org/" title="<?php _e('Powered by WordPress, state-of-the-art semantic personal publishing platform.'); ?>"><abbr title="WordPress">WP</abbr></a></li>
		<?php wp_meta(); ?>
	</ul>
 </li>
</ul>