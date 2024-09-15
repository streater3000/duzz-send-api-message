<?php

/**
 * Plugin Name: Duzz Send API Message
 * Description: A WordPress plugin to send messages via a custom API, integrating with the WordPress messaging system.
 * Version: 1.0.0
 * Author: Streater Kelley
 */


namespace DuzzApi;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Including WordPress's plugin utility functions
require_once(ABSPATH . 'wp-admin/includes/plugin.php');

// Check if the required plugin is active
if (!is_plugin_active('duzz-custom-portal/duzz-custom-portal.php')) {
    return;  // Exit if the required plugin is not active
}

// Include the Composer autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// Use the necessary classes.
use DuzzApi\Controller\ApiController;
use DuzzApi\Controller\DuzzAPI_Menu;
use DuzzApi\Controller\DuzzAPI_Admin;
use DuzzApi\Controller\Duzz_ChangelogController;

// Initialize the API controller.
new ApiController();

new Duzz_ChangelogController();

// Initialize the DuzzAPI_Menu.
$duzzApiMenu = new DuzzAPI_Menu();
$duzzApiMenu->setup();

// The DuzzApi_Menu_Items class uses a static method for initialization.
DuzzAPI_Admin::init();
