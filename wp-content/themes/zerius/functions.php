<?php

// add any new or customised functions here

add_action( 'wp_enqueue_scripts', 'zerius_enqueue_styles' );
function zerius_enqueue_styles() {
	
	wp_enqueue_style( 'zerius_font', '//fonts.googleapis.com/css?family=Titillium+Web:400,300,300italic,200italic,200,400italic,600,600italic,700,700italic,900');
	
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css', array('zerif_bootstrap_style') );

	// Loads our main stylesheet.
	 wp_enqueue_style( 'zerif-child-style', get_stylesheet_uri(), array('zerif_style'), 'v1' );

}

function zerius_custom_script_fix()
{
	if ( !wp_is_mobile() ){

		wp_enqueue_script('zerif_script_child', get_stylesheet_directory_uri() .'/js/zerif.js', array('zerif_scrollReveal_script','zerif_knob_nav'), '201202067', true);

	}else{

		wp_enqueue_script('zerif_script_child', get_stylesheet_directory_uri() .'/js/zerif.js', array('zerif_knob_nav'), '201202067', true);
		/*  reduce height of the google maps on mobile */
		wp_enqueue_style( 'zerif-style-mobile', get_template_directory_uri() . '/css/style-mobile.css' );

	}
	wp_enqueue_script('zerif_nicescroll',get_stylesheet_directory_uri().'/js/jquery.nicescroll.js',array('jquery'),'12121',true);
    wp_enqueue_script('zerif_nicescroll-script',get_stylesheet_directory_uri().'/js/zerif-nicescroll.js',array('jquery','zerif_nicescroll'),'12121',true);	
}

add_action( 'wp_enqueue_scripts', 'zerius_custom_script_fix' );

function zerius_remove_style_child(){
	remove_action('wp_print_scripts','zerif_php_style');	
}

add_action( 'wp_enqueue_scripts', 'zerius_remove_style_child', 100 );

/**
 * Declare textdomain for this child theme.
 * Translations can be filed in the /languages/ directory.
 */
function zerius_theme_setup() {
    load_child_theme_textdomain( 'zerius', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'zerius_theme_setup' );

/**
 * Notice in Customize to announce the theme is not maintained anymore
 */
function zerius_customize_register( $wp_customize ) {

	require_once get_stylesheet_directory() . '/class-ti-notify.php';

	$wp_customize->register_section_type( 'Ti_Notify' );

	$wp_customize->add_section(
		new Ti_Notify(
			$wp_customize,
			'ti-notify',
			array(
				'text'     => sprintf( __( 'This child theme is not maintained anymore, consider using the parent theme %1$s or check-out our latest free one-page theme: %2$s.','zerius' ), sprintf( '<a href="' . admin_url( 'theme-install.php?theme=zerif-lite' ) . '">%s</a>', 'Zerif Lite' ), sprintf( '<a href="' . admin_url( 'theme-install.php?theme=hestia' ) . '">%s</a>', 'Hestia' ) ),
				'priority' => 0,
			)
		)
	);

	$wp_customize->add_setting( 'zerius-notify', array(
	        'sanitize_callback' => 'esc_html',
    ) );

	$wp_customize->add_control( 'zerius-notify', array(
		'label'    => __( 'Notification', 'zerius' ),
		'section'  => 'ti-notify',
		'priority' => 1,
	) );
}

add_action( 'customize_register', 'zerius_customize_register' );

/**
 * Notice in admin dashboard to announce the theme is not maintained anymore
 */
function zerius_admin_notice() {

	global $pagenow;

	if ( is_admin() && ( 'themes.php' == $pagenow ) && isset( $_GET['activated'] ) ) {
		echo '<div class="updated notice is-dismissible"><p>';
		printf( __( 'This child theme is not maintained anymore, consider using the parent theme %1$s or check-out our latest free one-page theme: %2$s.','zerius' ), sprintf( '<a href="' . admin_url( 'theme-install.php?theme=zerif-lite' ) . '">%s</a>', 'Zerif Lite' ), sprintf( '<a href="' . admin_url( 'theme-install.php?theme=hestia' ) . '">%s</a>', 'Hestia' ) );
		echo '</p></div>';
	}
}

add_action( 'admin_notices', 'zerius_admin_notice', 99 );

/**
 * Add search area in  header
 */
function zerius_add_search_content_in_header() {
	?>
	<div id="wrapper">
		<div class="header-search">
			<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label>
					<span class="screen-reader-text"><?php _ex( 'Search for:', 'label', 'zerius' ); ?></span>
					<input type="text" class="search-field" placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'zerius' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php _ex( 'Search for:', 'label', 'zerius' ); ?>">
				</label>
				<input type="submit" class="header-search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'zerius' ); ?>">
			</form>
		</div>
	</div>
<?php
}

add_action( 'zerif_after_header_container', 'zerius_add_search_content_in_header' );

/**
 * Add search icon in header
 */
function zerius_add_search_icon_in_header() {
	echo '<a id="menu-toggle-search"><div class="navbar-right-search"></div></a>';
}

add_action( 'zerif_before_navbar','zerius_add_search_icon_in_header' );
