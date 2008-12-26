<?php
/*
	category.php
*/
?>
<?php get_header(); ?>
    <div id="wrapper-inner">

	<?php
		if (is_comic_cat() ){
			$class = "wide";
		} else {
			$class = "narrow";
		}
	?>
    <div id="posts" class="<?=$class?>">
	<?php if (have_posts()) :?>

		<?php if (is_comic_cat()) {	query_posts($query_string . '&showposts=1'); }?>
	    <?php while (have_posts()) : the_post(); ?>
		<?php  /**
			 	* since this is the single article template let's check
			 	* and see if this article is a comic..
			 	**/ ?>

		<?php if (is_comic_cat()) {	wp_comic_navigation($id); } ?>

            
		 
        	<div class="the-post" id="post-<?php the_ID()?>">
                    <h2><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="bookmark"><?php the_title() ?></a></h2>
                    <span class="post-meta"><img src="<?php bloginfo('stylesheet_directory') ?>/images/date.png" alt="#" /> <?php the_date() ?> @ <?php the_time()?> by <?php the_author(); ?> 
                     <img src="<?php bloginfo('stylesheet_directory') ?>/images/folder.png" alt="_\" />Filed under: <?php the_category(', ') ?>&nbsp;<?php the_tags('Tags: ', ', ', '<br />'); ?>&nbsp;&nbsp;
					<?php $add = "<img src=\"".get_bloginfo('stylesheet_directory')."/images/comment_edit.png\" alt=\"#\" />"; ?>
                    <?php $one = "<img src=\"".get_bloginfo('stylesheet_directory')."/images/comment.png\" alt=\"#\" />"; ?>
                    <?php $more = "<img src=\"".get_bloginfo('stylesheet_directory')."/images/comments.png\" alt=\"#\" />"; ?> 
                    <?php comments_popup_link($add.' Add Comment &#187;', $one.' 1 Comment &#187;', $more.' % Comments &#187;'); ?></span>
    
                    <div class="the-content">
                        <?php the_excerpt();?>
                    </div>
                                    
            </div>

            <p class="post-sep clear"></p>

    	<?php endwhile;?>
            <?php if (!is_comic_cat()): // so we don't display page navigation... ?>
            <div class="posts-nav">
				<?php posts_nav_link(); ?>
            </div>
            <?php endif; ?>
	<?php else: ?>

   		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>

    <?php endif;?>
	</div>
	<?php // if this isn't the comic category, display side-bar ?>
	<?php if (!is_comic_cat()): ?>
    <div id="sidebar">
	    <?php get_sidebar(); ?>
	</div>
	<?php endif; ?>
	<br class="clear"/>

</div>

<?php get_footer(); ?>