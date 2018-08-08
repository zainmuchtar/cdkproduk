<?php
/**
 *
 * Template Name: Full Width, No Title Page.
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

			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<div class="entry-content">
						<?php the_content(); ?>
						<?php
							wp_link_pages( array(
								'before' => '<div class="page-links">' . __( 'Pages:', 'sugarspice' ),
								'after'  => '</div>',
							) );
						?>
					</div><!-- .entry-content -->
					<?php edit_post_link( __( 'Edit', 'sugarspice' ), '<footer class="entry-meta"><span class="edit-link">', '</span></footer>' ); ?>
				</article><!-- #post-## -->


				<?php
					// If comments are open or we have at least one comment, load up the comment template
					if ( comments_open() || '0' != get_comments_number() )
						comments_template();
				?>

			<?php endwhile; // end of the loop. ?>

	</div><!-- #primary -->

<?php get_footer(); ?>
