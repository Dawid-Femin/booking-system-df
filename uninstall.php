<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */

// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Drop tables
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}booking_consultations");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}booking_consultation_types");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}booking_availability_rules");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}booking_blocked_periods");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}booking_settings");

// Delete options
delete_option('booking_system_df_timezone');
delete_option('booking_system_df_encryption_key');

// Clear scheduled cron jobs
wp_clear_scheduled_hook('booking_system_df_send_reminders_24h');
wp_clear_scheduled_hook('booking_system_df_send_reminders_1h');
wp_clear_scheduled_hook('booking_system_df_mark_completed');
wp_clear_scheduled_hook('booking_system_df_cleanup_old_data');
