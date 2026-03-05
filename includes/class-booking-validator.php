<?php
/**
 * Validation helper class.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Booking_Validator {

    public static function validate_email($email) {
        $email = sanitize_email($email);
        
        if (empty($email)) {
            return Result::failure(__('Adres email jest wymagany.', 'booking-system-df'), 'REQUIRED');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Result::failure(__('Nieprawidłowy format adresu email.', 'booking-system-df'), 'INVALID_FORMAT');
        }
        
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            return Result::failure(__('Nieprawidłowy format adresu email.', 'booking-system-df'), 'INVALID_FORMAT');
        }
        
        return Result::success($email);
    }

    public static function validate_phone($phone) {
        $phone = sanitize_text_field($phone);
        
        if (empty($phone)) {
            return Result::failure(__('Numer telefonu jest wymagany.', 'booking-system-df'), 'REQUIRED');
        }
        
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        if (strlen($phone) < 9 || strlen($phone) > 15) {
            return Result::failure(__('Numer telefonu musi mieć od 9 do 15 cyfr.', 'booking-system-df'), 'INVALID_LENGTH');
        }
        
        return Result::success($phone);
    }

    public static function validate_name($name) {
        $name = sanitize_text_field($name);
        
        if (empty($name)) {
            return Result::failure(__('Imię i nazwisko jest wymagane.', 'booking-system-df'), 'REQUIRED');
        }
        
        if (strlen($name) < 2) {
            return Result::failure(__('Imię i nazwisko musi mieć co najmniej 2 znaki.', 'booking-system-df'), 'TOO_SHORT');
        }
        
        if (strlen($name) > 100) {
            return Result::failure(__('Imię i nazwisko nie może przekraczać 100 znaków.', 'booking-system-df'), 'TOO_LONG');
        }
        
        return Result::success($name);
    }

    public static function validate_datetime($datetime_string) {
        if (empty($datetime_string)) {
            return Result::failure(__('Data i godzina są wymagane.', 'booking-system-df'), 'REQUIRED');
        }
        
        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $datetime_string, new DateTimeZone('Europe/Warsaw'));
        
        if (!$datetime) {
            return Result::failure(__('Nieprawidłowy format daty i godziny.', 'booking-system-df'), 'INVALID_FORMAT');
        }
        
        return Result::success($datetime);
    }

    public static function validate_consultation_type_id($type_id) {
        $type_id = absint($type_id);
        
        if ($type_id <= 0) {
            return Result::failure(__('Nieprawidłowy typ konsultacji.', 'booking-system-df'), 'INVALID');
        }
        
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultation_types';
        
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE id = %d AND is_active = 1",
            $type_id
        ));
        
        if (!$exists) {
            return Result::failure(__('Wybrany typ konsultacji nie istnieje lub jest nieaktywny.', 'booking-system-df'), 'NOT_FOUND');
        }
        
        return Result::success($type_id);
    }

    public static function validate_notes($notes) {
        if (empty($notes)) {
            return Result::success('');
        }
        
        $notes = sanitize_textarea_field($notes);
        
        if (strlen($notes) > 1000) {
            return Result::failure(__('Notatki nie mogą przekraczać 1000 znaków.', 'booking-system-df'), 'TOO_LONG');
        }
        
        return Result::success($notes);
    }

    public static function validate_price($price) {
        if (!is_numeric($price)) {
            return Result::failure(__('Cena musi być liczbą.', 'booking-system-df'), 'INVALID_TYPE');
        }
        
        $price = floatval($price);
        
        if ($price < 0) {
            return Result::failure(__('Cena nie może być ujemna.', 'booking-system-df'), 'NEGATIVE');
        }
        
        if ($price > 999999.99) {
            return Result::failure(__('Cena jest zbyt wysoka.', 'booking-system-df'), 'TOO_HIGH');
        }
        
        return Result::success($price);
    }

    public static function validate_duration($duration_minutes) {
        $duration_minutes = absint($duration_minutes);
        
        if ($duration_minutes <= 0) {
            return Result::failure(__('Czas trwania musi być większy od zera.', 'booking-system-df'), 'INVALID');
        }
        
        if ($duration_minutes > 480) {
            return Result::failure(__('Czas trwania nie może przekraczać 8 godzin.', 'booking-system-df'), 'TOO_LONG');
        }
        
        return Result::success($duration_minutes);
    }
}
