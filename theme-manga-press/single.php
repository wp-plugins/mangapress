<?php
/*
	single.php
*/
?>
<?php get_header(); ?>
    <div id="wrapper-inner">
	
    <div id="posts" class="wide">
	<?php if (have_posts()) :?>
	    <?php while (have_posts()) : the_post(); ?>

		<?php  /**
			 	* since this is the single article template let's check
			 	* and see if this article is a comic..
			 	**/ ?>

		<?php if (is_comic()) { wp_comic_navigation($id); }?>

            
		 
        	<div class="the-post" id="post-<?php the_ID()?>">
                    <h2><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="bookmark"><?php the_title() ?></a></h2>
                    <span class="post-meta"><img src="<?php bloginfo('stylesheet_directory') ?>/images/date.png" alt="#" /> <?php the_date() ?> @ <?php the_time()?> by <?php the_author(); ?> 
                     <img src="<?php bloginfo('stylesheet_directory') ?>/images/folder.png" alt="_\" />Filed under: <?php the_category(', ') ?>&nbsp;<?php the_tags('Tags: ', ', ', '<br />'); ?>&nbsp;&nbsp;
					<?php $add = "<img src=\"".get_bloginfo('stylesheet_directory')."/images/comment_edit.png\" alt=\"#\" />"; ?>
                    <?php $one = "<img src=\"".get_bloginfo('stylesheet_directory')."/images/comment.png\" alt=\"#\" />"; ?>
                    <?php $more = "<img src=\"".get_bloginfo('stylesheet_directory')."/images/comments.png\" alt=\"#\" />"; ?> 
                    <?php comments_popup_link($add.' Add Comment &#187;', $one.' 1 Comment &#187;', $more.' % Comments &#187;'); ?></span>
    
                    <div class="the-content">
                        <?php the_content('Read moar &raquo;');?>
                    </div>
                                    
            </div>
           	<?php comments_template(); ?>

    	<?php endwhile;?>
            <?php if (!is_comic()): // so we don't display page navigation... ?>
            <div class="posts-nav">
				<?php previous_post_link('&laquo; %link') ?>&nbsp;::&nbsp;<?php next_post_link('%link &raquo;') ?>
            </div>
            <?php endif; ?>
	<?php else: ?>

   		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>

    <?php endif;?>
	</div>
	<br class="clear"/>
</div>

<?php get_footer(); ?>