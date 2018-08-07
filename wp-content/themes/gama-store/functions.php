<?php
/**
 * Function describe for Gama Store 
 * 
 * @package gama-store
 */
include_once( trailingslashit( get_stylesheet_directory() ) . '/lib/gama-store-metaboxes.php' );

add_action( 'wp_enqueue_scripts', 'gama_store_enqueue_styles', 999 );
function gama_store_enqueue_styles() {
  $parent_style = 'gama-store-parent-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'gama-store-child-style',
        get_stylesheet_uri(),
        array( $parent_style )
    );
  wp_enqueue_script( 'gama-store-custom-script', get_stylesheet_directory_uri() . '/js/gama-store-custom.js', array('jquery'), '1.0.1' );
}


function gama_store_theme_setup() {
    
    load_child_theme_textdomain( 'gama-store', get_stylesheet_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'gama_store_theme_setup' );



