<section class="tif-storyline">
	<div id="tif-active-story" class="cf"></div>
	<div id="tif-story-nav" class="cf">
		<?php foreach ( $the_posts as $the_post ) {
			echo $the_post;
		} ?>
	</div>
	<a id="tif-add-story"><?php _e('Add a story')?></a>
</section>