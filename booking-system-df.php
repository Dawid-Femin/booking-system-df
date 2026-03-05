<?php
/**
 * Plugin Name: System Rezerwacji Konsultacji DF
 * Plugin URI: https://dawidfemin.pl/booking-system
 * Description: System rezerwacji konsultacji psychologicznych z integracją PayU i Google Meet
 * Version: 1.0.0
 * Author: Dawid Femin
 * Author URI: https://dawidfemin.pl
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: booking-system-df
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 */
define('BOOKING_SYSTEM_DF_VERSION', '1.0.0');
define('BOOKING_SYSTEM_DF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BOOKING_SYSTEM_DF_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_booking_system_df() {
    require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-booking-system-activator.php';
    Booking_System_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_booking_system_df() {
    require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-booking-system-deactivator.php';
    Booking_System_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_booking_system_df');
register_deactivation_hook(__FILE__, 'deactivate_booking_system_df');

/**
 * The core plugin class.
 */
require BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-booking-system.php';

/**
 * Begins execution of the plugin.
 */
function booking_system_df_init() {
    $plugin = new Booking_System();
    $plugin->run();
}
add_action('plugins_loaded', 'booking_system_df_init');
