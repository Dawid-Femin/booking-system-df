<?php
/**
 * Cron manager for scheduled tasks.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Cron_Manager {

    public static function init() {
        add_action('booking_system_df_send_reminders_24h', array(__CLASS__, 'send_reminders_24h'));
        add_action('booking_system_df_send_reminders_1h', array(__CLASS__, 'send_reminders_1h'));
        add_action('booking_system_df_mark_completed', array(__CLASS__, 'mark_completed_consultations'));
        add_action('booking_system_df_cleanup_old_data', array(__CLASS__, 'cleanup_old_data'));
    }

    public static function send_reminders_24h() {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultations';
        
        $tomorrow_start = date('Y-m-d H:i:s', strtotime('+23 hours'));
        $tomorrow_end = date('Y-m-d H:i:s', strtotime('+25 hours'));
        
        $consultations = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
            WHERE status = %s 
            AND start_datetime BETWEEN %s AND %s",
            ConsultationStatus::CONFIRMED,
            $tomorrow_start,
            $tomorrow_end
        ));
        
        foreach ($consultations as $row) {
            $consultation = Consultation::get_by_id($row->id);
            Notification_System::send_reminder_24h($consultation);
        }
        
        Booking_System_Logger::log_info('24h reminders sent', array('count' => count($consultations)));
    }

    public static function send_reminders_1h() {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultations';
        
        $hour_start = date('Y-m-d H:i:s', strtotime('+55 minutes'));
        $hour_end = date('Y-m-d H:i:s', strtotime('+65 minutes'));
        
        $consultations = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
            WHERE status = %s 
            AND start_datetime BETWEEN %s AND %s",
            ConsultationStatus::CONFIRMED,
            $hour_start,
            $hour_end
        ));
        
        foreach ($consultations as $row) {
            $consultation = Consultation::get_by_id($row->id);
            Notification_System::send_reminder_1h($consultation);
        }
        
        Booking_System_Logger::log_info('1h reminders sent', array('count' => count($consultations)));
    }

    public static function mark_completed_consultations() {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultations';
        
        $past_time = date('Y-m-d H:i:s', strtotime('-1 hour'));
        
        $consultations = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
            WHERE status = %s 
            AND end_datetime < %s",
            ConsultationStatus::CONFIRMED,
            $past_time
        ));
        
        foreach ($consultations as $row) {
            Booking_Engine::mark_as_completed($row->id);
        }
        
        Booking_System_Logger::log_info('Consultations marked as completed', array('count' => count($consultations)));
    }

    public static function cleanup_old_data() {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultations';
        
        // Delete cancelled consultations older than 90 days
        $old_date = date('Y-m-d H:i:s', strtotime('-90 days'));
        
        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM $table 
            WHERE status = %s 
            AND cancelled_at < %s",
            ConsultationStatus::CANCELLED,
            $old_date
        ));
        
        Booking_System_Logger::log_info('Old data cleaned up', array('deleted' => $deleted));
    }
}

Cron_Manager::init();
