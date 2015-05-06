<?php

$accoMeta = array(
	'post_type' => array(
		'id' => 'accommodation',
		'labels' => array (
			'name' => __('Accommodation'),
			'singular_name' => __( 'Accommodation' ),
			'add_new' => __('Add New'),
			'add_new_item' => __('Add New Accommodation'),
			'edit_item' => __('Edit Accommodation'),
			'new_item' => __('New Accommodation'),
			'view_item' => __('View Accommodation'),
			'search_items' => __('Search Accommodation'),
			'not_found' =>  __('No Accommodations Found'),
			'not_found_in_trash' => __('No Accommodations Found in Trash'),
		),
		'public' => true,
		'exclude_from_search' => false,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions'),
		'rewrite' => array('slug' => __('accommodation')),
	),
	
	'taxonomies' => array(
		array(
			'taxonomy' => 'accommodation_category',
			'object_type' => array('accommodation'),
			'settings' => array(
				'hierarchical' => true,
				'show_in_nav_menus' => true,			
				'labels' => array(
					'name' => __( 'Accommodation Categories'),
					'singular_name' => __( 'Accommodation Category'),
					'menu_name' => __( 'Categories' ),
					'search_items' => __( 'Search Accommodation Categories'),
					'all_items' => __( 'All Accommodation Categories'),
					'parent_item' => __( 'Parent Accommodation Category'),
					'parent_item_colon' => __( 'Parent Accommodation Category'),
					'edit_item' => __( 'Edit Accommodation Category'),
					'update_item' => __( 'Update Accommodation Category'),
					'add_new_item' => __( 'Add New Accommodation Category'),
					'new_item_name' => __( 'New Accommodation Category Name')
				),
				'rewrite' => array(
					'slug' => __('accommodation'),
					'hierarchical' => true,
				)
			)
		),
		array(
			'taxonomy' => 'accommodation_type',
			'object_type' => array('accommodation'),
			'settings' => array(
				'hierarchical' => true,
				'show_in_nav_menus' => true,			
				'show_admin_column' => true,
				'labels' => array(
					'name' => __( 'Types'),
	                'singular_name' => __( 'Type'),
					'menu_name' => __( 'Types' ),
	                'search_items' => __( 'Search Types'),
	                'all_items' => __( 'All Types'),
	                'parent_item' => __( 'Parent Type'),
	                'parent_item_colon' => __( 'Parent Type'),
	                'edit_item' => __( 'Edit Type'),
	                'update_item' => __( 'Update Type'),
	                'add_new_item' => __( 'Add New Type'),
	                'new_item_name' => __( 'New Type Name')
	            ),
				'rewrite' => array(
					'slug' => __('types'),
					'hierarchical' => true,
				),
			)
		)
	),
	
	'post_meta' => array(
		'id' => 'accommodation_metabox',
		'title' =>  __('Hotel Options', 'midway'),
		'page' => 'accommodation',
		'context' => 'normal',
		'priority' => 'high',
		'options' => array(					
			array(	
				'name' => __('Price', 'midway'),
				'id' => 'price',
				'type' => 'number',
				'default' => '0',
				'min'	=> '0',
			),
					
			array(
				'name' => __('Address', 'midway'),
				'id' => 'address',
				'type' => 'text',
			),
			
			array(	
				'name' => __('Display Map', 'midway'),
				'id' => 'map',
				'type' => 'select',
				'description' => __('Enabling this field will display the map of the hotel', 'midway'),
				'options' => array(
					'1' => __('Enable', 'midway'),
					'0' => __('Disable','midway'),
				),
			),
				
			array(
				'name' => __('Stars', 'midway'),
				'id' => 'stars',
				'type' => 'select',
				'options' => array(
					'0' => __('0','midway'),
					'1' => __('1', 'midway'),
					'1.5' => __('1.5', 'midway'),
					'2' => __('2', 'midway'),
					'2.5' => __('2.5', 'midway'),
					'3' => __('3', 'midway'),
					'3.5' => __('3.5', 'midway'),
					'4' => __('4', 'midway'),
					'4.5' => __('4.5', 'midway'),
					'5' => __('5', 'midway'),
				),
			),
					
			array(	
				'name' => __('User-Rating', 'midway'),
				'id' => 'user-rating',
				'type' => 'text',
			),
					
			array(	
				'name' => __('Awards', 'midway'),
				'id' => 'awards',
				'type' => 'textarea',
			),
									
			array(	
				'name' => __('Booking URL', 'midway'),
				'id' => 'booking_url',
				'type' => 'text',
				'description' => __('Enter booking page URL to replace the default booking form', 'midway'),
			),
					
					/*array(	
						'name' => __('Image Folder Name', 'midway'),
						'id' => 'image_folder',
						'type' => 'text',
						'description' => __('This will be the name of the folder that contains the images of the hotel', 'midway'),
						'value' => 'New-Hotel',
						),*/
		)
	)
);