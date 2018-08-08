<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Sugar & Spice
 */

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function sugarspice_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'sugarspice_page_menu_args' );

/**
 * Adds custom classes to the array of body classes.
 */
function sugarspice_body_classes( $classes ) {

	if ( ! is_active_sidebar( 'sidebar-1' ) || is_page_template( 'full-width-page.php' ) || is_page_template( 'full-width-no-title-page.php' ) )
		$classes[] = 'full-width';
        
	// Adds a class of group-blog to blogs with more than 1 published author
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	return $classes;
}
add_filter( 'body_class', 'sugarspice_body_classes' );

/**
 * Filter in a link to a content ID attribute for the next/previous image links on image attachment pages
 */
function sugarspice_enhanced_image_navigation( $url, $id ) {
	if ( ! is_attachment() && ! wp_attachment_is_image( $id ) )
		return $url;

	$image = get_post( $id );
	if ( ! empty( $image->post_parent ) && $image->post_parent != $id )
		$url .= '#main';

	return $url;
}
add_filter( 'attachment_link', 'sugarspice_enhanced_image_navigation', 10, 2 );

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 */
function sugarspice_wp_title( $title, $sep ) {
	global $page, $paged;

	if ( is_feed() )
		return $title;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title .= " $sep $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		$title .= " $sep " . sprintf( __( 'Page %s', 'sugarspice' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'sugarspice_wp_title', 10, 2 );


/**
 * Add signature at the bottom of post
 */
function sugarspice_aftercontent( $signature = '') {

    global $post;
    if( $post->post_type == 'post' && of_get_option( 'signature_image' ) && !is_home() )
        $signature .= '<div class="post_signature"><img src="'.esc_attr( of_get_option('signature_image') ).'" /></div>';

    return $signature;
}
add_filter('the_content','sugarspice_aftercontent');

/**
 * Output color scheme CSS in header
 */
function sugarspice_color_scheme() {
        
        $color = array(
            'green'     => '#97C379',
            'emerald'   => '#36AB8A',
            'mint'      => '#9ED6BB',
            'peach'     => '#F9AA89',
            'pink'      => '#F8AFB8',
            'red'       => '#F03B42',
            'violet'    => '#BB86B4',
            'babyblue'  => '#A7DBD8',
            'orange'    => '#F66B40',
            'yellow'    => '#fff568',
        );

        $main_color = $color [ ( of_get_option('main_color', 'emerald') ) ];            
        $accent_color = $color[ ( of_get_option('accent_color', 'peach') ) ];
        $basic_color = '#797979';
        $rgb = sugarspice_HexToRGB($main_color);
        
        $output = '';
        $output .= sugarspice_css_output( 'a, a:visited', $main_color );
        $output .= sugarspice_css_output( '.entry-meta a', $basic_color );
        $output .= '#nav-wrapper .ribbon-left, #nav-wrapper .ribbon-right { background-image: url("'.get_template_directory_uri().'/images/ribbon-'.of_get_option('accent_color','peach').'.png"); }';
        $output .= 'a:hover, a:focus, nav#main-nav > ul > li > a:hover { color: rgba('.$rgb['r'].', '.$rgb['g'].', '.$rgb['b'].', 0.7); }';
        $output .= sugarspice_css_output( 'nav#main-nav > ul > li.current_page_item > a, nav#main-nav > ul > li.current_page_ancestor > a, nav#main-nav > ul > li.current-menu-item > a', $main_color );
        $output .= sugarspice_css_output( '.widget-title em', $accent_color);
        $output .= sugarspice_css_output( '.widget_calendar table td#today', '', $accent_color);
        $output .= sugarspice_css_output( 'blockquote cite', $main_color );
        $output .= sugarspice_css_output( 'blockquote { border-left-color: ' . $accent_color . ';}' );
//        $output .= sugarspice_css_output( '.button, button, input[type="submit"], input[type="reset"], input[type="button"]', '', $color);
        $output .= '.button:hover, button:hover, a.social-icon:hover , input[type="submit"]:hover, input[type="reset"]:hover, input[type="button"]:hover { background: rgba('.$rgb['r'].', '.$rgb['g'].', '.$rgb['b'].', 0.7);} ';
        
        if ( of_get_option( 'responsive' ) == 1 )
            $output .= '.tinynav { display: none; }'; 

        if ( $output != '' ) {
			$output = "\n<style>\n" . $output . "</style>\n";
			echo $output;
		}
        
}   
add_action('wp_head', 'sugarspice_color_scheme');

function sugarspice_css_output($selectors, $color = '', $background = '') {

		$output = $selectors . ' {';
        if ($color) :
            $output .= ' color:' . $color .'; ';
        elseif ($background) :
            $output .= ' background:' . $background .'; ';
        endif;
		$output .= '}';
		$output .= "\n";
		return $output;
}

function sugarspice_HexToRGB($hex) {
    $hex = preg_replace("/#/", "", $hex);
    $color = array();

    if(strlen($hex) == 3) {
        $color['r'] = hexdec(substr($hex, 0, 1) . $r);
        $color['g'] = hexdec(substr($hex, 1, 1) . $g);
        $color['b'] = hexdec(substr($hex, 2, 1) . $b);
    }
    else if(strlen($hex) == 6) {
        $color['r'] = hexdec(substr($hex, 0, 2));
        $color['g'] = hexdec(substr($hex, 2, 2));
        $color['b'] = hexdec(substr($hex, 4, 2));
    }

    return $color;
}