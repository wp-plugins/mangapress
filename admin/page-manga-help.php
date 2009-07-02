<?
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
?>
<script type="text/javascript">
	/* <![CDATA[ */
	jQuery(function() {
		jQuery(document).ready(function(){
			jQuery('#mp_back').tabs({ fxFade: true, fxSpeed: 'fast' });
		});
	});
	/* ]]> */
</script>

<div id="mp_back" class="wrap">
  <h2>Manga+Press Help</h2>

    <ul id="tabs" class="ui-tabs-nav">
      <li><a href="#getting-started">Getting Started</a></li>
      <li><a href="#post-comic">How to: Post a Comic</a></li>
      <li><a href="#comic-options">Explained: Comic Options</a></li>
      <li><a href="#comic-banners">How to: Use Comic Banners</a></li>
    </ul>
    
  <h4>How-to-use guide to using the Manga+Press plugin for Wordpress.</h4>
  <div id="getting-started" class="ui-tabs-panel">
    <fieldset class="options">
      <legend style="font-weight: bold;">First! Getting Started...</legend>
      <p>The way Manga+Press works is by using Wordpress's posts and category creation functions as a back-end for managing comic strips. Your comic is really just a post stored in the posts table
        of your current Wordpress install, and your "series" are actually represented by categories you create and place under your Comics Category. However, before you can start, the first thing you must do is create a new category for where you store all of the child categories that represent your series (Comics Category). Then, in <a href="?page=comic-options">Comic Options</a>, select from the drop-down box which categories you want to use. The second thing is you must select two pages to display your most recent comic and past comic posts (the <strong>Latest Comic Page</strong> and <strong>Comic Archive Page</strong> options).  You can also specify how you want your comics to be ordered: by date or by post ID. </p>
      <p>The second thing to do is specify where you want the comic images to be uploaded. By default, this option is set to the <code>wp-content/uploads</code> directory. You can change this if you like, and you can also specify if you want your comic pages sorted into series-based folders, meaning that a new directory is created (if it doesn't already exist) for the series associated with your comic. If you decide to change to a different folder, please be aware that your new folder must still be inside the <code>wp-content</code> folder. That will probably be changed in later version. Note: some web-hosts require that permissions be set to 777 on directories for uploading, and may even prevent PHP scripts from creating directories on the fly for security purposes.</p>
      <legend style="font-weight: bold;">Suggestions...</legend>
      <p>Here are a few suggestions for using Manga+Press. First off, I recommend not creating any categories deeper than one level below your Comics Category (<strong>example</strong>: <em>right way</em>: Comics Cat &gt; Series Cat. <em>wrong way</em>: Comics Cat &gt; Series Cat &gt; Chapter Cat). This is because Manga+Press isn't really set up (in <em>this</em> version) to detect categories that are child categories of Series Categories. If you feel the need for chapters and sub-series, I'd suggest adding tags to your comic posts by editing the posts through <a href="edit.php">Manage &gt; Posts</a>. Hopefully, in future releases, I'll have a tagging function available for posting comics.</p>
    </fieldset>
  </div>
  <div id="post-comic" class="ui-tabs-panel ui-tabs-hide">
    <fieldset class="options">
      <legend style="font-weight: bold;">How to: Post a Comic</legend>
      <p>Once Manga+Press has been installed, you can post comics by going to the <a href="?page=post_comic">Post Comic</a> page (which is located in the <strong>Manga+Press</strong> tab). Process is fairly straightforward; all you do is specify your comic's title, select 
        the series, then select the image file you want to upload (must be a jpeg, gif or png), and an optional banner image, then click the Update Comic button.</p>
    </fieldset>
  </div>
  <div id="comic-options" class="ui-tabs-panel ui-tabs-hide">
    <fieldset class="options">
      <legend style="font-weight: bold;">Explained: Set Comic Options</legend>
      <p>You can find the Comic Options tab by click on the Settings tab. Some of this was explained in <strong>Getting Started</strong> but here I'll go over what each of the options do in more detail.</p>
      <p><strong>Order by</strong>: This option gives you the option of ordering your comics either by date or post id. Seems rather redundant until you go through a phase of reodering your comics, and wind up with some comics with post ids that represent a comic that was posted recently but maybe you want the date to be a little earlier. Its set to Post ID by default.</p>
      <p><strong>Comic Category</strong>: This category represents the most recently posted comic, and stores past comics and series categories.</p>
      <p><strong>Set Comic Directory</strong>: By default, this is set to the same directory as the uploads directory specified in <strong>Miscellaneous Settings</strong> (usually <code>wp-content/uploads</code>). If, for some reason, you don't want your comics uploaded to the uploads directory, you can change it here. Note that the plug-in is hard-coded to look for the directory under <code>/wp-content/</code>. You can also specify whether or not to organize your comics by series. Enabling this option will create seperate directories for each series.</p>
      <p><strong>Banner Options</strong>: This option is turned off by default but when enabled, the plugin creates a banner image either from the comic or combines a banner skin image with a portion of the comic or a banner image uploaded with the comic. Then html code containing the banner is generated and added as an excerpt to the comic post. Requires the GD library. Later releases will have support for ImageMagick. Enabling this option will save a banner image along with your comic image.<blockquote>
          <dl>
            <dt><strong>Banner Skin</strong></dt>
            <dd>A banner skin is an optional 24bit alpha transparency PNG that you upload to combine with a section of your comic page
              to create a new banner</dd>
          </dl>
          </blockquote>
      </p>
      <p><strong>Banner Dimensions</strong>: Sets the height and width of the banner. Is disabled when banner skin is enabled. (see How To: Comic Banners)</p>
    </fieldset>
  </div>
  <div id="comic-banners" class="ui-tabs-panel ui-tabs-hide">
    <fieldset class="options">
      <legend style="font-weight: bold;">How to: Use Comic Banners</legend>
      <p>If you don't want to display your entire comic on the front page, you can use an optional &quot;teaser&quot; or banner image instead. The banner can either be generated from a random portion of your comic page, with dimensions specified in the<strong> Banner Dimensions</strong> section of <strong>Comic Options</strong>, or dimensions determined by an optional banner skin that you upload in the <strong>Banner Options</strong> section of <strong>Comic Options</strong>. A banner skin can be used when you want to seemlessly blend your banner into your website's layout and/or add optional text to your banner (for instance: Latest Comic). Hopefully, in later releases, there will be an option to stamp a date onto the comic banner.</p>
      <p>If you want an example of a banner skin in action, check out the website for my webcomic <em><a href="http://www.silent-shadow.net/">Silent Shadow</a></em>. The banner image that's advertising the most recent comic is actually a jpeg image that was created by combining a 24bit PNG and a portion of the comic page, and is stored inside the post's excerpt. It wasn't hard to create a secondary loop to display the date and excerpt containing the banner. My website currently uses an earlier version of Manga+Press.</p>
    </fieldset>
  </div>
</div>
