<?php
/**
 *
 * Template name: Homepage with Slider
 * The template for displaying homepage.
 *
 * @package gama-store
 */

get_header(); ?>

<?php get_template_part('template-parts/template-part', 'head'); ?>

<!-- start content container -->
<div class="row rsrc-fullwidth-home">
   <?php  
          $gama_store_section_on  = get_post_meta( get_the_ID(), 'gama_store_fullwidth_slider_on', true ); 
          $gama_store_sidebar  = get_post_meta( get_the_ID(), 'gama_store_sidebar_position', true );
          if ( $gama_store_sidebar == 'left' ) { 
              $columns = (12 - get_theme_mod( 'left-sidebar-size', 3 ));
            } elseif ( $gama_store_sidebar == 'right' ) {
              $columns = (12 - get_theme_mod( 'right-sidebar-size', 3 ));
            } else {
              $columns = '12';
          }  
   ?>
   <?php if ( $gama_store_section_on == 'on' ) { ?>
      <?php get_template_part('template-parts/template-part', 'home-slider'); ?>
   <?php } ?>    
   <div class="rsrc-home" >  
    <?php if ( $gama_store_sidebar == 'left' ) get_sidebar( 'home' ); ?>   
   <div class="col-md-<?php echo $columns; ?> rsrc-main">   
    <?php // theloop
            if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>                                
    <div <?php post_class('rsrc-post-content'); ?>>                                                           
      <?php the_content(); ?>                                                                                  
    </div>        
    <?php endwhile; ?>        
    <?php else: ?>            
    <?php get_template_part( 'content', 'none' ); ?>        
    <?php endif; ?>    
  </div>
   </div>
  <?php //get the right sidebar ?>    
   <?php if ( $gama_store_sidebar == 'right' ) get_sidebar( 'home-right' ); ?>     
</div>
<!-- end content container -->
<?php get_footer(); ?>