<?php

/**
 * Plugin Name: Capacity management for Ticket Manager
 * Description: Simple capacity management for the ticket manager.
 * Version: 0.0.2
 * Author: Daniel Lúcia
 * Author URI: http://www.daniellucia.es
 * textdomain: dl-ticket-manager-capacity
 * Requires Plugins: dl-ticket-manager
 */

use DL\TicketManagerCapacity\Plugin;

defined('ABSPATH') || exit;

require_once __DIR__ . '/vendor/autoload.php';

add_action('plugins_loaded', function () {

    load_plugin_textdomain('dl-ticket-manager-capacity', false, dirname(plugin_basename(__FILE__)) . '/languages');

    (new Plugin())->init();
});
