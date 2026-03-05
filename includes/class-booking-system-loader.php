<?php
/**
 * Register all actions and filters for the plugin.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Booking_System_Loader {

    public static function load_all() {
        self::load_utilities();
        self::load_models();
        self::load_core();
        self::load_admin();
        self::load_public();
    }

    private static function load_utilities() {
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-booking-error.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-result.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-booking-validator.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-encryption-helper.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-booking-system-logger.php';
    }

    private static function load_models() {
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/models/enum-consultation-status.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/models/enum-payment-status.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/models/class-consultation.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/models/class-consultation-type.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/models/class-patient-data.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/models/class-payment-data.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/models/class-availability-rule.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/models/class-blocked-period.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/models/class-time-slot.php';
    }

    private static function load_core() {
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-availability-manager.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-payu-gateway.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-google-meet-integration.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-notification-system.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-booking-engine.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-payment-handler.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-refund-logger.php';
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-cron-manager.php';
    }

    private static function load_admin() {
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'admin/class-booking-system-admin.php';
    }

    private static function load_public() {
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'public/class-booking-system-public.php';
    }
}
