<!doctype html>

<!--[if lt IE 7]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8 lt-ie7"><![endif]-->
<!--[if (IE 7)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9 lt-ie8"><![endif]-->
<!--[if (IE 8)&!(IEMobile)]><html <?php language_attributes(); ?> class="no-js lt-ie9"><![endif]-->
<!--[if gt IE 8]><!--> <html <?php html_schema(); ?> <?php language_attributes(); ?> class="no-js"><!--<![endif]-->

	<head>
		<meta charset="utf-8">

		<?php // force Internet Explorer to use the latest rendering engine available ?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<?php // mobile meta (hooray!) ?>
		<meta name="HandheldFriendly" content="True">
		<meta name="MobileOptimized" content="320">
		<meta name=viewport content="width=device-width, initial-scale=1">

		<?php // Apple touch icons (see here: https://developer.apple.com/library/content/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html) ?>
		<link rel="apple-touch-icon" href="touch-icon-iphone.png">
		<link rel="apple-touch-icon" sizes="120x120" href="touch-icon-iphone.png">
		<link rel="apple-touch-icon" sizes="152x152" href="touch-icon-ipad.png">

		<?php // favicons (for more: http://www.jonathantneal.com/blog/understand-the-favicon/) ?>
		<link rel="icon" href="<?php echo get_template_directory_uri(); ?>/favicon.png">
		<!--[if IE]>
			<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">
		<![endif]-->
		<?php // or, set /favicon.ico for IE10 win ?>
		<meta name="msapplication-TileColor" content="#f01d4f">
		<meta name="msapplication-TileImage" content="<?php echo get_template_directory_uri(); ?>/library/images/win8-tile-icon.png">
        <meta name="theme-color" content="#121212">


        <?php // Social Meta. Generate your tags here: https://megatags.co/. More examples: https://moz.com/blog/meta-data-templates-123. If you use Yoast SEO or another social meta plugin or script, you can probably delete these so you don't have duplicates which will mess up what data is grabbed for your pages. Uncomment and fill in with your data to use and be a real OG. ?>
		
		<!-- <meta name="description" content="Template Theme by studio.bio is a HTML5, responsive, retina-ready WordPress theme for developers.">
		<meta name="image" content="https://template.studio.bio/images/template_logo.png">
		
		<meta itemprop="name" content="Template Theme by studio.bio">
		<meta itemprop="description" content="Template Theme by studio.bio is a HTML5, responsive, retina-ready WordPress theme for developers.">
		<meta itemprop="image" content="https://template.studio.bio/images/template_logo.png">
		
		<meta name="twitter:card" content="summary">
		<meta name="twitter:title" content="Template Theme by studio.bio">
		<meta name="twitter:description" content="Template Theme by studio.bio is a HTML5, responsive, retina-ready WordPress theme for developers.">
		<meta name="twitter:site" content="@studio.bio">
		<meta name="twitter:creator" content="@studio.bio">
		<meta name="twitter:image:src" content="https://template.studio.bio/images/template_logo.png">
		
		<meta name="og:title" content="Template Theme by studio.bio">
		<meta name="og:description" content="Template Theme by studio.bio is a HTML5, responsive, retina-ready WordPress theme for developers.">
		<meta name="og:image" content="https://template.studio.bio/images/template_logo.png">
		<meta name="og:url" content="https://template.studio.bio">
		<meta name="og:site_name" content="Template Theme ">
		<meta name="og:type" content="website"> -->


		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

		<?php // put font scripts like Typekit here ?>
		<?php // end fonts ?>

		<?php // wordpress head functions ?>
		<?php wp_head(); ?>
		<?php // end of wordpress head ?>

		<?php // drop Google Analytics Here ?>
		<?php // end analytics ?>

	</head>

	<body <?php body_class(); ?> itemscope itemtype="http://schema.org/WebPage">

		<div id="container">

			<header class="header" role="banner" itemscope itemtype="http://schema.org/WPHeader">

				<?php // Customizer Header Image section. Uncomment to use. ?>
				<?php if( get_header_image() != "" ) { 

					if (is_home() || is_front_page() ) { ?>

            		<div id="banner">                
            			
            			<img class="header-image" src="<?php header_image(); ?>" alt="Header graphic" />                
            			
            		</div>

            	<?php }

            	} ?>

				<div id="inner-header" class="wrap cf">

					<?php // to use a image just replace the bloginfo('title') with your img src ?>
					<div id="logo" itemscope itemtype="http://schema.org/Organization"><a href="<?php echo home_url(); ?>" rel="nofollow"><span class="site-title"><?php bloginfo('title'); ?></span></a></div>

					<?php // if you'd like to use the site description un-comment the below <p></p> ?>
					<p class="site-description"><?php bloginfo('description'); ?></p>


					<nav role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
					<?php // see all default args here: https://developer.wordpress.org/reference/functions/wp_nav_menu/ ?>
						<?php wp_nav_menu(array(
    					         'container' => false,                           // remove nav container
    					         'container_class' => 'menu cf',                 // class of container (should you choose to use it)
    					         'menu' => __( 'The Main Menu', 'templatetheme' ),  // nav name
    					         'menu_class' => 'nav top-nav cf',               // adding custom nav class
    					         'theme_location' => 'main-nav',                 // where it's located in the theme
						)); ?>

					</nav>

				</div>

			</header>
