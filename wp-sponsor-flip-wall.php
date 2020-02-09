<?php
/*
Plugin Name: WP Sponsor Flip Wall
Plugin URI: http://samuelramon.com.br/projects/wp-sponsor-flip-wall
Description: With this plugin you can show your sponsors/partners logo with short description using the power of Wordpress Post Types and CSS 3 flip animation.
Version: 2.0.1
Author: Samuel Ramon
Author URI: http://samuelramon.com.br
License: GPLv2
*/

/*
The MIT License (MIT)

Copyright (c) 2011 Samuel Ramon Barros de Oliveira

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

// Plugin Version
define( 'WP_SFW_VERSION', '2.0.1' );
// Defining the directory separator
define( 'DS', DIRECTORY_SEPARATOR );
// Defining the pluing's sponsor flip wall images directories
define( 'WP_SFW_IMG_DIR', WP_CONTENT_DIR.DS.'uploads'.DS.'sponsors_img'.DS );
// Default plugin images
define( 'WP_SFW_IMAGES_DIR', plugins_url('wp-sponsor-flip-wall/images') );
// Plugin's sponsors image URL directory
define( 'WP_SFW_IMG_URL', site_url('wp-content/uploads/sponsors_img') );
// Plugin's post type
define( 'WP_SFW_POST_TYPE' , 'sfw-post-type' );
//
define( 'WP_SFW_THUMB', 'wp-sfw-thumbnail' );
// Plugin's thumbnail default width
define( 'WP_SFW_THUMB_WIDTH' , 300 );
// Plugin's thumbnail default height
define( 'WP_SFW_THUMB_HEIGHT' , 300 );

// TODO Language

// TODO Config page

// TODO Add background image for smooth

// TODO Add custom field for sponsors dertails like site link

add_image_size( WP_SFW_THUMB, WP_SFW_THUMB_WIDTH, WP_SFW_THUMB_HEIGHT );

add_action('init', 'wp_sfw_enqueue');
function wp_sfw_enqueue() {
	if (!is_admin()) {
		wp_enqueue_style('sponsor.flip.wall', plugins_url('wp-sponsor-flip-wall/assets/css/wp-sfw.css'));
	}
}

add_action('init', 'wp_sfw_create_post_type');
function wp_sfw_create_post_type() {
	$labels = array(
		'name'									=> _x('Sponsors', 'Post type general name', 'textdomain'),
		'singular_name'					=> _x('Sponsor', 'Post type singular name', 'textdomain'),
		'menu_name'							=> _x('SFW', 'Admin menu text', 'textdomain'),
		'name_admin_bar'				=> _x('SWF', 'Add new on toolbar', 'textdomain'),
		'add_new'								=> __('Add New', 'textdomain'),
		'add_new_item'					=> __('Add New Sponsor', 'textdomain'),
		'new_item'							=> __('New Sponsor', 'textdomain'),
		'edit_item'							=> __('Edit Sponsor', 'textdomain'),
		'view_item'							=> __('View Sponsor', 'textdomain'),
		'all_items'							=> __('All Sponsors', 'textdomain'),
		'search_items'					=> __('Search Sponsor', 'textdomain'),
		'parent_item_colon'			=> __('Parent Sponsors:', 'textdomain'),
		'not_found'							=> __('No sponsor found.', 'textdomain'),
		'not_found_in_trash'		=> __('No sponsor found in Trash.', 'textdomain'),
		'featured_image'				=> _x('Sponsor cover image', '', 'textdomain'),
		'set_featured_image'		=> _x('Set cover image', '', 'textdomain'),
		'remove_featured_image'	=> _x('Remove cover image', '', 'textdomain'),
		'use_featured_image'		=> _x('Use as cover image', '', 'textdomain'),
		'archives'							=> _x('Sponsor archives', '', 'textdomain'),
		'uploaded_to_this_item'	=> _x('Uploaded to this sponsor', '', 'textdomain'),
		'filter_items_list'			=> _x('Filter sponsors list', '', 'textdomain'),
		'items_list_navigation'	=> _x('Sponsors list navigation', '', 'textdomain'),
		'items_list'						=> _x('Sponsors list', '', 'textdomain'),
	);

	$args = array(
		'labels' => $labels,
		'menu_icon' => 'dashicons-groups',
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_ui_menu' => true,
		'query_var' => true,
		'rewrite' => array('slug' => 'sponsor'),
		'capability_type' => 'post',
		'has_archive' => true,
		'hierarchical' => false,
		'menu_position' => null,
		'supports' => array('title', 'thumbnail', 'excerpt'),
	);

	register_post_type(WP_SFW_POST_TYPE, $args);
}

/**
 * Function to render the Sponsor Flip Wall
 */
function wp_sfw_render() {
	query_posts( array( 'post_type' => array( WP_SFW_POST_TYPE ) ) );

	// gallery views
	include 'view/wp_sfw_gallery.php';
}

// Delete post type if plugin was removed
register_deactivation_hook(__FILE__, 'wp_sfw_uninstall');
function wp_sfw_uninstall() {
	unregister_post_type(WP_SFW_POST_TYPE);
}
