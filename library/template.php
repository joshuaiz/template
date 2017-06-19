<?php
/* 	This is Template!
*
* 	This is the core file where most of the
* 	theme functions & features reside.
* 	
* 	Developed by: Joshua Michaels for studio.bio
* 	URL: http://studio.bio/template
* 	
* 	  - head cleanup (remove rsd, uri links, junk css, ect)
* 	  - enqueueing scripts & styles
* 	  - theme support functions
* 	  - custom menu output & fallbacks
* 	  - related post function
* 	  - page-navi function
* 	  - removing <p> from around images
* 	  - customizing the post excerpt
* 	
*/

/*********************
WP_HEAD GOODNESS
The default wordpress head is
a mess. Let's clean it up by
removing all the junk we don't
need.
*********************/

function template_head_cleanup() {
	// category feeds
	remove_action( 'wp_head', 'feed_links_extra', 3 );
	// post and comment feeds
	remove_action( 'wp_head', 'feed_links', 2 );
	// EditURI link
	remove_action( 'wp_head', 'rsd_link' );
	// windows live writer
	remove_action( 'wp_head', 'wlwmanifest_link' );
	// previous link
	remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
	// start link
	remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
	// links for adjacent posts
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
	// WP version
	remove_action( 'wp_head', 'wp_generator' );
	// remove WP version from css
	add_filter( 'style_loader_src', 'template_remove_wp_ver_css_js', 9999 );
	// remove WP version from scripts
	add_filter( 'script_loader_src', 'template_remove_wp_ver_css_js', 9999 );

} /* end template head cleanup */


add_filter( 'wp_title', 'rw_title', 10, 3 );

function rw_title( $title, $sep, $seplocation )
{
    global $page, $paged;

    // Don't affect in feeds.
    if ( is_feed() )
        return $title;

    // Add the blog name
    if ( 'right' == $seplocation )
        $title .= get_bloginfo( 'name' );
    else
        $title = get_bloginfo( 'name' ) . $title;

    // Add the blog description for the home/front page.
    $site_description = get_bloginfo( 'description', 'display' );
    if ( $site_description && ( is_home() || is_front_page() ) )
        $title .= " {$sep} {$site_description}";

    // Add a page number if necessary:
    if ( $paged >= 2 || $page >= 2 )
        $title .= " {$sep} " . sprintf( __( 'Page %s', 'dbt' ), max( $paged, $page ) );

    return $title;
}

// remove WP version from RSS
function template_rss_version() { return ''; }

// remove WP version from scripts
function template_remove_wp_ver_css_js( $src ) {
	if ( strpos( $src, 'ver=' ) )
		$src = remove_query_arg( 'ver', $src );
	return $src;
}

// remove injected CSS for recent comments widget
function template_remove_wp_widget_recent_comments_style() {
	if ( has_filter( 'wp_head', 'wp_widget_recent_comments_style' ) ) {
		remove_filter( 'wp_head', 'wp_widget_recent_comments_style' );
	}
}

// remove injected CSS from recent comments widget
function template_remove_recent_comments_style() {
	global $wp_widget_factory;
	if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
		remove_action( 'wp_head', array($wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style') );
	}
}

// remove injected CSS from gallery
function template_gallery_style($css) {
	return preg_replace( "!<style type='text/css'>(.*?)</style>!s", '', $css );
}


/*********************
SCRIPTS & ENQUEUEING
*********************/

// loading modernizr and jquery, and reply script
function template_scripts_and_styles() {

  global $wp_styles; // call global $wp_styles variable to add conditional wrapper around ie stylesheet the WordPress way

  if (!is_admin()) {

		// modernizr (without media query polyfill)
		wp_register_script( 'template-modernizr', get_stylesheet_directory_uri() . '/library/js/libs/modernizr.custom.min.js', array(), '2.5.3', false );

		// register main stylesheet
		wp_register_style( 'template-stylesheet', get_stylesheet_directory_uri() . '/library/css/style.css', array(), '', 'all' );

		// ie-only style sheet
		wp_register_style( 'template-ie-only', get_stylesheet_directory_uri() . '/library/css/ie.css', array(), '' );

    	// comment reply script for threaded comments
    	if ( is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
		  	wp_enqueue_script( 'comment-reply' );
    	}

		//adding scripts file in the footer
		wp_register_script( 'template-js', get_stylesheet_directory_uri() . '/library/js/scripts.js', array( 'jquery' ), '', true );

		// enqueue styles and scripts
		wp_enqueue_script( 'template-modernizr' );
		wp_enqueue_style( 'template-stylesheet' );
		wp_enqueue_style( 'template-ie-only' );

		$wp_styles->add_data( 'template-ie-only', 'conditional', 'lt IE 9' ); // add conditional wrapper around ie stylesheet

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'template-js' );

	}
}


/****************************************
* REMOVE WP EXTRAS *
****************************************/

// Remove emojis: because WordPress is serious business.
// But, if you want emojis, don't let me stop you from having a good time. 
// To enable emojis, comment these functions out or just delete them.

add_action( 'init', 'disable_wp_emojicons' );

function disable_wp_emojicons() {

  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

  // filter to remove TinyMCE emojis
  add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
}

function disable_emojicons_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
    return array();
  }
}


/**
* Dequeue jQuery migrate script in WordPress.
* http://isabelcastillo.com/remove-jquery-migrate-script-wordpress
*/
add_filter( 'wp_default_scripts', 'template_remove_jquery_migrate' );

function template_remove_jquery_migrate( &$scripts) {
    if(!is_admin()) {
        $scripts->remove( 'jquery');
        $scripts->add( 'jquery', false, array( 'jquery-core' ), '1.12.4' );
    }
}

/*********************
THEME SUPPORT
*********************/

// Adding WP 3+ Functions & Theme Support
function template_theme_support() {

	// wp thumbnails (sizes handled in functions.php)
	add_theme_support( 'post-thumbnails' );

	// default thumb size
	set_post_thumbnail_size(125, 125, true);

	// wp custom background (thx to @bransonwerner for update)
	add_theme_support( 'custom-background',
	    array(
	    'default-image' => '',    // background image default
	    'default-color' => '',    // background color default (dont add the #)
	    'wp-head-callback' => '_custom_background_cb',
	    'admin-head-callback' => '',
	    'admin-preview-callback' => ''
	    )
	);

	// rss thingy
	add_theme_support('automatic-feed-links');

	// wp menus
	add_theme_support( 'menus' );

	// registering wp3+ menus
	// To add another menu, uncomment the second line and change it to whatever you want. You can have even more menus.
	register_nav_menus(
		array(
			'main-nav' => __( 'The Main Menu', 'templatetheme' ),   // main nav in header
			// 'footer-links' => __( 'Footer Links', 'templatetheme' ) // secondary nav in footer. Uncomment to use
		)
	);

	// Title tag
	add_theme_support( 'title-tag' );

	// Enable support for HTML5 markup.
	add_theme_support( 'html5', array(
		'comment-list',
		'search-form',
		'comment-form'
	) );

	// Custom Header Image
	add_theme_support( 'custom-header', array( 
		'default-image'          => get_template_directory_uri() . '/library/images/header-image.png',
  		'default-text-color'     => 'ffffff',
  		'header-text'            => true,
  		'uploads'                => true,
  		'wp-head-callback'       => 'template_style_header'
	) );


	// Add WooCommerce support. This function only removes the warning in the WP Admin. To fully support WooCommerce you will need to add some stuff to your product loops. See here: https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
	add_action( 'after_setup_theme', 'woocommerce_support' );
	function woocommerce_support() {
    	add_theme_support( 'woocommerce' );
	}

	/* Post Formats
	Ahhhh yes, the wild and wonderful world of Post Formats. 
	I've never really gotten into them but I could see some
	situations where they would come in handy. Here's a few
	examples: https://www.competethemes.com/blog/wordpress-post-format-examples/

	If you want to use them in your project, go to town. 
	Just uncomment the function below and format the bejesus 
	out of your posts. We won't judge you.
	*/

	// add_theme_support( 'post-formats',
	// 	array(
	// 		'aside',             // title less blurb
	// 		'gallery',           // gallery of images
	// 		'link',              // quick link to other site
	// 		'image',             // an image
	// 		'quote',             // a quick quote
	// 		'status',            // a Facebook like status update
	// 		'video',             // video
	// 		'audio',             // audio
	// 		'chat'               // chat transcript
	// 	)
	// );

} /* end template theme support */


/****************************************
* CUSTOMIZER *
****************************************/

function template_register_theme_customizer( $wp_customize ) {

	// echo '<pre>';
	// var_dump( $wp_customize );  
	// echo '</pre>';

	// Customize title and tagline sections and labels
	$wp_customize->get_section('title_tagline')->title = __('Site Name and Description', 'templatethemecustomizer');  
	$wp_customize->get_control('blogname')->label = __('Site Name', 'templatethemecustomizer');  
	$wp_customize->get_control('blogdescription')->label = __('Site Description', 'templatethemecustomizer');  
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';

	// Customize the Front Page Settings
	$wp_customize->get_section('static_front_page')->title = __('Homepage Preferences', 'templatethemecustomizer');
	$wp_customize->get_section('static_front_page')->priority = 20;
	$wp_customize->get_control('show_on_front')->label = __('Choose Homepage Preference:', 'templatethemecustomizer');  
	$wp_customize->get_control('page_on_front')->label = __('Select Homepage:', 'templatethemecustomizer');  
	$wp_customize->get_control('page_for_posts')->label = __('Select Blog Homepage:', 'templatethemecustomizer');  

	// Customize Background Settings
	$wp_customize->get_section('background_image')->title = __('Background Styles', 'templatethemecustomizer');  
	$wp_customize->get_control('background_color')->section = 'background_image'; 

	// Customize Header Image Settings  
	$wp_customize->add_section( 'header_text_styles' , array(
		'title'      => __('Header Text Styles','templatethemecustomizer'), 
		'priority'   => 30    
	) );
	$wp_customize->get_control('display_header_text')->section = 'header_text_styles';  
	$wp_customize->get_control('header_textcolor')->section = 'header_text_styles'; 
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage'; 

}
add_action( 'customize_register', 'template_register_theme_customizer' );

// Custom js for theme customizer
function template_customizer_scripts() {
  wp_enqueue_script(
    'template_theme_customizer',
    get_template_directory_uri() . '/library/js/theme-customizer.js',
    array( 'jquery', 'customize-preview' ),
    '',
    true
);

  // register customizer stylesheet
	wp_register_style( 'template-customizer', get_stylesheet_directory_uri() . '/library/css/customizer.css', array(), '', 'all' );

	wp_enqueue_style( 'template-customizer' );
}
add_action( 'customize_preview_init', 'template_customizer_scripts' );


// Callback function for updating header styles
function template_style_header() {

  $text_color = get_header_textcolor();
  
  ?>
  
  <style type="text/css">

  	header.header .site-title a {
    	color: #<?php echo esc_attr( $text_color ); ?>;
  	}
  
  	<?php if(display_header_text() != true): ?>
  	.site-title, .site-description {
    	display: none;
  	} 
  	<?php endif; ?>

  	#banner .header-image {
  		max-width: 100%;
  		height: auto;
  	}

  	.customize-control-description {
  		font-style: normal;
  	}

  </style>
  <?php 

}

/*********************
RELATED POSTS FUNCTION
*********************/

// Related Posts Function (call using template_related_posts(); )
function template_related_posts() {
	echo '<ul id="template-related-posts">';
	global $post;
	$tags = wp_get_post_tags( $post->ID );
	if($tags) {
		foreach( $tags as $tag ) {
			$tag_arr .= $tag->slug . ',';
		}
		$args = array(
			'tag' => $tag_arr,
			'numberposts' => 5, /* you can change this to show more */
			'post__not_in' => array($post->ID)
		);
		$related_posts = get_posts( $args );
		if($related_posts) {
			foreach ( $related_posts as $post ) : setup_postdata( $post ); ?>
				<li class="related_post"><a class="entry-unrelated" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
			<?php endforeach; }
		else { ?>
			<?php echo '<li class="no_related_post">' . __( 'No Related Posts Yet!', 'templatetheme' ) . '</li>'; ?>
		<?php }
	}
	wp_reset_postdata();
	echo '</ul>';
} /* end template related posts function */

/*********************
PAGE NAVI
*********************/

// Numeric Page Navi (built into the theme by default)
function template_page_navi() {
  global $wp_query;
  $bignum = 999999999;
  if ( $wp_query->max_num_pages <= 1 )
    return;
  echo '<nav class="pagination">';
  echo paginate_links( array(
    'base'         => str_replace( $bignum, '%#%', esc_url( get_pagenum_link($bignum) ) ),
    'format'       => '',
    'current'      => max( 1, get_query_var('paged') ),
    'total'        => $wp_query->max_num_pages,
    'prev_text'    => '&larr;',
    'next_text'    => '&rarr;',
    'type'         => 'list',
    'end_size'     => 3,
    'mid_size'     => 3
  ) );
  echo '</nav>';
} /* end page navi */

/*********************
RANDOM CLEANUP ITEMS
*********************/

// remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
function template_filter_ptags_on_images($content){
	return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
}

// This removes the annoying […] to a Read More link
function template_excerpt_more($more) {
	global $post;
	// edit here if you like
	return '...  <a class="excerpt-read-more" href="'. get_permalink( $post->ID ) . '" title="'. __( 'Read ', 'templatetheme' ) . esc_attr( get_the_title( $post->ID ) ).'">'. __( 'Read more &raquo;', 'templatetheme' ) .'</a>';
}


/*
****************************************
*      Template Special Functions      *
****************************************
*/

// Body Class functions
// Adds more slugs to body class so we can style individual pages + posts.
// Page Slug Body Class
function template_body_class( $classes ) {
global $post;
	if ( isset( $post ) ) {
	/* $classes[] = $post->post_type . '-' . $post->post_name; *//*Un comment this if you want the post_type-post_name body class */
	$pagetemplate = get_post_meta( $post->ID, '_wp_page_template', true);
	$classes[] = sanitize_html_class( str_replace( '.', '-', $pagetemplate ), '' );
	$classes[] = $post->post_name;
	}

if (is_page()) {
        global $post;
        if ( $post->post_parent ) {
            # Parent post name/slug
            $parent = get_post( $post->post_parent );
            $classes[] = $parent->post_name;

            # Parent template name
            $parent_template = get_post_meta( $parent->ID, '_wp_page_template', true);
            
            if ( !empty($parent_template) )
                $classes[] = 'template-'.sanitize_html_class( str_replace( '.', '-', $parent_template ), '' );
        }
        
        // If we *do* have an ancestors list, process it
        // http://codex.wordpress.org/Function_Reference/get_post_ancestors
        if ($parents = get_post_ancestors($post->ID)) {
            foreach ((array)$parents as $parent) {
                // As the array contains IDs only, we need to get each page
                if ($page = get_page($parent)) {
                    // Add the current ancestor to the body class array
                    $classes[] = "{$page->post_type}-{$page->post_name}";
                }
            }
        }
 
        // Add the current page to our body class array
        $classes[] = "{$post->post_type}-{$post->post_name}";
    }

return $classes;

}
add_filter( 'body_class', 'template_body_class' );


// Let's add some extra Quicktags
// These come in handy especially for clients who aren't HTML masters
// Hook into the 'admin_print_footer_scripts' action
add_action( 'admin_print_footer_scripts', 'template_custom_quicktags' );

function template_custom_quicktags() {

  if ( wp_script_is( 'quicktags' ) ) {
  ?>
  <script type="text/javascript">
  QTags.addButton( 'qt-p', 'p', '<p>', '</p>', '', '', 1 );
  QTags.addButton( 'qt-br', 'br', '<br>', '', '', '', 9 );
  QTags.addButton( 'qt-span', 'span', '<span>', '</span>', '', '', 11 );
  QTags.addButton( 'qt-h2', 'h2', '<h2>', '</h2>', '', '', 12 );
  QTags.addButton( 'qt-h3', 'h3', '<h3>', '</h3>', '', '', 13 );
  QTags.addButton( 'qt-h4', 'h4', '<h4>', '</h4>', '', '', 14 );
  QTags.addButton( 'qt-h5', 'h5', '<h5>', '</h5>', '', '', 15 );
  </script>
  <?php
  }

}

// Load dashicons on the front end
// To use, go here and copy the css/html for the dashicon you want: https://developer.wordpress.org/resource/dashicons/
// Example: <span class="dashicons dashicons-wordpress"></span>

add_action( 'wp_enqueue_scripts', 'template_load_dashicons' );

function template_load_dashicons() {

    wp_enqueue_style( 'dashicons' );
}


/*****************************************
* LET'S ROCK SOME TEMPLATE THEME OPTIONS *

Adds option page to admin and admin menu
Uncomment to use. I have a basic Google Web
Font chooser which relies on Advanced Custom 
Fields Pro.
*****************************************/


// 
// if( function_exists('acf_add_options_page') ) {
  
//   acf_add_options_page(array(
//     'page_title'  => 'Osseous Theme Settings',
//     'menu_title'  => 'Theme Settings',
//     'menu_slug'   => 'theme-general-settings',
//     'capability'  => 'manage_options',
//     'redirect'    => false
//   ));
  
// }

// Let's keep the options for the admins, k? Uncomment to use
// if( function_exists('acf_set_options_page_capability') )
// {
//     acf_set_options_page_capability( 'manage_options' );
// }

// Allow site admin to change fonts. Uncomment to use
// add_filter( 'acfgfs/font_dropdown_array', 'my_font_list' );
// function my_font_list( $fonts ) {
//     $fonts = array(
//         'Allerta' => 'Allerta',
// 		'Arvo' => 'Arvo',
// 		'Crimson Text' => 'Crimson Text',
// 		'Domine' => 'Domine',
// 		'Droid Sans' => 'Droid Sans',
// 		'Droid Serif' => 'Droid Serif',
// 		'Fjalla One' => 'Fjalla One',
// 		'Lato' => 'Lato',
// 		'Montserrat' => 'Montserrat',
// 		'Noto Serif' => 'Noto Serif',
// 		'Open Sans' => 'Open Sans',
// 		'Oswald' => 'Oswald',
// 		'Playfair Display' => 'Playfair Display',
// 		'PT Sans' => 'PT Sans',
// 		'PT Serif' => 'PT Serif',
// 		'Raleway' => 'Raleway',
// 		'Rambla' => 'Rambla',
// 		'Roboto' => 'Roboto',
// 		'Ubuntu' => 'Ubuntu',
// 		'Vollkorn' => 'Vollkorn',
		// Add your own fonts to the list
//     );
//     return $fonts;
// }

?>