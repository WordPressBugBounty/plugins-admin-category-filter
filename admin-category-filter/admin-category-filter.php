<?php
/**
 * Plugin Name:       Post Category Filter (WP Admin)
 * Plugin URI:        https://infinitumform.com/projects/admin-category-filter
 * Description:       Quickly search and filter categories and taxonomies inside the WordPress admin.
 * Version:           1.7.5
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Ivijan Stefan Stipic
 * Author URI:        https://www.linkedin.com/in/ivijanstefanstipic/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       admin-category-filter
 * Domain Path:       /languages
 *
 * ------------------------------------------------------------------------
 * This plugin is a maintained continuation (adoption) of the original
 * "Post Category Filter" plugin created by Javier Villanueva (jahvi).
 *
 * Copyright (c) 2013-2018 Javier Villanueva (Original Author)
 * Copyright (c) 2025 Ivijan Stefan Stipic (Maintainer)
 * ------------------------------------------------------------------------
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define constants.
if ( ! defined( 'APCF_VERSION' ) ) {
    define( 'APCF_VERSION', '1.7.5' );
}

if ( ! defined( 'APCF_PLUGIN_FILE' ) ) {
    define( 'APCF_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'APCF_PLUGIN_DIR_PATH' ) ) {
    define( 'APCF_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'APCF_PLUGIN_BASENAME' ) ) {
    define( 'APCF_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'APCF_PLUGIN_URL' ) ) {
    define( 'APCF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// Load text domain.
function pcf_load_textdomain() {
    // Third argument MUST be relative path from WP_PLUGIN_DIR.
    load_plugin_textdomain(
        'admin-category-filter',
        false,
        dirname( APCF_PLUGIN_BASENAME ) . '/languages'
    );
}
add_action( 'plugins_loaded', 'pcf_load_textdomain' );

// Only load in admin screens (non-AJAX bootstrap).
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
    $pcf_class_file = APCF_PLUGIN_DIR_PATH . 'inc/class-category-filter.php';

    // Prevent fatal error if the file is missing during update or partial install.
    if ( file_exists( $pcf_class_file ) ) {
        require_once $pcf_class_file;

        if ( class_exists( 'Post_Category_Filter' ) ) {
            add_action(
                'plugins_loaded',
                array( 'Post_Category_Filter', 'get_instance' )
            );
        }
    } else {
		error_log( '[Admin Category Filter] class-category-filter.php not found at: ' . $pcf_class_file );
    }
}