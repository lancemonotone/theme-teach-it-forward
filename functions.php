<?php

/*
Author: Eddie Machado
URL: http://themble.com/bones/

This is where you can drop your custom functions or
just edit things like thumbnail sizes, header images,
sidebars, comments, etc.
*/

class TIF {

    /**
     * TIF constructor.
     * @todo load post type libraries on template_redirect hook
     */
    function __construct(){
        $this->add_hooks();
    }

    function add_hooks(){
        add_action( 'admin_head', array( &$this, 'load_admin_styles' ) );
        add_action( 'wp_enqueue_scripts', array( &$this, 'theme_enqueue_scripts_and_styles' ) );
        add_action( 'after_setup_theme', array( &$this, 'load_theme_plugins' ) );
        add_action( 'template_redirect', array( &$this, 'load_libraries' ));
	}

    function load_admin_styles(){
        /*if( is_admin() ){
            wp_enqueue_style( 'tif-admin-style', get_template_directory_uri() . '/assets/build/css/admin.css', array() );
        }*/
        ?>
        <script type="text/javascript">(function(){console.log('<?php echo WMS_LIB_URL ?>')})()</script>
<?php
    }

    function load_libraries($parameter){
        // Load common functions
        require_once( 'lib/common.php' );

        // Use the page query to determine which helper library to load
        $current = get_queried_object();

        // If we're on a landing page, use the page slug
        // If we're on a taxonomy page, use the taxonomy name
        $page = $current->post_name ? $current->post_name : $current->taxonomy;

        // Load special functions
        if(file_exists( $file = TEMPLATEPATH . '/lib/' . $page . '.php')){
            require_once( $file );
        }
    }

    /**
     *  Enqueue parent stylesheet
     */
    function theme_enqueue_scripts_and_styles(){
        global $post;

        // temporary lib/css/style.css overrides
        wp_enqueue_style( 'style-override', get_stylesheet_uri(), array( 'bones-stylesheet' ), '1.7' );
        wp_enqueue_style( 'dashicons' );

        // Video JS styles
        wp_enqueue_style( 'video-js-style', get_template_directory_uri() . '/assets/build/css/video-js.css', array() );

        wp_enqueue_script( 'jquery-mousewheel', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js', array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'jquery-touchswipe', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.touchswipe/1.6.9/jquery.touchSwipe.min.js', array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'jquery-transit', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.transit/0.9.12/jquery.transit.min.js', array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'jquery-throttle', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-throttle-debounce/1.1/jquery.ba-throttle-debounce.min.js', array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'caroufredsel', get_template_directory_uri() . '/assets/js/libs/caroufredsel/caroufredsel-packed.js', array( 'jquery', 'bones-js' ), '1.0', true );
        wp_enqueue_script( 'video-js', '//vjs.zencdn.net/4.12/video.js', array(), '1.0', true );
        //		wp_enqueue_script( 'video-js', get_template_directory_uri() . '/assets/js/libs/video-js/video.dev.js' , array(), '1.0', false );
        wp_enqueue_script( 'video-js-ga', get_template_directory_uri() . '/assets/js/libs/videojs-ga/dist/videojs.ga.min.js', array( 'video-js' ), '1.0', true );
        wp_enqueue_script( 'detect_swipe', WMS_LIB_URL . '/assets/js/vendor/jquery.detect_swipe.js', array( 'jquery' ), '1.1', true );
        wp_enqueue_script( 'featherlight', WMS_LIB_URL . '/assets/js/vendor/featherlight/featherlight.min.js', array( 'jquery', 'detect_swipe' ), '1.1', true );
        wp_enqueue_script( 'featherlight-config', WMS_LIB_URL . '/assets/js/vendor/featherlight/featherlight-config.js', array( 'featherlight' ), '1.1', true );
        wp_enqueue_script( 'expando_tables', get_template_directory_uri() . '/assets/js/libs/expando_tables/expando_tables.js', array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'video-youtube-js', get_template_directory_uri() . '/assets/js/libs/videojs-youtube/src/youtube.js', array( 'video-js' ), '1.0', true );
        wp_enqueue_script( 'tif-video', get_template_directory_uri() . '/assets/js/videos.js', array( 'video-js' ), '1.2', true );
        wp_enqueue_script( 'typekit', '//use.typekit.net/ueb4dzi.js', array(), '1.0.0' );
        wp_enqueue_script( 'tray', get_template_directory_uri() . '/assets/js/tray.js', array( 'jquery' ), '1.0', true );
        if( get_queried_object()->taxonomy === 'priorities' ){
            global $taxonomy_name, $queried_term;
            wp_localize_script( 'tif-video', 'priorities', array(
                'overlayText' => get_overlay_title( $taxonomy_name, $queried_term->term_id )
            ) );
        }
    }
  

    /**
     * Add plugins
     */
    function load_theme_plugins(){
        include_once( dirname( __FILE__ ) . '/plugins/custom-post-type-ui/custom-post-type-ui.php' );
        //		include_once( dirname( __FILE__ ) . '/plugins/taxonomy-terms-order/taxonomy-terms-order.php' );
        include_once( dirname( __FILE__ ) . '/plugins/admin-menu-editor/menu-editor.php' );
        include_once( dirname( __FILE__ ) . '/plugins/duplicate-post/duplicate-post.php' );
        include_once( dirname( __FILE__ ) . '/plugins/juicer.php' );
    }
}

// LOAD BONES CORE (if you remove this, the theme will break)
require_once( 'lib/bones.php' );

// CUSTOMIZE THE WORDPRESS ADMIN (off by default)
// require_once( 'lib/admin.php' );

/*********************
 * LAUNCH BONES
 * Let's get everything up and running.
 *********************/

function bones_ahoy(){

    //Allow editor style.
    add_editor_style( get_stylesheet_directory_uri() . '/assets/build/css/editor-style.css' );

    // let's get language support going, if you need it
    load_theme_textdomain( 'bonestheme', get_template_directory() . '/assets/translation' );

    // USE THIS TEMPLATE TO CREATE CUSTOM POST TYPES EASILY
    //require_once( 'lib/custom-post-type.php' );

    // launching operation cleanup
    add_action( 'init', 'bones_head_cleanup' );
    // A better title
    add_filter( 'wp_title', 'rw_title', 10, 3 );
    // remove WP version from RSS
    add_filter( 'the_generator', 'bones_rss_version' );
    // remove pesky injected css for recent comments widget
    add_filter( 'wp_head', 'bones_remove_wp_widget_recent_comments_style', 1 );
    // clean up comment styles in the head
    add_action( 'wp_head', 'bones_remove_recent_comments_style', 1 );
    // clean up gallery output in wp
    add_filter( 'gallery_style', 'bones_gallery_style' );

    // enqueue base scripts and styles
    add_action( 'wp_enqueue_scripts', 'bones_scripts_and_styles', 999 );
    // ie conditional wrapper

    // launching this stuff after theme setup
    bones_theme_support();

    // adding sidebars to Wordpress (these are created in functions.php)
    add_action( 'widgets_init', 'bones_register_sidebars' );

    // cleaning up random code around images
    add_filter( 'the_content', 'bones_filter_ptags_on_images' );
    // cleaning up excerpt
    add_filter( 'excerpt_more', 'bones_excerpt_more' );
} /* end bones ahoy */

// let's get this party started
add_action( 'after_setup_theme', 'bones_ahoy' );

/************* OEMBED SIZE OPTIONS *************/

if( ! isset( $content_width ) ){
    $content_width = 1140;
}

/************* THUMBNAIL SIZE OPTIONS *************/

// Thumbnail sizes
//add_image_size( 'bones-thumb-600', 600, 150, true );
//add_image_size( 'bones-thumb-300', 300, 100, true );

/*
to add more sizes, simply copy a line from above
and change the dimensions & name. As long as you
upload a "featured image" as large as the biggest
set width or height, all the other sizes will be
auto-cropped.

To call a different size, simply change the text
inside the thumbnail function.

For example, to call the 300 x 100 sized image,
we would use the function:
<?php the_post_thumbnail( 'bones-thumb-300' ); ?>
for the 600 x 150 image:
<?php the_post_thumbnail( 'bones-thumb-600' ); ?>

You can change the names and dimensions to whatever
you like. Enjoy!
*/
/*
add_filter( 'image_size_names_choose', 'bones_custom_image_sizes' );

function bones_custom_image_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'bones-thumb-600' => __('600px by 150px'),
        'bones-thumb-300' => __('300px by 100px'),
    ) );
}
*/

/*
The function above adds the ability to use the dropdown menu to select
the new images sizes you have just created from within the media manager
when you add media to your content blocks. If you add more image sizes,
duplicate one of the lines in the array and name it according to your
new image size.
*/

/************* THEME CUSTOMIZE *********************/

/* 
  A good tutorial for creating your own Sections, Controls and Settings:
  http://code.tutsplus.com/series/a-guide-to-the-wordpress-theme-customizer--wp-33722
  
  Good articles on modifying the default options:
  http://natko.com/changing-default-wordpress-theme-customization-api-sections/
  http://code.tutsplus.com/tutorials/digging-into-the-theme-customizer-components--wp-27162
  
  To do:
  - Create a js for the postmessage transport method
  - Create some sanitize functions to sanitize inputs
  - Create some boilerplate Sections, Controls and Settings
*/

function bones_theme_customizer( $wp_customize ){
    // $wp_customize calls go here.
    //
    // Uncomment the below lines to remove the default customize sections

    // $wp_customize->remove_section('title_tagline');
    // $wp_customize->remove_section('colors');
    // $wp_customize->remove_section('background_image');
    // $wp_customize->remove_section('static_front_page');
    // $wp_customize->remove_section('nav');

    // Uncomment the below lines to remove the default controls
    // $wp_customize->remove_control('blogdescription');

    // Uncomment the following to change the default section titles
    // $wp_customize->get_section('colors')->title = __( 'Theme Colors' );
    // $wp_customize->get_section('background_image')->title = __( 'Images' );
}

add_action( 'customize_register', 'bones_theme_customizer' );

/************* ACTIVE SIDEBARS ********************/

// Sidebars & Widgetizes Areas
function bones_register_sidebars(){
    register_sidebar( array(
        'id'            => 'sidebar1',
        'name'          => __( 'Sidebar 1', 'bonestheme' ),
        'description'   => __( 'The first (primary) sidebar.', 'bonestheme' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widgettitle">',
        'after_title'   => '</h4>',
    ) );

    /*
    to add more sidebars or widgetized areas, just copy
    and edit the above sidebar code. In order to call
    your new sidebar just use the following code:

    Just change the name to whatever your new
    sidebar's id is, for example:
    */

    register_sidebar( array(
        'id'            => 'event_videowall',
        'name'          => __( 'Event Videowall', 'bonestheme' ),
        'description'   => __( 'Widetized area for event videos.', 'bonestheme' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widgettitle">',
        'after_title'   => '</h4>',
    ) );
    /*
    To call the sidebar in your template, you can just copy
    the sidebar.php file and rename it to your sidebar's name.
    So using the above example, it would be:
    sidebar-sidebar2.php

    */
} // don't remove this bracket!

/************* COMMENT LAYOUT *********************/

// Comment Layout
function bones_comments( $comment, $args, $depth ) {
$GLOBALS[ 'comment' ] = $comment;
?>
<div id="comment-<?php comment_ID(); ?>" <?php comment_class( 'cf' ); ?>>
    <article class="cf">
        <header class="comment-author vcard">
            <?php if( $comment->term_name ){ ?>
                <h3>
                    <a href="<?php echo $comment->term_link ?>"><?php echo $comment->term_name ?></a>
                </h3>
            <?php } ?>

            <?php if( $comment_image = get_field( 'comment_image', $comment ) ): ?>

                <img src="<?php echo $comment_image[ 'url' ] ?>" alt=""/>

            <?php endif; ?>

            <?php if( $comment->comment_approved == '0' ) : ?>

                <div class="alert alert-info">

                    <p><?php _e( 'Your comment is awaiting moderation.', 'bonestheme' ) ?></p>

                </div>

            <?php endif; ?>

            <section class="comment_content cf">

                <?php comment_text() ?>

            </section>

            <?php printf( __( '<cite class="fn">%1$s</cite> %2$s', 'bonestheme' ), get_comment_author_link(), edit_comment_link( __( '(Edit)', 'bonestheme' ), '  ', '' ) ) ?>

            <time datetime="<?php echo comment_time( 'Y-m-j' ); ?>">

                <?php
                if( current_time( 'timestamp' ) - get_comment_time( 'U' ) < WEEK_IN_SECONDS ){
                    printf( _x( '%s ago', '%s = human-readable time difference', 'your-text-domain' ), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) );
                } else {
                    comment_time( 'F jS, Y' );
                } ?>

            </time>

        </header>

        <?php comment_reply_link( array_merge( $args, array(
            'depth'     => $depth,
            'max_depth' => $args[ 'max_depth' ]
        ) ) ) ?>

    </article>
    <?php // </li> is added by WordPress automatically ?>
    <?php
    } // don't remove this bracket!

    /*
    This is a modification of a function found in the
    twentythirteen theme where we can declare some
    external fonts. If you're using Google Fonts, you
    can replace these fonts, change it in your scss files
    and be up and running in seconds.
    */
    function bones_fonts(){
        // wp_enqueue_style( 'googleFonts', 'https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic' );
        wp_enqueue_style( 'FontAwesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css' );

      /**
        * inline typekit for Adobe Garamond
        */
        add_action( 'wp_head', 'prefix_typekit_inline' );
        /**
         * Check to make sure the main script has been enqueued and then load the typekit
         * inline script and load cloudtype
         */
        function prefix_typekit_inline() {
            if ( wp_script_is( 'typekit', 'enqueued' ) ) {
                echo '<link rel="stylesheet" href="https://use.typekit.net/hjx1jrc.css">';
                echo '<link rel="stylesheet" type="text/css" href="https://cloud.typography.com/7265312/7501612/css/fonts.css" />';

            }
        }
        /**
        * add type to BB
        */
        function my_bb_custom_fonts ( $system_fonts ) {

            $system_fonts[ 'adobe-garamond-pro' ] = array(
              'fallback' => 'serif',
              'weights' => array(
                  '400','700'
              ),
            );
            $system_fonts[ 'Haettenschweiler' ] = array(
                'fallback' => 'Helvetica, Arial, sans-serif',
                'weights' => array(
                    '400','500','700'
                ),
              );
              $system_fonts[ '"Knockout 68 A", "Knockout 68 B"' ] = array(
                'fallback' => 'Helvetica, Arial, sans-serif',
                'weights' => array(
                    '400'
                ),
              );
              $system_fonts[ '"Knockout 70 A", "Knockout 70 B"' ] = array(
                'fallback' => 'Helvetica, Arial, sans-serif',
                'weights' => array(
                    '400'
                ),
              );
            return $system_fonts;
          }
          
          
          //Add to Page Builder modules
          add_filter( 'fl_builder_font_families_system', 'my_bb_custom_fonts' );
    }

    add_action( 'wp_enqueue_scripts', 'bones_fonts' );

    //load bb scripts globally
    function my_global_builder_posts( $post_ids ) {
        // $post_ids[] = '1094'; manually add ids
        $post_ids = get_all_page_ids();
        return $post_ids;
    }
    add_filter( 'fl_builder_global_posts', 'my_global_builder_posts' );
    

    // Load Teach It Forward
    $tif = new TIF();
    /* DON'T DELETE THIS CLOSING TAG */ ?>
