<?
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
?>
<div class="wrap">
<h2>Manga+Press Template Tags</h2>
<p>A list of available Manga+Press Template Tags</p>
<ul>
  <li>
    <h3>  Conditional Template Tags
    </h3>
    <ul>
      <li><strong>is_comic()</strong>: is used to detect if post contains comic</li>
      <li><strong>is_comic_page()</strong>: is used to detect is page is a comic page, ie: Latest Comic Page</li>
      <li><strong>is_comic_archive_page()</strong>: is used to detect is page is a comic page, ie: Comic Archives Page</li>
      <li><strong>is_comic_cat()</strong>: used to detect if category is the Comics Category.</li>
      <li><strong>is_series_cat()</strong>: used to detect is category is associated with a series.</li>
      </ul>
  </li>
  <li><h3>Comic Template Tags</h3>
    <ul>
      <li><strong>get_comic_post($id)</strong>: works like the native Wordpress function <code>get_post()</code> except it retrieves a specific comic based on <code>$id</code>.</li>
      <li style="color: red;"><strong>the_series()</strong>: is supposed to work like the Wordpress template tag <code>the_category()</code>. Is currently not functional in 1.0 RC1. Look for it in later releases.</li>
      <li style="color: red;"><strong>get_comic_series($id)</strong>: retrieves the comic's series. Is currently not functional in 1.0 RC1. Look for it in later releases.</li>
      <li><strong>wp_comic_first</strong>(): returns the <code>$id</code> of the first posted comic.</li>
      <li><strong>wp_comic_last</strong>(): returns the <code>$id</code> of the last (most recent) posted comic.</li>
      <li><strong>wp_comic_navigation($post_id)</strong>: generates comic navigation if post is a comic.</li>
      <li><strong>wp_comic_next($id)</strong>: returnss the <code>$id</code> of the  comic <em>after</em> the comic specified by <code>$id</code>.</li>
      <li><strong>wp_comic_previous($id)</strong>: retrieves the <code>$id</code> of comic <em>before</em> the comic specified by <code>$id</code>.</li>
      <li><strong>wp_comic_category_id()</strong>: returns the <code>$id </code>of the Comic Category specified in <a href="?page=comic-options">Comic Options</a>.</li>
      <li><strong>get_comic_feed($feed = 'rss2')</strong>: returns a url of the feed for the Comic Category specified in Comic Options. Options are rdf, atom, and rss. Defaults to rss2.</li>
      <li><strong>get_series_feed($series, $feed = 'rss2')</strong>: returns a url of the feed for the comic series category specified by the category's id passed by <code>$series</code>. Options are rdf, atom, and rss. Defaults to rss2.</li>
    </ul>
  </li>
  </ul>
</div>