
              <?php
                /*
                 * This is the default post format.
                 *
                 * So basically this is a regular post. if you don't want to use post formats,
                 * you can just copy ths stuff in here and replace the post format thing in
                 * single.php.
                 *
                 * The other formats are SUPER basic so you can style them as you like.
                 *
                 * Again, If you want to remove post formats, just delete the post-formats
                 * folder and replace the function below with the contents of the "format.php" file.
                */
              ?>

              <article id="post-<?php the_ID(); ?>" <?php post_class('cf'); ?> role="article" itemscope itemprop="blogPost" itemtype="http://schema.org/BlogPosting">


                  <p class="byline entry-meta vcard">

                    <?php printf( __( 'Posted', 'bonestheme' ).' %1$s',
                       /* the time the post was published */
                       '<time class="updated entry-time" datetime="' . get_the_time('Y-m-d') . '" itemprop="datePublished">' . get_the_time(get_option('date_format')) . '</time>'

                    ); ?>

                  </p>
                <?php // end article header ?>

                <section class="entry-content cf" itemprop="articleBody">
                  <?php
                  global $post;
                  $post = get_post($post_id);
                  setup_postdata( $post, $more_link_text, $stripteaser );
                  the_content();
                  ?>
                </section> <?php // end article section ?>

                <footer class="article-footer">
               </footer> <?php // end article footer ?>



              </article> <?php // end article ?>
