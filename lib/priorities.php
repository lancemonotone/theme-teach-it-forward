<?php
/**
 * Get ACF sorted array of child terms
 *
 * @param $queried_term
 * @param $taxonomy_name
 *
 * @return array
 */
function get_termchildren_arr( $queried_term, $taxonomy_name ) {
	$termchildren = get_term_children( $queried_term->term_id, $taxonomy_name );
	$termchildren_arr = array();
	foreach ( $termchildren as $child_id ) {
		$term = get_term_by( 'id', $child_id, $taxonomy_name );
		$term->sort_order = get_field( 'sort_order', "{$taxonomy_name}_{$child_id}" );
		array_push( $termchildren_arr, $term );
	}
	usort( $termchildren_arr, 'sort_by_acf_sort_order' );

	return $termchildren_arr;
}

/**
 * Wrapper function to push HTML of terms and associated posts into array
 *
 * @param $term
 * @param $taxonomy_name
 * @param $termchildren
 *
 * @return array|mixed
 */
function get_terms_arr( $term, $taxonomy_name, $termchildren ) {
	$terms_arr = array();

	if ( $termchildren ) {
		foreach ( $termchildren as $child ) {
			array_push( $terms_arr, get_term_posts( $taxonomy_name, $child, true ) );
		}
	} else {
		array_push( $terms_arr, get_term_posts( $taxonomy_name, $term ) );
	}

	return $terms_arr;
}

/**
 * Get all posts for a term and return HTML
 *
 * @param $taxonomy_name
 * @param $term_id
 * @param bool $is_child
 *
 * @return string
 */
function get_term_posts( $taxonomy_name, $term, $is_child = false ) {
	if($the_posts = get_posts_by_term( $taxonomy_name, $term )){
		$posts_arr = get_priority_posts_html( $the_posts );
		return get_term_html( $term, $posts_arr, $is_child );
	}
	return false;
}

/**
 * Combine term and posts HTML
 *
 * @param $term
 * @param $the_posts
 * @param bool $is_child
 *
 * @return string
 */
function get_term_html( $term, $the_posts, $is_child ) {
	static $term_counter = 0;
	$the_posts = implode( "\n", $the_posts );
	if ( $is_child ) {
		$cat_header = '<h2 class="priority-cat-title">' . $term->name . '</h2>' . "\n";
		$cat_header .= '<span class="priority-cat-description">' . $term->description . '</span>';
	} else {
		$cat_header = '';
	}
	$out = <<<EOD
	<div class="priority-cat term-number-{$term_counter}">
		<div class="wrap">
			{$cat_header}
			<div class="priority-posts">
				{$the_posts}
			</div>
		</div>
	</div>

EOD;
	$term_counter ++;
	$term_counter = ( $term_counter >= 3 ) ? 0 : $term_counter;

	return $out;
}

/**
 * Return all priority posts HTML
 *
 * @param $the_posts
 *
 * @return array
 */
function get_priority_posts_html( $the_posts ) {
	$posts_arr = array();
	foreach ( $the_posts as $the_post ) {
		array_push( $posts_arr, get_priority_post_html( $the_post ) );
	}

	return $posts_arr;
}

/**
 * Return individual priority post HTML
 *
 * @param $the_post
 *
 * @return string
 */
function get_priority_post_html( $the_post ) {
	global $term_link;
	$post_class = implode( ' ', get_post_class( array( 'cf', 'priority' ), $the_post->ID ) );

	$img = '';
	if ( $img_arr = get_field( 'post_image', $the_post->ID ) ) {
		$img = '<img src="' . $img_arr['url'] . '" alt="' . $img_arr['alt'] . '">';
	}

	$title = get_the_title( $the_post->ID );
	$content = get_field( 'post_content', $the_post->ID );

	// Show edit link for admins
	$edit_link = get_edit_post_link( $the_post->ID );
	$edit_link_html = $edit_link ? '<a class="edit-link" href="' . $edit_link . '">Edit</a>' : '';

	$title_attribute = the_title_attribute( array( 'post' => $the_post->ID, 'echo' => false ) );
	$addthis = display_social_sharing( $title_attribute, $term_link . '#post-' . $the_post->ID );

	$out = <<<EOD
	<article id="post-{$the_post->ID}" class="{$post_class}" role="article">

		<header class="article-header">

			<h5 class="expander">{$title}</h5>

		</header>

		<section class="entry-content">

			<div class="post-img">{$img}</div>

			<div class="post-content">{$content}</div>

		</section>

		<footer class="article-footer cf">
			{$addthis}
			{$edit_link_html}
		</footer>

	</article>
EOD;

	return $out;
}

/**
 * Return all priority quotes HTML
 *
 * @param $taxonomy_name
 * @param $term_id
 *
 * @return mixed|null|void
 */
function get_priority_quotes_arr( $taxonomy_name, $term_id ) {
	$quotes_arr = array();
	if ( $quotes = get_field( 'priority_quote_sections', "{$taxonomy_name}_{$term_id}" ) ) {
		foreach ( $quotes as $quote ) {
			array_push( $quotes_arr, get_priority_quote_html( $quote ) );
		}
	}

	return $quotes_arr;
}

/**
 * Return individual priority quote HTML
 *
 * @param $quote
 *
 * @return string
 */
function get_priority_quote_html( $quote ) {
	$img = $quote['priority_quote_image']['url'];
	$img_alt = $quote['priority_quote_image']['alt'];

	$content = $quote['priority_quote'];

	$author = $quote['priority_quote_author'] ? '<span class="quote-author">' . $quote['priority_quote_author'] . '</span>' : '';
	$quote_opening = $author ? '<span class="quote_opening">&rdquo;</span>' : '';

	$out = <<<EOD
	<div class="cf priority-quote" role="note">

		<img class="item-background-image" src="{$img}" />
		<div class="wrap">
			<img src="{$img}" alt="{$img_alt}">

			<div class="quote-inner">

				{$quote_opening}

				{$content}

				{$author}

			</div>
		</div>

	</div>

EOD;

	return $out;
}

/**
 * @param $quotes_arr
 * @param $terms_arr
 *
 * @return array
 */
function interleave_priority_quotes_and_terms( $quotes_arr, $terms_arr ) {
	$priorities_arr = array();
	$mi = new MultipleIterator( MultipleIterator::MIT_NEED_ANY );
	$mi->attachIterator( new ArrayIterator( $quotes_arr ) );
	$mi->attachIterator( new ArrayIterator( $terms_arr ) );
	foreach ( $mi as $details ) {
		$priorities_arr = array_merge( $priorities_arr, array_filter( $details ) );
	}

	return $priorities_arr;
}

/**
 * @param $taxonomy_name
 * @param $term_id
 *
 * @return mixed|null|void
 */
function get_overlay_title( $taxonomy_name, $term_id ) {
	return get_field( 'priority_video_title', "{$taxonomy_name}_{$term_id}" );
}

/**
 * @param $taxonomy_name
 * @param $term_id
 *
 * @return mixed|null|void
 */
function get_priority_video_url( $taxonomy_name, $term_id ) {
	return get_field( 'priority_video_id', "{$taxonomy_name}_{$term_id}" );
}
