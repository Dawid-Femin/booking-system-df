<?php
/**
 * Availability slot model - for specific dates.
 *
 * @since      1.0.3
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Availability_Slot {
    public $id;
    public $date;
    public $start_time;
    public $end_time;
    public $is_active;

    public function __construct($id = null, $date = '', $start_time = '09:00', $end_time = '17:00', $is_active = true) {
        $this->id = $id;
        $this->date = $date;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->is_active = $is_active;
    }

    public static function get_by_date_range($start_date, $end_date) {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_availability_slots';
        
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE date >= %s AND date <= %s AND is_active = 1 ORDER BY date ASC, start_time ASC",
            $start_date,
            $end_date
        ));
        
        $slots = array();
        foreach ($rows as $row) {
            $slots[] = new self(
                $row->id,
                $row->date,
                $row->start_time,
                $row->end_time,
                (bool)$row->is_active
            );
        }
        
        return $slots;
    }

    public static function get_by_date($date) {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_availability_slots';
        
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE date = %s AND is_active = 1 ORDER BY start_time ASC",
            $date
        ));
        
        $slots = array();
        foreach ($rows as $row) {
            $slots[] = new self(
                $row->id,
                $row->date,
                $row->start_time,
                $row->end_time,
                (bool)$row->is_active
            );
        }
        
        return $slots;
    }

    public function save() {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_availability_slots';
        
        $data = array(
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'is_active' => $this->is_active ? 1 : 0
        );
        
        if ($this->id) {
            $wpdb->update($table, $data, array('id' => $this->id));
        } else {
            $wpdb->insert($table, $data);
            $this->id = $wpdb->insert_id;
        }
        
        return $this->id;
    }

    public static function delete($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_availability_slots';
        return $wpdb->delete($table, array('id' => $id));
    }

    public static function delete_by_date($date) {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_availability_slots';
        return $wpdb->delete($table, array('date' => $date));
    }
}
