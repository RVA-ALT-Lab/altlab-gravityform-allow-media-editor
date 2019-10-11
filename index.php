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
    wp_enqueue_media();
}

add_action( 'wp_enqueue_scripts', 'alt_lab_front_end_scripts' );
add_action('wp_ajax_nopriv_parse-embed', 'parse-embed');


function fake_user(){
    global $wp_rest_auth_cookie;    
    wp_set_auth_cookie( 1, false, '', '' );
}

add_action('init','fake_user');

//https://www.youtube.com/watch?v=FF6Bp5AAjcE
/**
 * Apply [embed] Ajax handlers to a string.
 *
 * @since 4.0.0
 *
 * @global WP_Post    $post       Global $post.
 * @global WP_Embed   $wp_embed   Embed API instance.
 * @global WP_Scripts $wp_scripts
 * @global int        $content_width
 */
// function wp_ajax_parse_embed() {
//     global $post, $wp_embed, $content_width;

//     if ( empty( $_POST['shortcode'] ) ) {
//         wp_send_json_error();
//     }
//     $post_id = isset( $_POST['post_ID'] ) ? intval( $_POST['post_ID'] ) : 0;
//     if ( $post_id > 0 ) {
//         $post = get_post( $post_id );
//         if ( ! $post || ! current_user_can( 'edit_post', $post->ID ) ) {
//             wp_send_json_error();
//         }
//         setup_postdata( $post );
//     } elseif ( ! current_user_can( 'edit_posts' ) ) { // See WP_oEmbed_Controller::get_proxy_item_permissions_check().
//         wp_send_json_error();
//     }

//     $shortcode = wp_unslash( $_POST['shortcode'] );

//     preg_match( '/' . get_shortcode_regex() . '/s', $shortcode, $matches );
//     $atts = shortcode_parse_atts( $matches[3] );
//     if ( ! empty( $matches[5] ) ) {
//         $url = $matches[5];
//     } elseif ( ! empty( $atts['src'] ) ) {
//         $url = $atts['src'];
//     } else {
//         $url = '';
//     }

//     $parsed                         = false;
//     $wp_embed->return_false_on_fail = true;

//     if ( 0 === $post_id ) {
//         /*
//          * Refresh oEmbeds cached outside of posts that are past their TTL.
//          * Posts are excluded because they have separate logic for refreshing
//          * their post meta caches. See WP_Embed::cache_oembed().
//          */
//         $wp_embed->usecache = false;
//     }

//     if ( is_ssl() && 0 === strpos( $url, 'http://' ) ) {
//         // Admin is ssl and the user pasted non-ssl URL.
//         // Check if the provider supports ssl embeds and use that for the preview.
//         $ssl_shortcode = preg_replace( '%^(\\[embed[^\\]]*\\])http://%i', '$1https://', $shortcode );
//         $parsed        = $wp_embed->run_shortcode( $ssl_shortcode );

//         if ( ! $parsed ) {
//             $no_ssl_support = true;
//         }
//     }

//     // Set $content_width so any embeds fit in the destination iframe.
//     if ( isset( $_POST['maxwidth'] ) && is_numeric( $_POST['maxwidth'] ) && $_POST['maxwidth'] > 0 ) {
//         if ( ! isset( $content_width ) ) {
//             $content_width = intval( $_POST['maxwidth'] );
//         } else {
//             $content_width = min( $content_width, intval( $_POST['maxwidth'] ) );
//         }
//     }

//     if ( $url && ! $parsed ) {
//         $parsed = $wp_embed->run_shortcode( $shortcode );
//     }

//     if ( ! $parsed ) {
//         wp_send_json_error(
//             array(
//                 'type'    => 'not-embeddable',
//                 /* translators: %s: URL which cannot be embedded, between code tags */
//                 'message' => sprintf( __( '%s failed to embed.' ), '<code>' . esc_html( $url ) . '</code>' ),
//             )
//         );
//     }

//     if ( has_shortcode( $parsed, 'audio' ) || has_shortcode( $parsed, 'video' ) ) {
//         $styles     = '';
//         $mce_styles = wpview_media_sandbox_styles();
//         foreach ( $mce_styles as $style ) {
//             $styles .= sprintf( '<link rel="stylesheet" href="%s"/>', $style );
//         }

//         $html = do_shortcode( $parsed );

//         global $wp_scripts;
//         if ( ! empty( $wp_scripts ) ) {
//             $wp_scripts->done = array();
//         }
//         ob_start();
//         wp_print_scripts( array( 'mediaelement-vimeo', 'wp-mediaelement' ) );
//         $scripts = ob_get_clean();

//         $parsed = $styles . $html . $scripts;
//     }

//     if ( ! empty( $no_ssl_support ) || ( is_ssl() && ( preg_match( '%<(iframe|script|embed) [^>]*src="http://%', $parsed ) ||
//         preg_match( '%<link [^>]*href="http://%', $parsed ) ) ) ) {
//         // Admin is ssl and the embed is not. Iframes, scripts, and other "active content" will be blocked.
//         wp_send_json_error(
//             array(
//                 'type'    => 'not-ssl',
//                 'message' => __( 'This preview is unavailable in the editor.' ),
//             )
//         );
//     }

//     $return = array(
//         'body' => $parsed,
//         'attr' => $wp_embed->last_attr,
//     );

//     if ( strpos( $parsed, 'class="wp-embedded-content' ) ) {
//         if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
//             $script_src = includes_url( 'js/wp-embed.js' );
//         } else {
//             $script_src = includes_url( 'js/wp-embed.min.js' );
//         }

//         $return['head']    = '<script src="' . $script_src . '"></script>';
//         $return['sandbox'] = true;
//     }

//     wp_send_json_success( $return );
// }
