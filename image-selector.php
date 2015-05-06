<?php
	defined('ABSPATH') or die ("No script kiddies please!");
	/**
	* Plugin Name: Image Selector
	* Plugin URI:
	* Description: A plugin that sets the feature image automatically
	* Version: 1.0.2
	* Author: Mike Marzano, Kevin Martinez
	* Author URI:
	* License: GPL2
	*
	*/
	define( 'PLUG_DIR_IMG_S', plugin_dir_path( __FILE__ ) );
	define( 'PLUG_URI_IMG_S', plugin_dir_url( __FILE__ ) );
	define( 'IMG_PREFIX', '_img_plugin_' );
	
	require_once( PLUG_DIR_IMG_S.'framework/classes/imageSelector.php' );
	require_once( PLUG_DIR_IMG_S.'framework/script_handler.php' );
	include( PLUG_DIR_IMG_S.'framework/accommodation-config.php' );
	
	$img = new imageSelector( $accoMeta );