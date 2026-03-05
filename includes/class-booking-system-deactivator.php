<?php
/**
 * Fired during plugin deactivation.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Booking_System_Deactivator {

    public static function deactivate() {
        // Clear scheduled cron jobs
        wp_clear_scheduled_hook('booking_system_df_send_reminders_24h');
        wp_clear_scheduled_hook('booking_system_df_send_reminders_1h');
        wp_clear_scheduled_hook('booking_system_df_mark_completed');
        wp_clear_scheduled_hook('booking_system_df_cleanup_old_data');
    }
}
