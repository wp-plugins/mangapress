<?php
/**
 * Template Name: Default Page
 *
 * Looks intimidating, doesn't it?
 *
***/
?>
<?php get_header(); ?>
    <div id="wrapper-inner">
	<?php
		// because $id isn't available outside of The Loop,
		// we have to it this way...
		$id=$post->ID;

		if (is_comic_page() ){
			$class = "wide";
		} else {
			$class = "narrow";
		}
	?>
    <div id="posts" class="<?=$class?>">
	<?php if (is_comic_page()):?>
    
        <?php 
            $latest = wp_comic_last();
            if (!$latest): // if no comics... ?>
            <div class="the-post" id="post-<?php the_ID()?>">          
                <h2 style="text-align: center;"><?php the_title() ?>: <?=$post_title?></h2>
                <div class="the-content" style="text-align: center;">
               Sorry, no comics have been posted. Check back later.
                </div>
            </div>
        <?php else: // well, there is a comic...
        		get_comic_post($latest); 
			/**
			 * The HTML comment below is needed to tell
			 * comic update services like TheWebComicList.com
			 * that your comic has been updated.
			**/
		?>
        <!--Last Update: <?=date('d/m/Y', strtotime($post_date)); ?> -->
			<?php wp_comic_navigation($latest); ?>
            <div class="the-post" id="post-<?php the_ID()?>">          
                <h2 style="text-align: center;"><?php the_title() ?>: <?=$post_title?></h2>
                <div class="the-content" style="text-align: center;">
                	<?php 
						/**
						 * If you use OnlineComics.net to automatically 
						 * record comic updates, place your code here...
						**/
					?>

                    <?=$post_content?>

                    <?php // ...and here ?>
                </div>
    
                <br class="clear" />
    
                <div class="comments-popup">
                      <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>
                </div>                    
    
            </div><!-- Closing DIV tag for <div id="post"> -->
            <?php comments_template(); ?>
    
		<?php endif; // closing if ( !$latest )... ?>

    </div>
        
	<?php elseif(is_comic_archive_page()): // this is the Past Comics, or Comic Archives page ?>

        <div class="the-post" id="post-<?php the_ID()?>">          
            <h2><?php the_title() ?></h2>
    	        <ul>
    	<?php //custom loop...this is why Comics should have their own category ?>
        <?php
			global $post;
			$posts = get_posts( 'numberposts=20&category='.wp_comic_category_id() );
			if (!empty($posts)): foreach( $posts as $post ): setup_postdata( $post );
		?>
        	<li><?php the_time('F j, Y')?> :: <a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="bookmark archive"><?php the_title() ?></a></li>
        <?php endforeach; endif; ?>
	        </ul>
        </div>
        
    </div><!-- Closing DIV tag for <div id="post"> -->

    <div id="sidebar">
        <?php get_sidebar(); ?>
    </div>

    <?php else: // just a normal Wordpress Page ?>
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    
		<?php /* regular Wordpress template processing...*/ ?>
    
        <div class="the-post" id="post-<?php the_ID()?>">          
            <h2><?php the_title() ?></h2>
                <div class="the-content">
                    <?php the_content('');?>
                </div>
                <br class="clear" />
            
            </div>
              
    </div><!-- Closing DIV tag for <div id="post"> -->

    <div id="sidebar">
        <?php get_sidebar(); ?>
    </div>
														

			<?php endwhile; // ending The Loop... ?>
        <?php endif; // closing if ( have_posts() )... ?>
	<?php endif;	// ending if ( if_comic_page() )... ?>
	<br class="clear"/>
    
</div><!-- Closing DIV tag for <div id="wrapper-inner"> -->

<?php get_footer(); ?>