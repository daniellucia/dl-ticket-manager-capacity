<?php

/**
 * Plugin Name: Capacity management for Ticket Manager
 * Description: Gestión de aforo sencillo para el gestor de tickets.
 * Version: 0.0.2
 * Author: Daniel Lúcia
 * Author URI: http://www.daniellucia.es
 * textdomain: dl-ticket-manager-capacity
 * Requires Plugins: dl-ticket-manager
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/src/Plugin.php';

add_action('plugins_loaded', function () {

    load_plugin_textdomain('dl-ticket-manager-capacity', false, dirname(plugin_basename(__FILE__)) . '/languages');

    $plugin = new TMCapacityManagementPlugin();
    $plugin->init();
});
