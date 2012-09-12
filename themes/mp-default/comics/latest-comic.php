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
<div id="primary">
    <div id="content" role="main">
        <?php $latest_comic = mpp_get_latest_comic(); ?>
        <?php
        if ($latest_comic->have_posts()) :
            while ($latest_comic->have_posts()) : $latest_comic->the_post(); ?>


            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header><!-- .entry-header -->

                <?php mangapress_comic_navigation($latest_comic); ?>

                <div class="entry-content">
                    <p>
                        <?php the_post_thumbnail(get_the_ID()); ?>
                    </p>

                    <?php the_content(); ?>
                </div><!-- .entry-content -->
                <footer class="entry-meta">
                    <?php edit_post_link(__('Edit', 'twentyeleven'), '<span class="edit-link">', '</span>'); ?>
                </footer><!-- .entry-meta -->
            </article><!-- #post-<?php the_ID(); ?> -->


            <?php comments_template('', true); ?>

        <?php endwhile; // end of the loop. ?>
        <?php else :
            locate_template(array('parts/404.php'), true);
        endif; ?>
    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>