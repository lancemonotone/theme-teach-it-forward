<?php
/**
 * Template Name: Events Landing
 */
?>

<?php get_header(); ?>

<div id="content">

    <div id="inner-content" class="cf">

        <main id="main" class="m-all cf" role="main" itemscope itemprop="mainContentOfPage" itemtype="http://schema.org/Blog">

            <?php if( have_posts() ) : while( have_posts() ) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

                    <header class="article-header">

                        <h1 class="page-title"><?php the_title(); ?></h1>

                    </header>

                    <section class="entry-content cf" itemprop="articleBody">

                        <?php the_content(); ?>

                    </section>

                </article>

            <?php endwhile; endif; ?>
            <div class="wrap">

                <?php print_events(); ?>

                <?php if( is_active_sidebar( 'event_videowall' ) ){ ?>
                    <h2 class="videowall-heading"><?php _e( 'Watch videos from past events' ) ?></h2>
                    <?php dynamic_sidebar( 'event_videowall' );
                } ?>

            </div>
        </main>

    </div>

</div>

<?php get_footer(); ?>

