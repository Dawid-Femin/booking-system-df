<?php
/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Booking_System_Activator {

    public static function activate() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        // Table: wp_booking_consultations
        $table_consultations = $wpdb->prefix . 'booking_consultations';
        $sql_consultations = "CREATE TABLE $table_consultations (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            consultation_type_id bigint(20) UNSIGNED NOT NULL,
            start_datetime datetime NOT NULL,
            end_datetime datetime NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'pending_payment',
            patient_name_encrypted text NOT NULL,
            patient_email_encrypted text NOT NULL,
            patient_phone_encrypted text NOT NULL,
            patient_notes_encrypted text,
            payment_order_id varchar(255),
            payment_status varchar(20) NOT NULL DEFAULT 'pending',
            payment_amount decimal(10,2) NOT NULL,
            payment_currency varchar(3) NOT NULL DEFAULT 'PLN',
            payment_completed_at datetime,
            google_meet_link varchar(500),
            google_meet_event_id varchar(255),
            confirmation_token varchar(64),
            confirmed_at datetime,
            cancelled_at datetime,
            cancellation_reason text,
            refund_amount decimal(10,2),
            refund_status varchar(20),
            refund_completed_at datetime,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY consultation_type_id (consultation_type_id),
            KEY start_datetime (start_datetime),
            KEY status (status),
            KEY payment_order_id (payment_order_id),
            KEY confirmation_token (confirmation_token)
        ) ENGINE=InnoDB $charset_collate;";
        
        dbDelta($sql_consultations);
        
        // Table: wp_booking_consultation_types
        $table_types = $wpdb->prefix . 'booking_consultation_types';
        $sql_types = "CREATE TABLE $table_types (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            description text,
            duration_minutes int(11) NOT NULL,
            price decimal(10,2) NOT NULL,
            currency varchar(3) NOT NULL DEFAULT 'PLN',
            is_active tinyint(1) NOT NULL DEFAULT 1,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY is_active (is_active)
        ) ENGINE=InnoDB $charset_collate;";
        
        dbDelta($sql_types);
        
        // Table: wp_booking_availability_rules
        $table_rules = $wpdb->prefix . 'booking_availability_rules';
        $sql_rules = "CREATE TABLE $table_rules (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            day_of_week tinyint(1) NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY day_of_week (day_of_week),
            KEY is_active (is_active)
        ) ENGINE=InnoDB $charset_collate;";
        
        dbDelta($sql_rules);
        
        // Table: wp_booking_blocked_periods
        $table_blocked = $wpdb->prefix . 'booking_blocked_periods';
        $sql_blocked = "CREATE TABLE $table_blocked (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            start_datetime datetime NOT NULL,
            end_datetime datetime NOT NULL,
            reason text,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY start_datetime (start_datetime),
            KEY end_datetime (end_datetime)
        ) ENGINE=InnoDB $charset_collate;";
        
        dbDelta($sql_blocked);
        
        // Table: wp_booking_settings
        $table_settings = $wpdb->prefix . 'booking_settings';
        $sql_settings = "CREATE TABLE $table_settings (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            setting_key varchar(255) NOT NULL,
            setting_value longtext,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY setting_key (setting_key)
        ) ENGINE=InnoDB $charset_collate;";
        
        dbDelta($sql_settings);
        
        // Set default timezone
        update_option('booking_system_df_timezone', 'Europe/Warsaw');
        
        // Schedule cron jobs
        if (!wp_next_scheduled('booking_system_df_send_reminders_24h')) {
            wp_schedule_event(time(), 'hourly', 'booking_system_df_send_reminders_24h');
        }
        
        if (!wp_next_scheduled('booking_system_df_send_reminders_1h')) {
            wp_schedule_event(time(), 'hourly', 'booking_system_df_send_reminders_1h');
        }
        
        if (!wp_next_scheduled('booking_system_df_mark_completed')) {
            wp_schedule_event(time(), 'hourly', 'booking_system_df_mark_completed');
        }
        
        if (!wp_next_scheduled('booking_system_df_cleanup_old_data')) {
            wp_schedule_event(time(), 'daily', 'booking_system_df_cleanup_old_data');
        }
        
        // Create logs directory
        $logs_dir = BOOKING_SYSTEM_DF_PLUGIN_DIR . 'logs';
        if (!file_exists($logs_dir)) {
            wp_mkdir_p($logs_dir);
            
            // Protect logs directory
            $htaccess_content = "Order deny,allow\nDeny from all";
            file_put_contents($logs_dir . '/.htaccess', $htaccess_content);
        }
    }
}
