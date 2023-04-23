<?php

/*
Plugin Name: Edit Registration Date
Description: Very simple plugin that allows you to easily edit a user's registration date in WordPress.
Author: Tyler Gilbert
Author URI: https://tcgilbert.com/
Version: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: edit-registration-date
Domain Path: /lang

Edit Registration Date is free software you can redistribute
it and/or modify it under the terms of the GNU General Public License
as published by the Free Software Foundation, either version 2 of the
License, or any later version.
Edit Registration Date is distributed in the hope that it
will be useful, but WITHOUT ANY WARRANTY; without even the implied
warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
the GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with Edit Registration Date. If not, see
https://www.gnu.org/licenses/gpl-2.0.html.
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! defined( 'EDIT_REGISTRATION_DATE_PLUGIN_URL' ) ) {
  define( 'EDIT_REGISTRATION_DATE_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
}

if ( is_admin() ) {
  require_once( plugin_dir_path( __FILE__ ) . '/Edit_Registration_Date.php' );

  Edit_Registration_Date::instance();
}

/**
 * Load the translated strings.
 * 
 * @return void
 */
function edit_registration_date_textdomain() {
  load_plugin_textdomain( 'edit-registration-date', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}
add_action( 'init', 'edit_registration_date_textdomain' );