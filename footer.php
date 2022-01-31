<?php
// determine which "give" button to use 
// Give Now link is only for PwP page and Alumni/Parent funds page. Rest of the site should have Ways to Give link.

global $wp_query;
$give_label = $give_url = '';

if ( $wp_query->query['priorities'] == 'alumni-fund-parents-fund' || $wp_query->query['pagename'] == 'pwp' ){
   $give_label = 'Give Now';
   $give_url = 'http://give2.williams.edu';		   
}
else {
	 $give_label = 'Ways to Give';
	 $give_url = 'http://giving.williams.edu/ways-to-give/';
}

?>
<div id="scroll_arrow">SCROLL
    <div class="arrow-scroll">
        <span></span>
    </div>
</div>
<div class="footer-buttons">
	<div class="wrap">
		<a class="button back-to-top" href="#">Back to Top</a>

		<a class="button give-now" href="<?php echo $give_url; ?>"><?php echo $give_label; ?></a>
	</div>
</div>
<footer class="footer" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">
	<div id="inner-footer" class="wrap cf">
		<a class="tif-home" href="<?php echo get_site_url(); ?>"><img alt="Teach It Forward" title="Teach It Forward" class="tif-footer" src="<?php echo get_template_directory_uri(); ?>/assets/images/tif-footer.png"/></a>
		<ul class="contact">
            <li class="wms-home"><a href="http://www.williams.edu/">Williams College</a></li>
			<li>75 Park Street</li>
			<li>Williamstown, MA 01267 USA</li>
			<li>413-597-4153</li>
			<li><a href="mailto:giving@williams.edu">giving@williams.edu</a></li>
            <li class="copy">&copy; <?php echo date( 'Y' ); ?></li>
		</ul>

        <a class="wms-home-graphic" href="http://www.williams.edu/"><img title="Williams College" alt="Williams College" class="wordmark" src="<?php echo get_template_directory_uri(); ?>/assets/images/wordmark.png"/></a>

			</div>
			<!-- end #inner-footer -->

		</footer>

		<?php // all js scripts are loaded in lib/bones.php ?>
		<?php wp_footer(); ?>

	</div><!-- #container -->
</body>

</html> <!-- end of site. what a ride! -->
