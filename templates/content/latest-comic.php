<?php
global $thumbnail_size;

mangapress_comic_navigation(); ?>

<h2><?php the_title(); ?></h2>

<p>
    <?php the_post_thumbnail(get_the_ID(), $thumbnail_size);?>
</p>

<?php the_content(); ?>