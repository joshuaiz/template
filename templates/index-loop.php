<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class( 'cf' ); ?> role="article">

		<header class="article-header">

			<h1 class="h2 entry-title">

				<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>

			</h1>
			
			<?php get_template_part( 'templates/byline'); ?>

		</header>

		<section class="entry-content cf">
									
			<?php the_content(); ?>

		</section>

		<footer class="article-footer cf">

			<?php get_template_part( 'templates/comment', 'count'); ?>

            <?php get_template_part( 'templates/category-tags'); ?>

		</footer>

	</article>

<?php endwhile; endif; ?>

<?php template_page_navi(); ?>