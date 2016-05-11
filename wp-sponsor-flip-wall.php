<?php defined( 'WPINC' ) or die( 'No direct access allowed' );
/*
Plugin Name: WP Sponsor Flip Wall
Plugin URI: http://samuellabs.com.br/dev/contributions/wp-sponsor-flip-wall
Description: This plugin was made to present yours sponsors's details in a beautiful page. The presentation display your active sponsors in a wall with a flip event to show behind the sponsor's logo a short description and a link to the its page. This plugin is based in the tutorial of Devid Walsh: <a href="https://davidwalsh.name/css-flip" target="_blank">CSS Flip</a>
Version: 2.0
Author: Samuel Ramon <samuel.ramon@gmail.com>
Author URI: http://samuelramon.com.br
License: GPLv2
*/

/*
	Copyright 2011  Samuel Ramon  (email: samue.ramon@gmail.com)

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

// Plugin Version
define( 'WP_SFW_VERSION', '2.0' );

interface WP_SFW_Page {

}

/**
 * Plugin setup class
 *
 * @Version 1.0
 * @Author Samuel Ramon <samuell.ramon@gmail.com>
 */
class WP_SFW_Setup
{
    private $_options = array(

    );

    public function __construct()
    {
        $this->_loadOptions();
        add_action( 'admin_menu', array( $this, 'addPage' ) );
        add_action( 'admin_init', array( $this, 'pageInit' ) );
    }

    public function install()
    {

    }

    public function uninstall()
    {

    }

    public function _loadOptions()
    {

    }

    public function addPage()
    {
        add_options_page(
            'Settings Admin',
            'My Settings',
            'manage_options',
            'my-settings-admin'
        );
    }

    public function pageInit()
    {

    }
}
