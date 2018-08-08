<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Sugar & Spice
 */

get_header(); ?>

	<div id="primary" class="content-area">

				<?php woocommerce_content(); ?>

	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
