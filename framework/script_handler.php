<?php

	add_action('wp_ajax_get_files','get_files');
	
	function get_files(){
		$image_folder = $_POST['image_folder'];
		
		die ( imageSelector::get_files( $image_folder ) );
	}