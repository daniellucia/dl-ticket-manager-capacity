<?php

/**
 * Plugin Name: Capacity management for Ticket Manager
 * Description: Simple capacity management for the ticket manager.
 * Version: 0.0.2
 * Author: Daniel Lúcia
 * Author URI: http://www.daniellucia.es
 * textdomain: dl-ticket-manager-capacity
 * Requires Plugins: dl-ticket-manager
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

use DL\TicketManagerCapacity\Plugin;

defined('ABSPATH') || exit;

require_once __DIR__ . '/vendor/autoload.php';

add_action('plugins_loaded', function () {

    load_plugin_textdomain('dl-ticket-manager-capacity', false, dirname(plugin_basename(__FILE__)) . '/languages');

    $plugin = new Plugin();
    $plugin->init();
});
