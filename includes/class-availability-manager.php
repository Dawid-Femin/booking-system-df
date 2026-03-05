<?php
/**
 * Availability manager - generates available time slots.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Availability_Manager {

    public static function get_available_slots($consultation_type_id, $start_date, $end_date) {
        $type = Consultation_Type::get_by_id($consultation_type_id);
        
        if (!$type) {
            return Result::failure(__('Nieprawidłowy typ konsultacji.', 'booking-system-df'));
        }
        
        $rules = Availability_Rule::get_all_active();
        
        if (empty($rules)) {
            return Result::failure(__('Brak zdefiniowanych reguł dostępności.', 'booking-system-df'));
        }
        
        $timezone = new DateTimeZone('Europe/Warsaw');
        $current = new DateTime($start_date, $timezone);
        $end = new DateTime($end_date, $timezone);
        
        $all_slots = array();
        
        while ($current <= $end) {
            $day_of_week = $current->format('N'); // 1 (Monday) to 7 (Sunday)
            
            foreach ($rules as $rule) {
                if ($rule->day_of_week == $day_of_week) {
                    $slots = self::generate_slots_for_day($current, $rule, $type->duration_minutes);
                    $all_slots = array_merge($all_slots, $slots);
                }
            }
            
            $current->modify('+1 day');
        }
        
        // Filter out blocked periods
        $all_slots = self::filter_blocked_slots($all_slots, $start_date, $end_date);
        
        // Filter out already booked slots
        $all_slots = self::filter_booked_slots($all_slots);
        
        return Result::success($all_slots);
    }

    private static function generate_slots_for_day(DateTime $date, Availability_Rule $rule, $duration_minutes) {
        $slots = array();
        $timezone = new DateTimeZone('Europe/Warsaw');
        
        // Remove seconds from time if present (format: HH:MM:SS -> HH:MM)
        $start_time_str = substr($rule->start_time, 0, 5);
        $end_time_str = substr($rule->end_time, 0, 5);
        
        $start_time = DateTime::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $start_time_str, $timezone);
        $end_time = DateTime::createFromFormat('Y-m-d H:i', $date->format('Y-m-d') . ' ' . $end_time_str, $timezone);
        
        // Check if DateTime creation was successful
        if ($start_time === false || $end_time === false) {
            Booking_System_Logger::log_error('Failed to create DateTime for slot generation', array(
                'date' => $date->format('Y-m-d'),
                'start_time' => $rule->start_time,
                'end_time' => $rule->end_time,
                'start_time_str' => $start_time_str,
                'end_time_str' => $end_time_str
            ));
            return $slots;
        }
        
        $current = clone $start_time;
        
        while ($current < $end_time) {
            $slot_end = clone $current;
            $slot_end->modify("+{$duration_minutes} minutes");
            
            if ($slot_end <= $end_time) {
                // Only include future slots
                $now = new DateTime('now', $timezone);
                if ($current > $now) {
                    $slots[] = new Time_Slot(clone $current, clone $slot_end);
                }
            }
            
            $current->modify("+{$duration_minutes} minutes");
        }
        
        return $slots;
    }

    private static function filter_blocked_slots($slots, $start_date, $end_date) {
        $blocked_periods = Blocked_Period::get_for_range($start_date, $end_date);
        
        if (empty($blocked_periods)) {
            return $slots;
        }
        
        $filtered_slots = array();
        
        foreach ($slots as $slot) {
            $is_blocked = false;
            
            foreach ($blocked_periods as $period) {
                $period_start = new DateTime($period->start_datetime, new DateTimeZone('Europe/Warsaw'));
                $period_end = new DateTime($period->end_datetime, new DateTimeZone('Europe/Warsaw'));
                
                // Check if slot overlaps with blocked period
                if ($slot->start < $period_end && $slot->end > $period_start) {
                    $is_blocked = true;
                    break;
                }
            }
            
            if (!$is_blocked) {
                $filtered_slots[] = $slot;
            }
        }
        
        return $filtered_slots;
    }

    private static function filter_booked_slots($slots) {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultations';
        
        $filtered_slots = array();
        
        foreach ($slots as $slot) {
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table 
                WHERE status IN (%s, %s, %s)
                AND start_datetime < %s 
                AND end_datetime > %s",
                ConsultationStatus::PAYMENT_COMPLETED,
                ConsultationStatus::CONFIRMED,
                ConsultationStatus::COMPLETED,
                $slot->get_end_formatted(),
                $slot->get_start_formatted()
            ));
            
            if ($count == 0) {
                $filtered_slots[] = $slot;
            }
        }
        
        return $filtered_slots;
    }

    public static function is_slot_available($start_datetime, $end_datetime) {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultations';
        
        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table 
            WHERE status IN (%s, %s, %s)
            AND start_datetime < %s 
            AND end_datetime > %s",
            ConsultationStatus::PAYMENT_COMPLETED,
            ConsultationStatus::CONFIRMED,
            ConsultationStatus::COMPLETED,
            $end_datetime,
            $start_datetime
        ));
        
        return $count == 0;
    }
}
