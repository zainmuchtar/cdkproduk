<?php
/**
*
* Metaboxes
*
*/
add_action( 'cmb2_init', 'gama_store_homepage_slider_metaboxes' );

function gama_store_homepage_slider_metaboxes() {
    

    $prefix = 'gama_store';

    
  $cmb_group = new_cmb2_box( array(
		'id'           => $prefix .'_home_slider',
		'title'        => __( 'Homepage Settings', 'gama-store' ),
		'object_types' => array( 'page', ),
		'show_on'       => array( 'key' => 'page-template', 'value' => array('template-home-slider.php') ),
		'context'       => 'normal',
    'priority'      => 'high',
    'show_names'    => true,
	) );
	$cmb_group->add_field( array(
        'name'   => __( 'Slider', 'gama-store' ),
    		'desc'   => __( 'Enable or disable slider', 'gama-store' ),
    		'id'     => $prefix .'_fullwidth_slider_on',
    		'default' => 'off',
        'type'    => 'radio_inline',
        'options' => array(
            'on' => __( 'On', 'gama-store' ),
            'off'   => __( 'Off', 'gama-store' ),
        ),
    ) );
	// $group_field_id is the field id string, so in this case: $prefix . 'demo'
	$group_field_id = $cmb_group->add_field( array(
		'id'          => $prefix .'_home_slider',
		'type'        => 'group',
		'description' => __( 'Generate slider', 'gama-store' ),
		'options'     => array(
			'group_title'   => __( 'Slide {#}', 'gama-store' ), // {#} gets replaced by row number
			'add_button'    => __( 'Add another slide', 'gama-store' ),
			'remove_button' => __( 'Remove slide', 'gama-store' ),
			'sortable'      => true, 
		),
	) );
  $cmb_group->add_group_field( $group_field_id, array(
		'name'   => __( 'Image', 'gama-store' ),
		'id'     => $prefix .'_image',
		'type' => 'file',
    'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
	) );
	$cmb_group->add_group_field( $group_field_id, array(
		'name'   => __( 'Slider Title', 'gama-store' ),
		'id'     => $prefix .'_title',
		'type'   => 'text',
	) );
	$cmb_group->add_group_field( $group_field_id, array(
		'name' => __( 'Slider Description', 'gama-store' ),
		'id'   => $prefix .'_desc',
		'type' => 'textarea_code',
	) );
	$cmb_group->add_group_field( $group_field_id, array(
		'name' => __( 'Button Text', 'gama-store' ),
		'id'   => $prefix .'_button_text',
		'type' => 'text',
	) );
	$cmb_group->add_group_field( $group_field_id, array(
		'name' => __( 'Button URL', 'gama-store' ),
		'id'   => $prefix .'_url',
		'type' => 'text_url',
	) );
}