<?php
/**
 * Plugin Name: Social Widget
 */

class sugarspice_social_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function sugarspice_social_widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'sugarspice_social_widget', 'description' => __('Displays icons to social media profiles.', 'sugarspice') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'sugarspice_social_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'sugarspice_social_widget', __('Sugar & Spice: Social media icons', 'sugarspice'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$rss = $instance['rss'];
		$facebook = $instance['facebook'];
		$twitter = $instance['twitter'];
		$googleplus = $instance['googleplus'];
		$pinterest = $instance['pinterest'];
		$instagram = $instance['instagram'];
		$youtube = $instance['youtube'];
		$flickr = $instance['flickr'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		?>
        <ul class="social">
            <?php if($facebook) { ?><li><a href="<?php echo esc_url($facebook); ?>" target="_blank" class="social-icon" title="<?php esc_attr_e('Follow me on Facebook','sugarspice') ?>"><div class="icon icon-facebook"></div></a></li><?php } ?>
            <?php if($twitter) { ?><li><a href="<?php echo esc_url($twitter); ?>" target="_blank" class="social-icon" title="<?php esc_attr_e('Follow me on Twitter','sugarspice') ?>"><div class="icon icon-twitter"></div></a></li><?php } ?>
            <?php if($googleplus) { ?><li><a href="<?php echo esc_url($googleplus); ?>" target="_blank" class="social-icon" title="<?php esc_attr_e('Follow me on Google+','sugarspice') ?>"><div class="icon icon-google-plus"></div></a></li><?php } ?>
            <?php if($pinterest) { ?><li><a href="<?php echo esc_url($pinterest); ?>" target="_blank" class="social-icon" title="<?php esc_attr_e('Follow me on Pinterest','sugarspice') ?>"><div class="icon icon-pinterest"></div></a></li><?php } ?>
            <?php if($instagram) { ?><li><a href="<?php echo esc_url($instagram); ?>" target="_blank" class="social-icon" title="<?php esc_attr_e('Follow me on Instagram','sugarspice') ?>"><div class="icon icon-instagram"></div></a></li><?php } ?>
            <?php if($youtube) { ?><li><a href="<?php echo esc_url($youtube); ?>" target="_blank" class="social-icon" title="<?php esc_attr_e('Subscribe to my YouTube channel','sugarspice') ?>"><div class="icon icon-youtube"></div></a></li><?php } ?>
            <?php if($flickr) { ?><li><a href="<?php echo esc_url($flickr); ?>" target="_blank" class="social-icon" title="<?php esc_attr_e('Follow me on Flickr','sugarspice') ?>"><div class="icon icon-flickr"></div></a></li><?php } ?>
            <?php if($rss) { ?><li><a href="<?php echo esc_url($rss); ?>" target="_blank" class="social-icon" title="<?php esc_attr_e('Subscribe to my RSS feed','sugarspice') ?>"><div class="icon icon-rss"></div></a></li><?php } ?>
        </ul>
		<?php

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['rss'] = $new_instance['rss'];
		$instance['facebook'] = $new_instance['facebook'];
		$instance['googleplus'] = $new_instance['googleplus'];
		$instance['twitter'] = $new_instance['twitter'];
		$instance['pinterest'] = $new_instance['pinterest'];
		$instance['instagram'] = $new_instance['instagram'];
		$instance['youtube'] = $new_instance['youtube'];
		$instance['flickr'] = $new_instance['flickr'];

		return $instance;
	}


	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => 'Follow me', 'rss' => '', 'facebook' => '', 'twitter' => '', 'googleplus' => '', 'pinterest' => '', 'instagram' => '', 'youtube' => '', 'flickr' => '');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title','sugarspice') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
		</p>
        
        <p><?php _e('Enter full URL. If you don\'t want to display element, leave it\'s URL field empty.','sugarspice') ?></p>
		
		<!-- Facebook URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'facebook' ); ?>"><?php _e('URL address of your Facebook profile or page','sugarspice') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'facebook' ); ?>" name="<?php echo $this->get_field_name( 'facebook' ); ?>" value="<?php echo $instance['facebook']; ?>" style="width:90%;" />
		</p>
        
		<!-- Twitter URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'twitter' ); ?>"><?php _e('URL address of your Twitter profile','sugarspice') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'twitter' ); ?>" name="<?php echo $this->get_field_name( 'twitter' ); ?>" value="<?php echo $instance['twitter']; ?>" style="width:90%;" />
		</p>
        
		<!-- Google Plus URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'googleplus' ); ?>"><?php _e('URL address of your Google+ profile','sugarspice') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'googleplus' ); ?>" name="<?php echo $this->get_field_name( 'googleplus' ); ?>" value="<?php echo $instance['googleplus']; ?>" style="width:90%;" />
		</p>    
        
		<!-- Pinterest URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'pinterest' ); ?>"><?php _e('URL address of your Pinterest profile','sugarspice') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'pinterest' ); ?>" name="<?php echo $this->get_field_name( 'pinterest' ); ?>" value="<?php echo $instance['pinterest']; ?>" style="width:90%;" />
		</p>

		<!-- Instagram URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'instagram' ); ?>"><?php _e('URL address of your Instagram profile','sugarspice') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'instagram' ); ?>" name="<?php echo $this->get_field_name( 'instagram' ); ?>" value="<?php echo $instance['instagram']; ?>" style="width:90%;" />
		</p>
        
		<!-- YouTube URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'youtube' ); ?>"><?php _e('URL address of your YouTube channel','sugarspice') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'youtube' ); ?>" name="<?php echo $this->get_field_name( 'youtube' ); ?>" value="<?php echo $instance['youtube']; ?>" style="width:90%;" />
		</p>
        
		<!-- Flickr URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'flickr' ); ?>"><?php _e('URL address of your Flickr profile page','sugarspice') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'flickr' ); ?>" name="<?php echo $this->get_field_name( 'flickr' ); ?>" value="<?php echo $instance['flickr']; ?>" style="width:90%;" />
		</p>
        
		<!-- RSS URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'rss' ); ?>"><?php _e('URL address of your RSS feed','sugarspice') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'rss' ); ?>" name="<?php echo $this->get_field_name( 'rss' ); ?>" value="<?php echo $instance['rss']; ?>" style="width:90%;" />
		</p>
	<?php
	}
}

?>