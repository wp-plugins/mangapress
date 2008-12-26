<?php
/**
 *	Template Name: Index.php
 *
 *	index.php
 *	starting here because single.-, category.- and page.php
 *	are going to be based off this file
 *
***/
?>
<?php get_header(); ?>
    <div id="wrapper-inner">
    <span id="comic-banner">
    	<?php
			$latest = wp_comic_last();
			if ((int)$latest) {
				get_comic_post ( $latest );
				echo "<h1><a href=\"".get_permalink( $latest )."\" title=\"".get_the_title( $latest )."\">".get_the_title( $latest )."</a></h1>";
				echo $post_excerpt;
			}
		?>
    </span>
    
    <div id="posts" class="narrow">
	<?php if (have_posts()) :?>
	<?php query_posts($query_string . "&cat=-".wp_comic_category_id()); ?>
	    <?php while (have_posts()) : the_post(); ?>      
        	<div class="the-post" id="post-<?php the_ID()?>">
            <?php wp_calendar_icon($post->post_date) ?>
            	<div class="the-post-wrapper">
                    <h2><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="bookmark"><?php the_title() ?></a></h2>
                    <span class="post-meta"><img src="<?php bloginfo('stylesheet_directory') ?>/images/date.png" alt="#" /> <?php the_date() ?> @ <?php the_time()?> by <?php the_author(); ?> 
                     <img src="<?php bloginfo('stylesheet_directory') ?>/images/folder.png" alt="_\" />Filed under: <?php the_category(', ') ?>&nbsp;<?php the_tags('Tags: ', ', ', '<br />'); ?>&nbsp;&nbsp;
					<?php $add = "<img src=\"".get_bloginfo('stylesheet_directory')."/images/comment_edit.png\" alt=\"#\" />"; ?>
                    <?php $one = "<img src=\"".get_bloginfo('stylesheet_directory')."/images/comment.png\" alt=\"#\" />"; ?>
                    <?php $more_com = "<img src=\"".get_bloginfo('stylesheet_directory')."/images/comments.png\" alt=\"#\" />"; ?> 
                    <?php comments_popup_link($add.' Add Comment &#187;', $one.' 1 Comment &#187;', $more_com.' % Comments &#187;'); ?></span>
    
                    <div class="the-content">
                        <?php the_content('Read moar &raquo;');?>
                    </div>
                                    
				</div>
            </div>

            <p class="post-sep clear"></p>
            
    	<?php endwhile;?>
            <div class="posts-nav">
				<?php posts_nav_link(); ?>
            </div>
	<?php else: ?>

   		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>

    <?php endif;?>
	</div>


    <div id="sidebar">
	    <?php get_sidebar(); ?>
	</div>
	<br class="clear"/>
</div>
<?php get_footer(); ?>