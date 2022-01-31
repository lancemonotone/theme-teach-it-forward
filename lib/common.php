<?php
/**
 * Social sharing links
 *
 * @param $title
 * @param $url
 *
 * @return string
 */
function display_social_sharing ( $title, $url ){
	$url_encoded = urlencode($url);
	$title_encoded = urlencode($title);
return <<<EOD
	<div class="fa-ces sharing-links">
		<div class="sharing-icons">
			<div><a class="sharing anchor" title="Right-click to copy link" href="{$url}"></a></div>
			<div><a class="sharing facebook" href="https://www.facebook.com/login.php?next=https%3A%2F%2Fwww.facebook.com%2Fsharer%2Fsharer.php%3Fu%3D{$url_encoded}%26ret%3Dlogin&display=popup"></a></div>
			<div><a class="sharing tumblr" href="https://www.tumblr.com/login?redirect_to=https%3A%2F%2Fwww.tumblr.com%2Fwidgets%2Fshare%2Ftool%3FshareSource%3Dlegacy%26canonicalUrl%3D%26url%3D{$url_encoded}"></a></div>
			<div><a class="sharing twitter" href="https://twitter.com/intent/tweet?text={$title_encoded}&url={$url_encoded}&related="></a></div>
			<div><a class="sharing email" href="mailto:?subject=A Williams Story for you&body={$title_encoded} {$url_encoded}"></a></div>
		</div>
		<div class="share-icon"></div>
	</div>
EOD;
}

/**
 * Get all posts under specified taxonomy.
 *
 * @param $taxonomy_name
 *
 * @return array
 */
function get_posts_by_taxonomy($taxonomy_name){
	$posts_arr = array();
	$terms = get_terms($taxonomy_name);
	foreach ( $terms as $term ) {
		array_push($posts_arr, reset(get_posts_by_term($taxonomy_name, $term)));
	}
	return $posts_arr;
}

/**
 * Get all posts under specified term.
 *
 * @param $taxonomy_name
 * @param $term
 * @param string $orderby
 *
 * @return WP_Query
 */
function get_posts_by_term( $taxonomy_name, $term, $orderby = 'menu_order' ) {
	$args = array(
		'posts_per_page' => - 1,
		'orderby'        => $orderby,
		'order'          => 'ASC',
		'status'         => 'publish',
		'tax_query'      => array(
			array(
				'taxonomy' => $taxonomy_name,
				'field'    => 'id',
				'terms'    => $term->term_id
			)
		)
	);

	$query = new WP_Query( $args );
	return $query->posts;
}

/**
 * Get ACF sorted top-level priorities and poster images for landing page.
 *
 * @param $taxonomy
 *
 * @return array
 */
function get_terms_by_taxonomy($taxonomy) {
	$args = array(
		'parent'     => 0,
		'hide_empty' => false,
	);
	$terms = get_terms( $taxonomy, $args );
	$termchildren_arr = array();
	foreach ( $terms as &$term ) {
		$term->sort_order = get_field( 'sort_order', "{$term->taxonomy}_{$term->term_id}" );
		$term->poster_img_arr = get_poster_image( $term->taxonomy, $term->term_id );
		array_push( $termchildren_arr, $term );
	}
	usort( $termchildren_arr, 'sort_by_acf_sort_order' );

	return $termchildren_arr;
}

/**
 * Sort for ACF term sort_order field
 *
 * @param $a
 * @param $b
 *
 * @return int
 */
function sort_by_acf_sort_order( $a, $b ) {
	if ( $a->sort_order == $b->sort_order ) {
		return 0;
	} elseif ( $a->sort_order < $b->sort_order ) {
		return - 1;
	} else {
		return 1;
	}
}

/**
 * Get category poster image
 *
 * @param $taxonomy_name
 * @param $term_id
 *
 * @return mixed|null|void
 */
function get_poster_image( $taxonomy_name, $term_id ) {
	return get_field( 'poster_image', "{$taxonomy_name}_{$term_id}" );
}
