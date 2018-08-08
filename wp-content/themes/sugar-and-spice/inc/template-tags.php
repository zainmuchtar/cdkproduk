<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package Sugar & Spice
 */

if ( ! function_exists( 'sugarspice_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 */
function sugarspice_content_nav( $nav_id ) {
	global $wp_query, $post;

	// Don't print empty markup on single pages if there's nowhere to navigate.
	if ( is_single() ) {
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous )
			return;
	}

	// Don't print empty markup in archives if there's only one page.
	if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
		return;

	$nav_class = ( is_single() ) ? 'post-navigation' : 'paging-navigation';

	?>
	<nav role="navigation" id="<?php echo esc_attr( $nav_id ); ?>" class="<?php echo $nav_class; ?> section">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'sugarspice' ); ?></h1>

	<?php if ( is_single() ) : // navigation links for single posts ?>
        <h2 class="section-title"><span><?php _e('Navigation','sugarspice'); ?></span></h2>
		<?php previous_post_link( '<div class="nav-previous">%link</div>', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', 'sugarspice' ) . '</span> %title' ); ?>
		<?php next_post_link( '<div class="nav-next">%link</div>', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', 'sugarspice' ) . '</span>' ); ?>

	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

		<?php if ( get_next_posts_link() ) : ?>
		<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'sugarspice' ) ); ?></div>
		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>
		<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'sugarspice' ) ); ?></div>
		<?php endif; ?>

	<?php endif; ?>

	</nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
	<?php
}
endif; // sugarspice_content_nav

/**
 * Content pagination
 */
function sugarspice_link_pages( $content ) {
    if ( is_single() ) {
        $content .= wp_link_pages( array(
                        'before' => '<div class="page-links">' . __( 'Pages:', 'sugarspice' ),
                        'after'  => '</div>',
                        'echo'   => 0
                    ) );
    }
    return $content;
}
add_filter('the_content','sugarspice_link_pages', 10);


if ( ! function_exists( 'sugarspice_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
function sugarspice_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<div class="comment-body">
			<?php _e( 'Pingback:', 'sugarspice' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'sugarspice' ), '<span class="edit-link">', '</span>' ); ?>
		</div>

	<?php else : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
				<div class="comment-author vcard">
					<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
				</div><!-- .comment-author -->
                <div class="comment-box">
					<?php printf( __( '%s <span class="says">says:</span>', 'sugarspice' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
                    <span class="comment-meta">
                        <small>
                        <a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
                            <time datetime="<?php comment_time( 'c' ); ?>">
                                <?php printf( _x( '%1$s at %2$s', '1: date, 2: time', 'sugarspice' ), get_comment_date(), get_comment_time() ); ?>
                            </time>
                        </a>
                        <?php edit_comment_link( __( 'Edit', 'sugarspice' ), '<span class="edit-link">', '</span>' ); ?>
                        </small>
                    <?php if ( '0' == $comment->comment_approved ) : ?>
                    <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'sugarspice' ); ?></p>
                    <?php endif; ?>
                    </span><!-- .comment-meta -->

                    <div class="comment-content">
                        <?php comment_text(); ?>
                    </div><!-- .comment-content -->                        
                        
                        
                    </span><!-- .comment-metadata -->



			<?php
				comment_reply_link( array_merge( $args, array(
					'add_below' => 'div-comment',
					'depth'     => $depth,
					'max_depth' => $args['max_depth'],
					'before'    => '<div class="reply">',
					'after'     => '</div>',
				) ) );
			?>
		</article><!-- .comment-body -->

	<?php
	endif;
}
endif; // ends check for sugarspice_comment()

if ( ! function_exists( 'sugarspice_the_attached_image' ) ) :
/**
 * Prints the attached image with a link to the next attached image.
 */
function sugarspice_the_attached_image() {
	$post                = get_post();
	$attachment_size     = apply_filters( 'sugarspice_attachment_size', array( 1200, 1200 ) );
	$next_attachment_url = wp_get_attachment_url();

	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the
	 * URL of the next adjacent image in a gallery, or the first image (if
	 * we're looking at the last image in a gallery), or, in a gallery of one,
	 * just the link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );

	// If there is more than 1 attachment in a gallery...
	if ( count( $attachment_ids ) > 1 ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" rel="attachment">%2$s</a>',
		esc_url( $next_attachment_url ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}
endif;

/*-------------------------------------------------------------------------------------------*/
/* Image Caption Shortcode 
/*-------------------------------------------------------------------------------------------*/

function sugarspice_caption_shortcode_filter($val, $attr, $content = null) {

    extract(shortcode_atts(array(
        'id'    => '',
        'align' => 'alignnone',
        'width' => '',
        'caption' => ''
    ), $attr));
     
    if ( 1 > (int) $width || empty($caption) )
        return $val;
 
    if ( $id ) $id = 'id="' . esc_attr($id) . '" ';
 
    return '<div ' . $id . 'class="wp-caption ' . esc_attr($align) . '" style="width: ' . (int) $width . 'px">'
    . do_shortcode( $content ) . '<p class="wp-caption-text">' . $caption . '</p></div>';
}

add_filter('img_caption_shortcode', 'sugarspice_caption_shortcode_filter',10,3);

if ( ! function_exists( 'sugarspice_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function sugarspice_posted_on() {

  $time_string = '<span class="posted-on"><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date published updated" datetime="%3$s">%4$s</time></a></span>';

	$time_string = sprintf( $time_string,
        esc_url( get_permalink() ),
        esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);    

    $num_comments = get_comments_number();

    if ( comments_open() ) {
        if ( $num_comments == 0 ) {
            $comments = __('No Comments','sugarspice');
        } elseif ( $num_comments > 1 ) {
            $comments = $num_comments . __(' Comments','sugarspice');
        } else {
            $comments = __('1 Comment','sugarspice');
        }
    } else {
        $comments =  __('Comments off','sugarspice');
    }

	$comments_string = '<span class="comments"><a href="%1$s"><i class="icon-comment"></i> %2$s</a></span>';

	$comments_string = sprintf( $comments_string,
		esc_url( get_comments_link() ),
		esc_html( $comments )
	);    

    $author_string = '<span class="byline"> %1$s <span class="author vcard"><a href="%2$s" title="%3$s" rel="author" class="fn">%4$s</a></span></span>';
    
    $author_string = sprintf( $author_string,
        /* translators: this text appears next to author name */
        __( 'by', 'sugarspice' ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'sugarspice' ), get_the_author() ) ),
		get_the_author()
    );

    $meta_data = array();

    $post_meta = of_get_option( 'meta_data' );

    if ( empty ($post_meta) )
      return ' ';

    if ( is_array( $post_meta ) ) {
        $display_author = $post_meta['author'];
        $display_date = $post_meta['date'];
        $display_comments = $post_meta['comments'];
    }
    else {
        $display_author = 1;
        $display_date = 1;
        $display_comments = 1;
    }

    
    if ( $display_author )
        $meta_data[] = $author_string;

    if ( $display_date )
        $meta_data[] = $time_string;

    if ( $display_comments )
        $meta_data[] = $comments_string;
    
    
    print( implode( ' // ', $meta_data) );
    
}
endif;

if ( ! function_exists( 'sugarspice_post_meta' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function sugarspice_post_meta() {

    /* translators: used between list items, there is a space after the comma */
    $category_list = get_the_category_list( __( ', ', 'sugarspice' ) );

    /* translators: used between list items, there is a space after the comma */
    $tag_list = get_the_tag_list( '', __( ', ', 'sugarspice' ) );

    if ( ! sugarspice_categorized_blog() ) {
        // This blog only has 1 category so we just need to worry about tags in the meta text
        if ( '' != $tag_list ) {
            $meta_text = __( 'This entry was tagged %2$s.', 'sugarspice' );
        }

    } else {
        // But this blog has loads of categories so we should probably display them here
        if ( '' != $tag_list ) {
            $meta_text = __( 'This entry was posted in %1$s and tagged %2$s.', 'sugarspice' );
        } else {
            $meta_text = __( 'This entry was posted in %1$s.', 'sugarspice' );
        }

    } // end check for categories on this blog

    printf(
        $meta_text,
        $category_list,
        $tag_list
    );
}
endif;
/**
 * Filter post_gallery to display gallery as slideshow.
 */
if ( ! function_exists( 'sugarspice_post_gallery' ) ) :
  function sugarspice_post_gallery( $output, $attr) {
    global $post, $wp_locale;

    static $instance = 0;
    $instance++;

    if ( isset( $attr['orderby'] ) ) {
        $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
        if ( !$attr['orderby'] )
            unset( $attr['orderby'] );
    }
    // Exception for Jetpack galleries
    if ( isset( $attr['type'] ) ) {
      return;
    }
    
    extract(shortcode_atts(array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post->ID,
        'itemtag'    => 'li',
        'icontag'    => 'div',
        'captiontag' => 'div',
        'columns'    => 3,
        'size'       => array(620,350),
        'include'    => '',
        'exclude'    => '',
        'link'       => '',
    ), $attr));

    $id = intval($id);
    if ( 'RAND' == $order )
        $orderby = 'none';

    if ( !empty($include) ) {
        $include = preg_replace( '/[^0-9,]+/', '', $include );
        $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

        $attachments = array();
        foreach ( $_attachments as $key => $val ) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif ( !empty($exclude) ) {
        $exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
        $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    } else {
        $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    }

    if ( empty($attachments) )
        return '';

    if ( is_feed() ) {
        $output = "\n";
        foreach ( $attachments as $att_id => $attachment )
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        return $output;
    }

  	$itemtag = tag_escape($itemtag);
  	$selector = "slider-{$instance}";
  	$captiontag = tag_escape($captiontag);


  	$output .= "<div id='{$selector}' class='flexslider slider-{$id}'>";

  	$i = 0;
    $output .= "<ul class='slides'>";
    foreach ( $attachments as $id => $attachment ) {
    	$itemclass = ($i==0) ? 'item active' : 'item';

        if ( $link == 'none' ) {
            $image = wp_get_attachment_image( $id, $size );        
        } elseif ( $link == 'media' ) {
            $image = sprintf(
                '<a href="%1$s" rel="attachment">%2$s</a>',
                esc_url( wp_get_attachment_url( $id ) ),
                wp_get_attachment_image( $id, $size )
            );
        } else {
            $image = wp_get_attachment_link( $id, $size, true, false );            
        }

    	$output .= "<{$itemtag} class='{$itemclass}'>";
    	$output .= "$image";

    	if ( $captiontag && trim($attachment->post_excerpt) ) {
        $output .= "
          <{$captiontag} class='flex-caption'>
          " . wptexturize($attachment->post_excerpt) . "
          </{$captiontag}>";
      }
    	$output .= "</{$itemtag}>";
    	$i++;
    }
    $output .= "</ul>";
    
    $output .= "</div>";
    return $output;
  }
endif;
add_filter( 'post_gallery', 'sugarspice_post_gallery', 10, 2 );

/**
 * Remove Gallery Inline Styling
 */
add_filter( 'use_default_gallery_style', '__return_false' );

/**
 * Returns true if a blog has more than 1 category
 */
function sugarspice_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so sugarspice_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so sugarspice_categorized_blog should return false
		return false;
	}
}

/**
 * Flush out the transients used in sugarspice_categorized_blog
 */
function sugarspice_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'sugarspice_category_transient_flusher' );
add_action( 'save_post',     'sugarspice_category_transient_flusher' );
