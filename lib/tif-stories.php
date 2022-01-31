<?php
function print_tif_stories_posts(){
    $permalink         = get_permalink();
    $tif_stories_posts = get_tif_stories_posts();

    foreach( $tif_stories_posts as $p ){
        $title                            = get_the_title($p->ID);
        $title                            = get_field( 'impact_title', $p->ID ) ? get_field( 'impact_title', $p->ID ) : $title;
        $title                            = $title ? '<h3 class="h2">' . $title . '</h3>' : '';
        $title_attribute = the_title_attribute( array( 'post' => $p->ID, 'echo' => false ) );
        $story_arr                        = get_extended($p->post_content);
        $story                            = $story_arr['main']; // get before read more only
        $story                            = wpautop($story); //add formattting
        $story                            = apply_filters('the_content', $story);
        $story                            = str_replace(']]>', ']]&gt;', $story);
        $extended                         = $story_arr['extended'];
//        $expandoArray[ 'post-' . $p->ID ] = $extended;
        $addthis         = display_social_sharing( $title_attribute, $permalink . '#post-' . $p->ID );
        $post_link = get_permalink($p->ID);
        $post_link_html = $extended ? '<a class="more-link" href="' . $post_link . '">Read more</a>' : '';
        $featured_img =  get_the_post_thumbnail($p->ID, "medium", array( 'class' => 'featured-img' ));

        // Show edit link for admins
        $edit_link      = get_edit_post_link( $p->ID );
        $edit_link_html = $edit_link ? '<a class="edit-link" href="' . $edit_link . '">Edit</a>' : '';

        echo <<<EOD
	<div id="post-{$p->ID}" class="social-item">
		{$edit_link_html}
		<div class="table-cell center-vertical">
		        {$addthis}
		        <a href="{$post_link}" class="title-link">{$title}</a>
		        {$featured_img}
		       
				{$story}
			    {$post_link_html}
			   
		</div>
	</div>
EOD;
      //  echo '<script type="text/javascript">var expandoData = ' . json_encode( $expandoArray ) . "\n</script>\n";
    }
}

function print_juicer_posts(){
    juicer_feed( array(
        'feed-id'    => 'teachitforward',
        'columns'    => '1',
        'gutter'     => '20',
        'overlay'    => 'false',
        'per'        => '8',
        'pages'      => '2') );
}

function get_tif_stories_posts(){
    $args = array(
        'posts_per_page' => 15,
        'post_type'      => 'tif_story',
        'post_status'    => 'publish',
    );

    return get_posts( $args );
}
