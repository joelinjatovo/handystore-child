<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 */

get_header(); ?>

	<main class="site-content<?php if (function_exists('pt_main_content_class')) pt_main_content_class(); ?>" itemscope="itemscope" itemprop="mainContentOfPage"><!-- Main content -->

	<?php global $wp_query;

		if ( have_posts() ) {

			// Extra wrapper for blog posts
			if ( handy_get_option('blog_frontend_layout')=='grid' ) { ?>
				<div class="blog-grid-wrapper row">
			<?php }

			// Start the Loop.
			while ( have_posts() ) : the_post();

				get_template_part( 'content', get_post_format() );

			endwhile;

			// Close Extra wrapper for blog posts
			if ( handy_get_option('blog_frontend_layout')=='grid' ) { echo "</div>"; }

			// Pagination
			the_posts_pagination( array(
			  'mid_size' => 2,
				'prev_text' => '<i class="fa fa-chevron-left"></i>',
				'next_text' => '<i class="fa fa-chevron-right"></i>',
			) );

		} else {
			// If no content, include the "No posts found" template.
			get_template_part( 'content', 'none' );
		} ?>

	</main><!-- end of Main content -->

	<?php get_sidebar(); ?>

<?php get_footer(); ?>
