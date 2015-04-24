<?php

/**
 * Plugin Name: WP REST API Custom Fields
 * Description: Adds meta fields to WP Rest JSON output.
 * Version: 0.1
 * Author: André Boekhorst (based on work from @panman)
 * Author URI: http://www.andreboekhorst.nl
 * Plugin URI: https://github.com/andreboekhorst/wp-api-custom-fields/
 */


// Add Meta Fields to Posts and Pages
add_filter( 'json_prepare_post', 'addACFmeta' ); 
add_filter( 'json_prepare_page', 'addACFmeta' ); 

function addACFmeta( $_post ){

    if( function_exists( 'get_fields' ) ){
        $ACF = get_fields( $_post['ID'] );
        foreach( $ACF as $key => &$custom_field ){
            $custom_field = apply_filters( 'JSON_META_' . $key, $custom_field, $_post );
        }
        $_post['meta'] = array_merge( $_post['meta'], $ACF );    
    }
    
    return $_post;

}


// Add Meta Fields to Taxonomy
add_filter('json_prepare_term', 'wp_api_encode_acf_taxonomy', 10, 2);

function wp_api_encode_acf_taxonomy($data,$post){

    $ACF = (array) get_fields($post->taxonomy."_". $post->term_id );

    foreach( $ACF as $key => &$custom_field ){
        $custom_field = apply_filters( 'JSON_META_' . $key, $custom_field, $post );
    }
    
    $data['meta'] = array_merge($data['meta'], $ACF );

    return $data;

}


// Add meta fields to user.
add_filter('json_prepare_user', 'wp_api_encode_acf_user', 10, 2);

function wp_api_encode_acf_user($data,$post){

    $customMeta = (array) get_fields("user_". $data['ID']);
    $data['meta'] = array_merge($data['meta'], $customMeta );

    return $data;

}


