<?php
/*
Author: Joshua Michaels for studio.bio
URL: https://studio.bio/template

This is where you can drop your custom functions or
just edit things like thumbnail sizes, header images,
sidebars, comments, etc.
*/


// LOAD TEMPLATE (if you remove this, the theme will break)
require_once( 'library/template.php' );

// LOAD Osseous custom functions. The theme will (probably) break without this too.
// require_once( 'library/osseous.php' );

// CUSTOMIZE THE WORDPRESS ADMIN 
require_once( 'library/admin.php' );

/*********************
LAUNCH BONES
Let's get everything up and running.
*********************/

function template_launch() {

  //Allow editor style.
  add_editor_style( get_stylesheet_directory_uri() . '/library/css/editor-style.css' );

  // let's get language support going, if you need it
  //load_theme_textdomain( 'templatetheme', get_template_directory() . '/library/translation' );

  // launching operation cleanup
  add_action( 'init', 'template_head_cleanup' );
  // A better title
  add_filter( 'wp_title', 'rw_title', 10, 3 );
  // remove WP version from RSS
  add_filter( 'the_generator', 'template_rss_version' );
  // remove pesky injected css for recent comments widget
  add_filter( 'wp_head', 'template_remove_wp_widget_recent_comments_style', 1 );
  // clean up comment styles in the head
  add_action( 'wp_head', 'template_remove_recent_comments_style', 1 );
  // clean up gallery output in wp
  add_filter( 'gallery_style', 'template_gallery_style' );

  // enqueue base scripts and styles
  add_action( 'wp_enqueue_scripts', 'template_scripts_and_styles', 999 );
  // ie conditional wrapper

  // launching this stuff after theme setup
  template_theme_support();

  // adding sidebars to Wordpress (these are created in functions.php)
  add_action( 'widgets_init', 'template_register_sidebars' );

  // cleaning up random code around images
  add_filter( 'the_content', 'template_filter_ptags_on_images' );
  // cleaning up excerpt
  add_filter( 'excerpt_more', 'template_excerpt_more' );

} /* end template ahoy */

// let's get this party started
add_action( 'after_setup_theme', 'template_launch' );



/************* THUMBNAIL SIZE OPTIONS *************/

// Thumbnail sizes
add_image_size( 'template-image-600', 600, 600, true );
add_image_size( 'template-image-300', 300, 300, true );
add_image_size( 'template-image-300', 150, 150, true );

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
<?php the_post_thumbnail( 'template-image-300' ); ?>
for the 600 x 150 image:
<?php the_post_thumbnail( 'template-image-600' ); ?>

You can change the names and dimensions to whatever
you like. Enjoy!
*/

add_filter( 'image_size_names_choose', 'template_custom_image_sizes' );

function template_custom_image_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'template-image-600' => __('600px by 600px'),
        'template-image-300' => __('300px by 300px'),
        'template-image-150' => __('150px by 150px'),
    ) );
}

/*
The function above adds the ability to use the dropdown menu to select
the new images sizes you have just created from within the media manager
when you add media to your content blocks. If you add more image sizes,
duplicate one of the lines in the array and name it according to your
new image size.
*/




/************* ACTIVE SIDEBARS ********************/

// Sidebars & Widgetizes Areas
function template_register_sidebars() {
	register_sidebar(array(
		'id' => 'sidebar1',
		'name' => __( 'Sidebar 1', 'templatetheme' ),
		'description' => __( 'The first (primary) sidebar.', 'templatetheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));

	/*
	to add more sidebars or widgetized areas, just copy
	and edit the above sidebar code. In order to call
	your new sidebar just use the following code:

	Just change the name to whatever your new
	sidebar's id is, for example:

	register_sidebar(array(
		'id' => 'sidebar2',
		'name' => __( 'Sidebar 2', 'templatetheme' ),
		'description' => __( 'The second (secondary) sidebar.', 'templatetheme' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4 class="widgettitle">',
		'after_title' => '</h4>',
	));

	To call the sidebar in your template, you can just copy
	the sidebar.php file and rename it to your sidebar's name.
	So using the above example, it would be:
	sidebar-sidebar2.php

	*/
} // don't remove this bracket!


/************* COMMENT LAYOUT *********************/

// Comment Layout
function template_comments( $comment, $args, $depth ) {
   $GLOBALS['comment'] = $comment; ?>
  <div id="comment-<?php comment_ID(); ?>" <?php comment_class('cf'); ?>>
    <article  class="cf">
      <header class="comment-author vcard">
        <?php
        /*
          this is the new responsive optimized comment image. It used the new HTML5 data-attribute to display comment gravatars on larger screens only. What this means is that on larger posts, mobile sites don't have a ton of requests for comment images. This makes load time incredibly fast! If you'd like to change it back, just replace it with the regular wordpress gravatar call:
          echo get_avatar($comment,$size='32',$default='<path_to_url>' );
        */
        ?>
        <?php // custom gravatar call ?>
        <?php
          // create variable
          $bgauthemail = get_comment_author_email();
        ?>
        <img data-gravatar="http://www.gravatar.com/avatar/<?php echo md5( $bgauthemail ); ?>?s=40" class="load-gravatar avatar avatar-48 photo" height="40" width="40" src="<?php echo get_template_directory_uri(); ?>/library/images/nothing.gif" />
        <?php // end custom gravatar call ?>
        <?php printf(__( '<cite class="fn">%1$s</cite> %2$s', 'templatetheme' ), get_comment_author_link(), edit_comment_link(__( '(Edit)', 'templatetheme' ),'  ','') ) ?>
        <time datetime="<?php echo comment_time('Y-m-j'); ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php comment_time(__( 'F jS, Y', 'templatetheme' )); ?> </a></time>

      </header>
      <?php if ($comment->comment_approved == '0') : ?>
        <div class="alert alert-info">
          <p><?php _e( 'Your comment is awaiting moderation.', 'templatetheme' ) ?></p>
        </div>
      <?php endif; ?>
      <section class="comment_content cf">
        <?php comment_text() ?>
      </section>
      <?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
    </article>
  <?php // </li> is added by WordPress automatically ?>
<?php
} // don't remove this bracket!


/*
Use this to add Google or other web fonts.
*/
// function template_fonts() {
//   wp_enqueue_style('templateFonts', 'http://fonts.googleapis.com/css?family=Open+Sans:400,600,400italic,');
// }

// add_action('wp_enqueue_scripts', 'template_fonts');


/****************************************
* SCHEMA *
http://www.longren.io/add-schema-org-markup-to-any-wordpress-theme/
****************************************/

function html_schema() {

    $schema = 'http://schema.org/';
 
    // Is single post
    if( is_single()) {
        $type = "Article";
    }
    // Is blog home, archive or category
    else if( is_home() || is_archive() || is_category() ) {
        $type = "Blog";
    }
    // Is static front page
    else if( is_front_page()) {
        $type = "Website";
    }
    // Is a general page
     else {
        $type = 'WebPage';
    }
 
    echo 'itemscope="itemscope" itemtype="' . $schema . $type . '"';
}


// Custom js for theme customizer
// function template_customizer_js() {
//   wp_enqueue_script(
//     'template_theme_customizer',
//     get_template_directory_uri() . '/library/js/theme-customizer.js',
//     array( 'jquery', 'customize-preview' ),
//     '',
//     true
// );
// }

// function template_customizer_js() {
//   wp_enqueue_script( 'template_theme_customizer', get_template_directory_uri() . '/library/js/theme-customizer.js' );
// }
// // add_action( 'customize_preview_init', 'template_customizer_js' );
// add_action( 'admin_enqueue_scripts', 'template_customizer_js' );

/* DON'T DELETE THIS CLOSING TAG */ 
?>