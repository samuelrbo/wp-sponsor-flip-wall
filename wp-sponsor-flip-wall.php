<?php
/*
Plugin Name: WP Sponsor Flip Wall
Plugin URI: http://phpcafe.com.br/
Description: With this plugin you may insert your partners logo, description and link using the jQuery sponsor flip wall ( <a href="http://tutorialzine.com/2010/03/sponsor-wall-flip-jquery-css/" target="_blank">http://tutorialzine.com/2010/03/sponsor-wall-flip-jquery-css/</a> ).
Version: 0.1
Author: Samuel Ramon
Author URI: http://samu.ca/
License: GPLv2
*/

/*
	Copyright 2011  Samuel Ramon  (email: samuelrbo@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//ini_set('display_errors', 1);
//error_reporting(E_ALL);

// Plugin Version
define( 'WP_SFG_VERSION', '0.1' );
// Defining the directory separator
define( 'DS', DIRECTORY_SEPARATOR );
// Defining the WP upload directory
define( 'WP_UPLOAD_DIR', WP_CONTENT_DIR.DS.'uploads' );
// Defining the pluing's sponsor flip wall images directories
define( 'WP_SFW_IMG_DIR', WP_UPLOAD_DIR.DS.'sponsors_img'.DS );
// Default plugin images
define( 'WP_SFW_IMAGES_DIR', plugins_url('wp-sponsor-flip-wall/images') );
// Plugin's sponsors image URL directory
define( 'WP_SFW_IMG_URL', site_url('wp-content/uploads/sponsors_img') );

include_once 'helpers.php'; // helpers classes
include_once 'sponsorDAO.php'; // Data Access Object to sponsor

// loading plugin language
load_plugin_textdomain( 'wp-sfw-plugin', false, 'wp-sponsor-flip-wall/languages' );

// register plugin activation actions ( create tables and folders )
register_activation_hook( __FILE__ , array('SponsorDAO','createTable'));
// register plugin deactivation actions ( remove tables and folders )
register_deactivation_hook( __FILE__ , array('SponsorDAO','removeTable'));

/**
 * Adding admin javascript and styles
 */
add_action('admin_init', '_admin_scripts');
function _admin_scripts() {
	wp_enqueue_style( 'wp-sfw-style', plugins_url('css/sponsor-flip-wall.css', __FILE__) );
}

/**
 * Initialize Sponsor Flip Wall - Plugin Menu
 */
add_action('admin_menu', '_admin_menu_options');
function _admin_menu_options() {
	if ( function_exists('add_menu_page') && function_exists('add_submenu_page') ) {
		// Adding the plugin menu to WP Admin Menu and seeting it up
		// to be the configuration menu
		add_menu_page(__('WP Sponsor Flip Wall', 'wp-sfw-plugin'), __('WP Sponsor Flip Wall', 'wp-sfw-plugin'), "manage_options", "menu-sponsorflip", "_sponsor_options");

		// Adding plugin submenu "Sponsors": List all included sponsors
		add_submenu_page("menu-sponsorflip", __('Sponsors', 'wp-sfw-plugin'), __('Sponsors', 'wp-sfw-plugin'), 7, "wp-swf-list", "_show_sponsors");
		// Adding plugin submenu "New Sponsor": Add a new sponsor
		add_submenu_page("menu-sponsorflip", __('New Sponsor', 'wp-sfw-plugin'), __('New Sponsor', 'wp-sfw-plugin'), 7, "wp-swf-add", "_add_new_sponsor");
	}
}

/**
 * Initalize Sponsor Flip Wall - Options
 */
add_action('admin_init', '_save_sfw_options');
function _save_sfw_options() {
	// Uninstall configuration
	register_setting('wp_sfw_config', 'wp_sfw_remove_tables');
	register_setting('wp_sfw_config', 'wp_sfw_remove_folders');

	// Image configuration
	register_setting('wp_sfw_config', 'wp_sfw_img_folder');
	register_setting('wp_sfw_config', 'wp_sfw_img_width');
	register_setting('wp_sfw_config', 'wp_sfw_img_height');

	// Image crop configuration
	register_setting('wp_sfw_config', 'wp_sfw_auto_crop');
	register_setting('wp_sfw_config', 'wp_sfw_crop_width');
	register_setting('wp_sfw_config', 'wp_sfw_crop_height');
}

/**
 * Loading the plugin config view
 */
function _sponsor_options() {
	include 'views/sponsor_config.php';
}

/**
 * Loading sponsors table view
 */
function _show_sponsors() {
	// Loading the DAO class
	$dao = new SponsorDAO();
	
	if ( Input::get('remove') ) {
		$dao->remove( $dao->get( Input::get('remove') ) );
		$message = __('A sponsor was removed!', 'wp-sfw-plugin');
	}
	
	$acive_sponsors		= $dao->getAll( 'name', false, false, 'active' ); // Getting all active sponsors
	$inacive_sponsors	= $dao->getAll( 'name', false, false, 'inactive' ); // Getting all inactive sponsors
	$sponsors			= $dao->getAll( 'name' ); // Getting all sponsors

	include 'views/sponsor_list.php';
}

/**
 * Sponsor form to add or edit sponsors
 */
function _add_new_sponsor() {
	$dao = new SponsorDAO();

	if ( Input::get('sponsor') )
		$sponsor = $dao->get(Input::get('sponsor') );
	else
		$sponsor = new Sponsor();

	$response = array();
	if ( Input::post('save') ) {
		try {

			$validate = new Validate($_POST);
			$validate->setRules(array(
				'name' => array('required'),
				'link' => array('prep_url','required','valid_url'),
				'description' => array('required'),
				'status' => array('required')
			));

			$validate->setFields(array(
				'name' => __('Name'),
				'link' => __('Link'),
				'description' => __('Description'),
				'status' => __('Status')
			));

			if ( !$validate->run() )
				throw new Exception( $validate->getErrorMessage() );

			$sponsor->setName( Input::post('name') );
			$sponsor->setDescription( Input::post('description') );
			$sponsor->setLink( Input::post('link') );
			$sponsor->setStatus( Input::post('status') );

			if ( !empty( $_FILES['sponsor_img']['name'] ) ) {
				// TODO - remove old sponsor image
				$img_path = get_option('wp_sfw_img_folder', WP_SFW_IMG_DIR);
				$params = array(
					'upload_path'	=> $img_path,
                    'allowed_types' => 'gif|png|jpg',
                    'encrypt_name'  => true,
					'max_width'		=> get_option('wp_sfw_img_width', '140'),
					'max_height'	=> get_option('wp_sfw_img_height', '140'),
				);
				$upload = new Upload($params);

				if ( !$upload->do_upload('sponsor_img') )
					throw new Exception( $upload->display_errors() );

				$data = $upload->data();
				$sponsor->setImg( $data['file_name'] );

				if ( get_option('wp_sfw_auto_crop') == '1' ) {
					$imgLib = new Image(array(
						'source_image' => $img_path.DS.$sponsor->getImg(),
						'width' => get_option('wp_sfw_crop_width','140'),
						'height' => get_option('wp_sfw_crop_height','140'),
						'maintain_ratio' => false
					));

					if (!$imgLib->resize())
						throw new Exception($imgLib->display_errors());

					$imgLib->clear();
				}
			}

			$sponsor = $dao->save( $sponsor );
			$response = array(
				'success' => true,
				'message' => __('Success! Sponsor saved', 'wp-sfw-plugin')
			);
		} catch ( Exception $e ) {
			$response = array(
				'success' => false,
				'message' => $e->getMessage()
			);
		}
	}

	include 'views/sponsor_new.php';
}

/**
 * Function to remove sponsor image
 * Throw Exception in failure.
 *
 * @access private
 * @param Sponsor $sponsor
 * @return 
 */
function _remove_sponsor_img( Sponsor $sponsor ) {
	// Check if the sponsor image exists
	if ( !file_exists( $sponsor->getImgDir(false) ) )
		throw new Exception( __("The sponsor's image doesn't exists", 'wp-sfw-plugin') );

	// Remove the sponsor image from the server
	$img_directory = $sponsor->getImgDir(false);
	unset( $img_directory );
	// Seeting empty for the sponsor image
	$sponsor->setImg('');

	// Loading the SponsorDAO
	$dao = new SponsorDAO();
	// Saving the sponsor without img and return the sponsor object
	return $dao->save( $sponsor );
}

// adding plugin ajax support
add_action('wp_ajax_remove_sponsor_image', 'remove_sponsor_image_callback');
function remove_sponsor_image_callback() {
	try {
		$validate = new Validate( $_POST );
		$validate->setRules('sponsor_id', array('required','numeric'));
		$validate->setFields('sponsor_id', __('Sponsor', 'wp-sfw-plugin'));

		if ( !$validate->run() )
			throw new Exception( $validate->getErrorMessage() );

		$dao = new SponsorDAO();
		$sponsor = $dao->get( Input::post('sponsor_id') );

		_remove_sponsor_img($sponsor);

		$response = array(
			'success' => true,
			'message' => __('Success! The sponsor image was removed', 'wp-sfw-plugin')
		);
	} catch ( Exception $e ) {
		$response = array(
			'success' => false,
			'message' => $e->getMessage()
		);
	}

	echo json_encode($response);
	exit; // For wordpress ajax you have to exit or wordpress will return the value 1 in success or 0 in error
}

add_action('init', 'wp_sfw_enqueue');
function wp_sfw_enqueue() {
	if (!is_admin()) {
		wp_enqueue_script('jquery.ui.flip', plugins_url('wp-sponsor-flip-wall/js/jquery.ui.js'), array('jquery'));
		wp_enqueue_script('jquery.flip', plugins_url('wp-sponsor-flip-wall/js/jquery.flip.js'), array('jquery','jquery.ui.flip'));

		wp_enqueue_style('sponsor.flip.wall', plugins_url('wp-sponsor-flip-wall/css/sponsor-flip-wall.css'));
	}
}

/**
 * Function to render the Sponsor Flip Wall
 */
function wp_sfw_render() {
	// init the DAO
	$dao = new SponsorDAO();
	// getting all active sponsors
	$sponsors = $dao->getAll( 'name', false, false, 'active' ); // Getting all active sponsors

	// gallery views
	include 'views/sponsor_gallery_render.php';
}
