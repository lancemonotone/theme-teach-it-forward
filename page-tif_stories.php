<?php
/**
 * Template Name: TIF Stories & Juicer Landing
 */
?>

<?php get_header(); ?>

<div id="content">

	<div id="inner-content" class="cf">

		<main id="main" class="m-all cf" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

					<header class="article-header wrap">

						<h1 class="page-title"><?php the_title(); ?></h1>

					</header>

					<section class="entry-content cf wrap" itemprop="articleBody">
						<?php the_content(); ?>
					</section>

				</article>

				<div class="social-list wrap">
					<div class="single-col "><?php print_juicer_posts();?></div>
					<div class="single-col entry-content">
                        <?php print_tif_stories_posts();?>
                        <a href="/stories-archive/" class="archive-btn">Read more stories</a>
					</div>
				</div>

			<?php endwhile; endif; ?>

		</main>

	</div>

</div>


<?php get_footer(); ?>
