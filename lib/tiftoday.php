<?php
function print_impact_posts() {
	$permalink  = get_permalink();
	$impact_posts = get_impact_posts();
	$background_colors = array(
		'drk-purple',
		'lt-purple',
		'orange',
		'grey',
		'white',
	);
	$color_count = count( $background_colors );
	$color = $color_count;
	foreach ( $impact_posts as $p ) {
		$type = get_field( 'impact_type', $p->ID );
		$quote_class = $type === 'quote' ? $type : '';
		$expands = $quote_class ? '' : 'expands';
		$title = get_field( 'impact_title', $p->ID );
		$title = $title ? '<h3 class="h2">' . $title . '</h3>' : '';
		$expanded =  apply_filters( 'the_content', get_field( 'impact_expanded', $p->ID ) );
        $expandoArray[ 'post-' . $p->ID ] = $expanded;
		$excerpt = get_field( 'impact_excerpt', $p->ID );
		$quote_author = get_field( 'impact_quote_author', $p->ID );
		$image = '';
		$background_color = '';
		if ( $img_arr = get_field( 'impact_image', $p->ID ) ) {
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
		switch ( $type ) {
			case 'quote':
				$partial = <<<EOD
					<span class="quote_opening">&rdquo;</span>
					{$excerpt}
					<p class="quote-author">{$quote_author}</p>

EOD;
				break;
			default:
				$partial = <<<EOD
					{$image}
					{$title}
					{$excerpt}
					{$addthis}
				<a href="javascript:void(0)" class="expander"></a>

EOD;
				break;
		}
		echo <<<EOD
	<div id="post-{$p->ID}" class="social-item ${expands} bg-color-{$background_color} {$quote_class} icon-{$type}">
		{$edit_link_html}
		<div class="table-cell center-vertical">
			{$partial}
		</div>
    </div>

EOD;
	}
	/*
	 * Add all the expando content to a javascript object for
	 * use on the client side. This prevtiftodayents the embeds from loading
	 * when the page loads, which really slows things down.
	*/
	echo '<script type="text/javascript">var expandoData = ' . json_encode( $expandoArray ) . "\n</script>\n";
}

function get_impact_posts() {
	$args = array(
		'posts_per_page' => - 1,
		'post_type'      => 'impact',
		'post_status'    => 'publish',
	);

	/*
	 * After getting the list of posts, rearrange to make the
	 * "progress" type posts stick near the top of the list.
	 * Others remain in reverse chron order EXCEPT quotes may
	 * be nudged so that that don't leave orphans above them.
	 */
	$impactPosts = get_posts( $args );
	for ( $i = 0; $i < count( $impactPosts ); $i ++ ) {
		$impactPosts[ $i ]->box_type = get_field( 'impact_type', $impactPosts[ $i ]->ID );
		if ( $impactPosts[ $i ]->box_type == 'progress' ) {
			$progressPosts[] = $impactPosts[ $i ];
			array_splice( $impactPosts, $i, 1 );
			$i --;
		}
	}

	$position = 0;
	$insertIndex = 0; //position of progress box on /tif today
	$columns = array( 'left', 'right' );
	$column = 'left';
	for ( $i = 0; $i < count( $impactPosts ); $i ++ ) {
		if ( $insertIndex == $i && count( $progressPosts ) ) {
			// This is one of the defined progress box locations. 
			//
			// Is it the correct column?
			if ( $columns[ ( $position % 2 ) ] == $column ) {
				// Insert progress box here.
				array_splice( $impactPosts, $i, 0, array( array_shift( $progressPosts ) ) );
				$insertIndex += 5;
				$position ++;
				$i ++;
				$column = ( $column == 'left' ) ? 'right' : 'left';
			} else {
				$insertIndex ++;
			}
		}
		// drm2 - there is no $quotes var defined. Throws a warning.
		/*if ( count( $quotes ) && $position % 2 == 0 ) {
			// Quotes are queued; insert one in this left-column spot.
			array_splice( $impactPosts, $i, 0, array( array_shift( $quotes ) ) );
		}*/
		if ( $impactPosts[ $i ]->box_type == 'quote' ) {
			if ( $position % 2 != 0 ) {
				// This is a right-column box on desktop. Adjust quote location such 
				// that it's in the left column. Remove it from array and reinsert
				// when $postion is left column.
				$quotes[] = $impactPosts[ $i ];
				array_splice( $impactPosts, $i, 1 );
				$i --;
			}
		} else {
			$position ++;
		}
	}

	return $impactPosts;
}
