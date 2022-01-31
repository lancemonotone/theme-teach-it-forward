<?php
/**
 * Template Name: Home Landing BB
 */
?>

<?php get_header();

?>

<div id="content">
	
		<main >
			<?php 
				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post(); 
						//
						the_content();
						//
					} // end while
				} // end if
			?>
		</main>

</div>

<?php get_footer(); ?>