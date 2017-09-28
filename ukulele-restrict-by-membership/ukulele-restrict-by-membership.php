<?php
/**
 * Plugin Name: Ukulele Restrict by Membership
 * Plugin URI: http://ukulelecheats.com/
 * Description: Allows or restricts content depending on user level of Membership
 * Author: Maximo Leza
 * Author URI: https://www.upwork.com/o/profiles/users/_~012d9d1278bdc04412/
 * Version: 1.0
 * Text Domain: ukulele
 * Domain Path: /languages
 *
 * Copyright (C) 2017 Maximo Leza.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * See <http://www.gnu.org/licenses/> for the GNU General Public License
 *
 * ███╗   ███╗ █████╗ ██╗   ██╗
 * ████╗ ████║██╔══██╗╚██╗ ██╔╝
 * ██╔████╔██║███████║ ╚████╔╝ 
 * ██║╚██╔╝██║██╔══██║ ██╔╝██╗
 * ██║ ╚═╝ ██║██║  ██║██╔╝ ╚██╗ 
 * ╚═╝     ╚═╝╚═╝  ╚═╝╚═╝   ╚═╝
 */

define( 'UKULELE_VIEWS_DIR', '/views/' );
define( 'UKULELE_LEVEL_LIMIT', 10 );
define( 'UKULELE_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'UKULELE_CLASSES_DIR', UKULELE_PLUGIN_DIR . '/classes/' );

register_activation_hook( __FILE__, 'ukulele_plugin_activation' );
register_deactivation_hook( __FILE__, 'ukulele_plugin_deactivation' );

add_action( 'init', 'ukulele_init' );

function ukulele_init() {
    require_once UKULELE_CLASSES_DIR . '/class-ukulele-settings-page.php';
    require_once UKULELE_CLASSES_DIR . '/class-ukulele-membership-controller.php';

    /*  Add the taxonomy Tag for Pages */
    register_taxonomy( 'page_tag', 'page', array(
        'hierarchical' => false,
        'query_var' => 'tag',
        'public' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        '_builtin' => true,
        'show_in_rest' => true,
    ) );
}

function ukulele_plugin_activation() {
    register_uninstall_hook( __FILE__, 'ukulele_membership_uninstall' );
}

function ukulele_plugin_deactivation() {
    /*  Nothing to do yet */
}

function ukulele_membership_uninstall() {
	delete_option( 'ukulele_settings' );
}


























