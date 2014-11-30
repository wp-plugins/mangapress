<?php
/**
 * MangaPress Default Theme
 *
 * @package Manga_Press
 * @subpackage MPDefault\LatestComic
 * @author Jess Green <jgreen@psy-dreamer.com>
 * @version $Id$
 */
get_header(); ?>
<div id="main-content" class="main-content">
    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
        <?php $latest_comic = mpp_get_latest_comic();
        if ($latest_comic->have_posts()) :
            while ($latest_comic->have_posts()) : $latest_comic->the_post(); ?>


                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <?php twentyfourteen_post_thumbnail(); ?>
                    <?php the_title( '<header class="entry-header"><h1 class="entry-title">', '</h1></header><!-- .entry-header -->' ); ?>
                    <div class="entry-content">
                        <?php mangapress_comic_navigation($latest_comic); ?>
                        <?php the_content(); ?>
                        <?php
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'twentyfourteen' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) );

			edit_post_link( __( 'Edit', 'twentyfourteen' ), '<span class="edit-link">', '</span>' );
                        ?>
                    </div><!-- .entry-content -->
                </article><!-- #post -->


                <?php comments_template('', true); ?>

            <?php endwhile; // end of the loop. ?>
        <?php //
        else :
            locate_template(array('parts/404.php'), true);
        endif;
        ?>
    </div><!-- #content -->
    <?php get_sidebar( 'content' ); ?>
    </div><!-- #primary -->
</div>

<?php
get_sidebar();
get_footer();