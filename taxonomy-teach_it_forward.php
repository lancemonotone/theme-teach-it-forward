<?php
$queried_term = get_queried_object();
$taxonomy_name = $queried_term->taxonomy;
$adj_term = new Adjacent_Term( 'teach_it_forward', 'sort_order', false );
$next_term = $adj_term->next( $queried_term->term_id );
$prev_term = $adj_term->previous( $queried_term->term_id );
$next_term_name = $next_term->name;
$prev_term_name = $prev_term->name;
$next_term_link = get_term_link( $next_term, $taxonomy_name );
$prev_term_link = get_term_link( $prev_term, $taxonomy_name );
get_header(); ?>

<style type="text/css">
	/* temporary */
	/*
	section#tif-storyline-container {
		position: relative;
		min-width: 100%;
		min-height: 350px;
	}

	div#tif-comment-container {
		min-height: 200px;
		position: relative;
	}

	.loader {
		background: url('<?php //echo get_stylesheet_directory_uri()?>/assets/images/loading.gif') no-repeat scroll center center / 50px auto;
		display: block;
		width: 100%;
		height: 100%;
		position: absolute;
	}

	#tif-active-story {
		min-height: 350px;
	}

	#tif-active-story .post-image {
		float: left;
		margin-right: 10px;
	}

	#tif-active-story .post-title {
		margin: 0;
	}

	#tif-story-nav article {
		float: left;
		margin-right: 10px;
		width: 150px;
		cursor: pointer;
	}

	#tif-story-nav .post-image > img {
		height: auto;
		width: 100%;
	}

	#tif-story-nav .post-title {
		font-size: 15px;
	}

	#tif-story-nav .article-footer {
		display: none;
	}

	#tif-story-nav .entry-content {
		padding: 0;
	}

	#tif-add-story {
		border: 2px dashed;
		color: #faa64b;
		display: block;
		height: 113px;
		width: 150px;
		font-family: "Haettenschweiler";
		text-transform: uppercase;
	}
	*/
</style>

<div id="content">

	<div id="inner-content" class="wrap cf">

		<main id="main" class="m-all cf" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

			<h1 class="archive-title h4">

				<?php single_cat_title(); ?>

			</h1>

			<div class="tif-pager">

				<?php if ( $prev_term ) { ?>

					<a class="tif-prev-term" href="<?php echo $prev_term_link ?>" title="<?php echo $prev_term_name ?>">&lt;</a>

				<?php } ?>

				<?php if ( $next_term ) { ?>

					<a class="tif-next-term" href="<?php echo $next_term_link ?>" title="<?php echo $next_term_name ?>">&gt;</a>

				<?php } ?>

			</div>

			<section id="tif-storyline-container" class="cf"></section>

			<section id="tif-add-story-form">
				<?php do_action('tif_add_story_form');?>
			</section>

			<section id="tif-comments" class="cf">
				<a href="javascript:void(0)" class="join-the-discussion"><?php _e( 'Join the Discussion' ) ?></a>

				<h3><?php _e( 'Comments' ) ?></h3>

				<div id="tif-comment-container"></div>
			</section>

		</main>

	</div>

</div>

<?php get_footer(); ?>
