<?php get_header(); ?>
	<!-- Single Story Page -->


				<div id="full-image" class="<?php echo has_post_thumbnail()  ?  'has_image': '' ; ?>" style="background-image: url(<?php the_post_thumbnail_url( 'full' ); ?>)">

					<h1 class="entry-title"><?php the_title(); ?></h1>

				</div>
<div id="content">
		<div id="inner-content" class="wrap cf">

			<main id="main" class="m-all cf" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

				<?php get_template_part( 'post-formats/format', 'story' ); ?>
			</main>

		</div>

	</div>


<?php get_footer(); ?>

