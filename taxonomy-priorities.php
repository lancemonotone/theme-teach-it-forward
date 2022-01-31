<?php
global $term_link;
$queried_term = get_queried_object();
$taxonomy_name = $queried_term->taxonomy;
$term_link = get_term_link( $queried_term, $taxonomy_name );

// Video
$poster_img_arr = get_poster_image( $taxonomy_name, $queried_term->term_id );
$video_url = get_priority_video_url( $taxonomy_name, $queried_term->term_id );

// Quotes
$quotes_arr = get_priority_quotes_arr( $taxonomy_name, $queried_term->term_id );

// Child terms
$termchildren_arr = get_termchildren_arr( $queried_term, $taxonomy_name );

// Subcategories & posts
$terms_arr = get_terms_arr( $queried_term, $taxonomy_name, $termchildren_arr );

// Interleave Quotes and Terms
$priorities_quotes_and_terms_arr = interleave_priority_quotes_and_terms( $quotes_arr, $terms_arr );;

get_header(); ?>

<!-- Priorities Terms -->

<div id="content">

	<div id="inner-content" class="cf">

		<nav role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
			<?php wp_nav_menu( array(
				'container'       => 'div',                           // remove nav container
				'container_class' => 'menu cf wrap',                 // class of container (should you choose to use it)
				'menu'            => 'Priorities Subnavigation',  // nav name
				'before'          => '',                                 // before the menu
				'after'           => '',                                  // after the menu
				'link_before'     => '',                            // before each link
				'link_after'      => '',                             // after each link
				'depth'           => 0,                                   // limit the depth of the nav
				'fallback_cb'     => ''                             // fallback function (if there is one)
			) ); ?>

		</nav>

		<main id="main" class="m-all cf" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

			<div class="priorities-top">
			<h2 class="mobile-title">

						<span><?php echo get_overlay_title( $taxonomy_name, $queried_term->term_id ) ?></span>
					</h2>
				<div class="col2">
					<?php if ( $video_url ) { ?>

						<div class="video-container"  data-poster="<?php echo $poster_img_arr['url'] ?>" data-src="<?php echo $video_url ?>">

							<video id="priority_video_<?php echo $queried_term->term_id ?>" class="video-js vjs-wms-skin vjs-big-play-centered">

								<p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that
									<a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
								</p>

							</video>

						</div>

					<?php } else if ( isset( $poster_img_arr['url'] ) ) { ?>

						<div class="priorities-video">

							<img src="<?php echo $poster_img_arr['url'] ?>">

						</div>

					<?php } ?>

				</div>


				<div class="priorities-description col1">

					<div class="wrap">
					<h2>

						<span><?php echo get_overlay_title( $taxonomy_name, $queried_term->term_id ) ?></span>
					</h2>
						<?php echo category_description() ?>
					</div>

				</div>

			</div>

			<?php if(count($priorities_quotes_and_terms_arr)){?>

			<div class="priorities-all">

				<?php echo implode( "\n", $priorities_quotes_and_terms_arr ) ?>

			</div>

			<?php } ?>

		</main>

	</div>

</div>

<?php get_footer(); ?>
