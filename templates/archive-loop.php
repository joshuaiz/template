<?php
the_archive_title( '<h1 class="page-title">', '</h1>' );
the_archive_description( '<div class="taxonomy-description">', '</div>' );
							?>
							
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">

		<header class="entry-header article-header">

			<h3 class="h2 entry-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
		
			<?php get_template_part( 'templates/byline'); ?>

		</header>

		<section class="entry-content cf">

			<?php the_post_thumbnail( 'template-thumb-300' ); ?>

			<?php the_excerpt(); ?>

		</section>

		<footer class="article-footer">

		</footer>

	</article>

<?php endwhile; endif; ?>

<?php template_page_navi(); ?>

							
	