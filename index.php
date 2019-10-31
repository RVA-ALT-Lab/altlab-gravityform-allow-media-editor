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

//from the Alan Levine @cogdog
add_action('init', 'splot_invisible_user');

function splot_invisible_user() {
    if ( !is_user_logged_in() ) {

    $user_id = username_exists( 'splotcookie' );

    if ( !$user_id ) {
        $random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
        //get the  domain so we can make a fake email
        $urlparts = parse_url(site_url());
        $domain = $urlparts [host];

        $user_id = wp_create_user( 'splotcookie', $random_password, 'splotcookie@' . $domain  );

        $user = new \WP_User( $user_id );
        $user->set_role( 'author' );
    }

    global $wp_rest_auth_cookie;    
    wp_set_auth_cookie( $user_id, false, '', '' );
    }
}


add_action('after_setup_theme', 'splot_remove_admin_bar');

function splot_remove_admin_bar() {
    $current_user = wp_get_current_user();
    if ( $current_user->user_login == 'splotcookie'  ) show_admin_bar(false);
}