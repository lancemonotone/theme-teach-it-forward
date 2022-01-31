<?php
/**
 * Template Name: Priorities Landing
 */

$priority_cats = get_terms_by_taxonomy('priorities');
?>

<?php get_header(); ?>
<!-- Priorities Page -->
<div id="content">

	<div id="inner-content" class="cf">

		<main id="main" class="m-all cf" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

			<section class="entry-content cf" itemprop="articleBody">
				<h2><?php echo  $post->post_title ?></h2>
				<div class="intro"><?php  echo $post->post_content ?></div>


			</section>


			<?php foreach ( $priority_cats as $cat ) { ?>

				<?php
				$bkimage = "";
				if ( isset ( $cat->poster_img_arr['url'] ) ) {
					$bkimage = $cat->poster_img_arr['url'];
				}
				?>
				<div class="item" style="background-image: url(<?php print $bkimage; ?>);">
					
					<a class="priority-cat-landing wrap"  href="<?php echo esc_url( get_term_link($cat))?>">
						<span class="text">
							<h2><?php echo $cat->name; ?></h2>
							<p><?php echo the_field('home_page_blurb', $cat ) ?></p>
						</span>
					</a>
				</div>

			<?php } ?>



		</main>

	</div>

</div>


<?php get_footer(); ?>

