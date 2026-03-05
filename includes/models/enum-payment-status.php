<?php
/**
 * Payment status enum.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class PaymentStatus {
    const PENDING = 'pending';
    const COMPLETED = 'completed';
    const FAILED = 'failed';
    const REFUNDED = 'refunded';
    const PARTIALLY_REFUNDED = 'partially_refunded';

    public static function get_all() {
        return array(
            self::PENDING,
            self::COMPLETED,
            self::FAILED,
            self::REFUNDED,
            self::PARTIALLY_REFUNDED
        );
    }

    public static function get_label($status) {
        $labels = array(
            self::PENDING => __('Oczekująca', 'booking-system-df'),
            self::COMPLETED => __('Zrealizowana', 'booking-system-df'),
            self::FAILED => __('Nieudana', 'booking-system-df'),
            self::REFUNDED => __('Zwrócona', 'booking-system-df'),
            self::PARTIALLY_REFUNDED => __('Częściowo zwrócona', 'booking-system-df')
        );

        return isset($labels[$status]) ? $labels[$status] : $status;
    }

    public static function is_valid($status) {
        return in_array($status, self::get_all());
    }
}
