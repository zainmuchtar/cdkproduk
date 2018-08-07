<?php
/**
 * Plugin Name: About Widget
 */

class sugarspice_about_widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function sugarspice_about_widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'sugarspice_about_widget', 'description' => __('About section with autor image', 'sugarspice') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 250, 'height' => 350, 'id_base' => 'sugarspice_about_widget' );

		/* Create the widget. */
		$this->WP_Widget( 'sugarspice_about_widget', __('Sugar & Spice: About', 'sugarspice'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$text = $instance['text'];
		$image = $instance['image'];
		$url = $instance['url'];
		$read_more = $instance['read_more'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		?>
        <?php if($image) { ?><img src="<?php echo esc_url($image); ?>"  class="profile" /><?php } ?>
        <p><?php echo $text; ?></p>
        <?php if($url) { ?><a href="<?php echo esc_url($url); ?>" class="button"><?php echo esc_attr($read_more); ?></a><?php } ?>

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
		$instance['image'] = strip_tags( $new_instance['image'] );
		$instance['url'] = strip_tags( $new_instance['url'] );
		$instance['read_more'] = strip_tags( $new_instance['read_more'] );
		$instance['text'] = $new_instance['text'];

		return $instance;
	}


	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('About','sugarspice'), 'text' => '', 'image' => '', 'url' =>'', 'read_more' => 'Read more' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title','sugarspice') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
		</p>

		<!-- About text -->
		<p>
			<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e('About the author text','sugarspice') ?>:</label>
			<textarea id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" style="width:96%;" rows="6"><?php echo $instance['text']; ?></textarea>
		</p>

		<!-- Image -->
		<p>
			<label for="<?php echo $this->get_field_id( 'image' ); ?>"><?php _e('Author image URL','sugarspice') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" value="<?php echo $instance['image']; ?>" style="width:90%;" />
			<small><?php _e('Suggested image dimensions: 120x120px or 100x100px. Image will be rounded automatically!', 'suagrspice') ?></small>
		</p>
		<!-- URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e('Read more URL','sugarspice') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" value="<?php echo $instance['url']; ?>" style="width:90%;" />
		</p>
		<!-- Read more -->
		<p>
			<label for="<?php echo $this->get_field_id( 'read_more' ); ?>"><?php _e('Read more button text','sugarspice') ?>:</label>
			<input id="<?php echo $this->get_field_id( 'read_more' ); ?>" name="<?php echo $this->get_field_name( 'read_more' ); ?>" value="<?php echo $instance['read_more']; ?>" style="width:90%;" />
		</p>

	<?php
	}
}

?>