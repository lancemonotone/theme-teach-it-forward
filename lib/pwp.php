<?php
function print_pwp_posts(){
	$permalink  = get_permalink();
	$pwp_posts = get_pwp_posts();
    $background_colors = array(
        'drk-purple',
        'lt-purple',
        'orange',
        'grey',
        'white',
    );
    $color_count = count( $background_colors );
    $color = $color_count;
	foreach($pwp_posts as $p){
		$type = get_field('impact_type', $p->ID);
		$quote_class = $type === 'quote' ? $type : '';
		$title_color = get_field('impact_title_color', $p->ID);
		$title = get_field('impact_title', $p->ID);
		$title = $title ? '<h3 class="h2 color-' . $title_color .'">' . $title . '</h3>' : '';
		$excerpt = get_field('impact_excerpt', $p->ID);
		$expanded = get_field('impact_expanded', $p->ID);
        $expandoArray[ 'post-' . $p->ID ] = $expanded;
        $image = '';
        $background_color = '';
        if ( is_array($img_arr = get_field( 'impact_image', $p->ID )) ) {
            $image = '<img src="' . $img_arr['url'] . '" alt="' . $img_arr['alt'] . '">';
        } else if ($type != 'quote') {
            $background_color = $background_colors[ ( $color % $color_count ) ];
            $color ++;
        }
		// Show edit link for admins
		$edit_link = get_edit_post_link( $p->ID );
		$edit_link_html = $edit_link ? '<a class="edit-link" href="' . $edit_link . '">Edit</a>' : '';

		$title_attribute = the_title_attribute( array( 'post' => $p->ID, 'echo' => false ) );
		$addthis = display_social_sharing( $title_attribute, $permalink . '#post-' . $p->ID );
		echo <<<EOD
	<div id="post-{$p->ID}" class="social-item expands bg-color-{$background_color} {$quote_class} icon-{$type}">
		{$edit_link_html}
		<div class="table-cell center-vertical">
				{$image}
				{$title}
				{$excerpt}
				{$addthis}
			<a href="#" class="expander"></a>
			<section class="expand">
				{$title}
				{$expanded}
			</section>
		</div>
	</div>

EOD;
	}/*
     * Add all the expando content to a javascript object for
     * use on the client side. This prevtiftodayents the embeds from loading
     * when the page loads, which really slows things down.
    */
    echo '<script type="text/javascript">var expandoData = ' . json_encode( $expandoArray ) . "\n</script>\n";
}

function get_pwp_posts(){
	$args = array(
		'posts_per_page' => - 1,
		'post_type'      => 'pwp',
		'post_status'    => 'publish',
	);

	return get_posts( $args );
}
