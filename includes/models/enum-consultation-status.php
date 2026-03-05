<?php
/**
 * Consultation status enum.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class ConsultationStatus {
    const PENDING_PAYMENT = 'pending_payment';
    const PAYMENT_COMPLETED = 'payment_completed';
    const CONFIRMED = 'confirmed';
    const COMPLETED = 'completed';
    const CANCELLED = 'cancelled';
    const REFUNDED = 'refunded';

    public static function get_all() {
        return array(
            self::PENDING_PAYMENT,
            self::PAYMENT_COMPLETED,
            self::CONFIRMED,
            self::COMPLETED,
            self::CANCELLED,
            self::REFUNDED
        );
    }

    public static function get_label($status) {
        $labels = array(
            self::PENDING_PAYMENT => __('Oczekuje na płatność', 'booking-system-df'),
            self::PAYMENT_COMPLETED => __('Płatność zrealizowana', 'booking-system-df'),
            self::CONFIRMED => __('Potwierdzona', 'booking-system-df'),
            self::COMPLETED => __('Zakończona', 'booking-system-df'),
            self::CANCELLED => __('Anulowana', 'booking-system-df'),
            self::REFUNDED => __('Zwrócona', 'booking-system-df')
        );

        return isset($labels[$status]) ? $labels[$status] : $status;
    }

    public static function is_valid($status) {
        return in_array($status, self::get_all());
    }
}
