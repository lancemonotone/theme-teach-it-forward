<?php get_header(); ?>

			<div id="content">

				<div id="inner-content" class="wrap cf">

					<main id="main" class="m-all t-2of3 d-5of7 cf" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

						<article id="post-not-found" class="hentry cf">

							<header class="article-header">

                        <h3><?php _e( 'Sorry, we can\'t find the page you requested: ' . $_SERVER['REQUEST_URI'], 'bonestheme' ); ?></h3>

							</header>

							<section class="entry-content">

								<p><?php _e( 'You may find what you are looking for in one of the sections below.', 'bonestheme' ); ?></p>

				<?php wp_nav_menu( array(
					'container'       => false,                           // remove nav container
					'container_class' => 'menu cf',                 // class of container (should you choose to use it)
					'menu'            => __( 'The Main Menu', 'bonestheme' ),  // nav name
					'menu_class'      => '404-menu',               // adding custom nav class
					'theme_location'  => '',                 // where it's located in the theme
					'before'          => '',                                 // before the menu
					'after'           => '',                                  // after the menu
					'link_before'     => '',                            // before each link
					'link_after'      => '',                             // after each link
					'depth'           => 0,                                   // limit the depth of the nav
					'fallback_cb'     => ''                             // fallback function (if there is one)
				) ); ?>

							</section>

						</article>

					</main>

				</div>

			</div>

<?php get_footer(); ?>
