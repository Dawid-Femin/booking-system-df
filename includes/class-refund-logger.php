<?php
/**
 * Refund logger for tracking refunds.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Refund_Logger {

    public static function log_refund($consultation_id, $order_id, $amount, $reason) {
        $log_entry = array(
            'consultation_id' => $consultation_id,
            'order_id' => $order_id,
            'amount' => $amount,
            'reason' => $reason,
            'timestamp' => current_time('mysql')
        );
        
        Booking_System_Logger::log_info('Refund initiated', $log_entry);
        
        // Store in database for reporting
        global $wpdb;
        $table = $wpdb->prefix . 'booking_settings';
        
        $refunds = get_option('booking_system_df_refunds', array());
        $refunds[] = $log_entry;
        update_option('booking_system_df_refunds', $refunds);
    }

    public static function get_all_refunds() {
        return get_option('booking_system_df_refunds', array());
    }
}
