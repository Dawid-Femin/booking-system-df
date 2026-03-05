<?php
/**
 * Logging system for the booking plugin.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Booking_System_Logger {

    private static $log_file = null;
    private static $max_file_size = 10485760; // 10MB
    private static $max_files = 5;

    private static function get_log_file() {
        if (self::$log_file !== null) {
            return self::$log_file;
        }

        $logs_dir = BOOKING_SYSTEM_DF_PLUGIN_DIR . 'logs';
        
        if (!file_exists($logs_dir)) {
            wp_mkdir_p($logs_dir);
            file_put_contents($logs_dir . '/.htaccess', "Order deny,allow\nDeny from all");
        }
        
        self::$log_file = $logs_dir . '/booking-system.log';
        return self::$log_file;
    }

    private static function rotate_logs() {
        $log_file = self::get_log_file();
        
        if (!file_exists($log_file)) {
            return;
        }
        
        if (filesize($log_file) < self::$max_file_size) {
            return;
        }

        for ($i = self::$max_files - 1; $i > 0; $i--) {
            $old_file = $log_file . '.' . $i;
            $new_file = $log_file . '.' . ($i + 1);
            
            if (file_exists($old_file)) {
                if ($i == self::$max_files - 1) {
                    unlink($old_file);
                } else {
                    rename($old_file, $new_file);
                }
            }
        }
        
        rename($log_file, $log_file . '.1');
    }

    private static function write_log($level, $message, $context = array()) {
        self::rotate_logs();
        
        $timestamp = current_time('Y-m-d H:i:s');
        $context_str = !empty($context) ? ' ' . json_encode($context) : '';
        $log_entry = sprintf("[%s] [%s] %s%s\n", $timestamp, $level, $message, $context_str);
        
        $log_file = self::get_log_file();
        file_put_contents($log_file, $log_entry, FILE_APPEND);
    }

    public static function log_info($message, $context = array()) {
        self::write_log('INFO', $message, $context);
    }

    public static function log_error($message, $context = array()) {
        self::write_log('ERROR', $message, $context);
        
        // Send email to admin for critical errors
        if (isset($context['critical']) && $context['critical']) {
            $admin_email = get_option('admin_email');
            $subject = __('Błąd krytyczny w systemie rezerwacji', 'booking-system-df');
            $body = sprintf(
                __("Wystąpił błąd krytyczny:\n\n%s\n\nCzas: %s\n\nKontekst: %s", 'booking-system-df'),
                $message,
                current_time('Y-m-d H:i:s'),
                json_encode($context)
            );
            wp_mail($admin_email, $subject, $body);
        }
    }

    public static function log_warning($message, $context = array()) {
        self::write_log('WARNING', $message, $context);
    }

    public static function log_debug($message, $context = array()) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            self::write_log('DEBUG', $message, $context);
        }
    }
}
