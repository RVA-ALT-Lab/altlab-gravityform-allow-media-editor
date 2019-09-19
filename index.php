<?php 
/*
Plugin Name: ALT Lab Gravity Form Allow Media Upload Editor
Plugin URI:  https://github.com/
Description: lets the rich text editor take file uploads on the form side
Version:     1.0
Author:      ALT Lab
Author URI:  http://altlab.vcu.edu
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: my-toolset

*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function show_media_button( $editor_settings, $field_object, $form, $entry ) {
    $editor_settings['media_buttons'] = true;
    $editor_settings['wpautop']       = true;
    return $editor_settings;
}
add_filter( 'gform_rich_text_editor_options', 'show_media_button', 10, 4 );

function alt_lab_front_end_scripts(){
	wp_enqueue_editor();		
	wp_enqueue_script( 'mce-view', '', array('tiny_mce') );	
}


add_action( 'wp_enqueue_scripts', 'alt_lab_front_end_scripts' );
