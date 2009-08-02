<?
/**
 * @package Manga_Press
 * @subpackage Page_Manage_Series
 */
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

?>

<div class="wrap">
  <h2>Series Categories</h2>
  <fieldset class="options">
    <h3>Create New Series:</h3>
    <p class="description">Create a new series by <em>creating</em> a new category under your default comic category by going to <a href="categories.php">Posts &gt; Categories</a>.</p>
  </fieldset>
  <h2>List of Series &amp; Chapters:</h2>
  <table class="widefat">
    <thead>
      <tr>
        <th scope="col" style="text-align: center">Series</th>
        <th scope="col">Chapter</th>
        <th scope="col" style="text-align: center">Description</th>
        <th scope="col" width="90" style="text-align: center">Posts</th>
      </tr>
    </thead>
    <tbody class="the-list">
    <?php
	
	$categories	=  get_categories('child_of='.$mp_options[latestcomic_cat].'&hide_empty=0');
	foreach ($categories as $category){

	$class = ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || " class='alternate'" == $class ) ? '' : " class='alternate'";

	?>
      <tr <?=$class?>>
<?php
	
	if ($category->category_parent != $mp_options[latestcomic_cat] ) {
		$cat_parent = get_category( $category->category_parent );
?>
        <th style="text-align:center">&mdash;</th>
		<td><?=$category->cat_name?></td>
        
<?	}else{ ?>
        <th style="text-align:center"><?=$category->cat_name?></th>
		<td>&mdash;</td>
<?	} ?>
        <td><?=$category->category_description?></td>
        <td align="center"><?=$category->category_count?></td>
      </tr>
<? } ?>
    </tbody>
  </table>
</div>
