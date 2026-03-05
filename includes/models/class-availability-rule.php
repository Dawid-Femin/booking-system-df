<?php
/**
 * Availability rule model.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Availability_Rule {
    public $id;
    public $day_of_week;
    public $start_time;
    public $end_time;
    public $is_active;

    public function __construct($id = null, $day_of_week = 1, $start_time = '09:00', $end_time = '17:00', $is_active = true) {
        $this->id = $id;
        $this->day_of_week = $day_of_week;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->is_active = $is_active;
    }

    public static function get_all_active() {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_availability_rules';
        
        $rows = $wpdb->get_results(
            "SELECT * FROM $table WHERE is_active = 1 ORDER BY day_of_week ASC, start_time ASC"
        );
        
        $rules = array();
        foreach ($rows as $row) {
            $rules[] = new self(
                $row->id,
                $row->day_of_week,
                $row->start_time,
                $row->end_time,
                (bool)$row->is_active
            );
        }
        
        return $rules;
    }

    public function save() {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_availability_rules';
        
        $data = array(
            'day_of_week' => $this->day_of_week,
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
        $table = $wpdb->prefix . 'booking_availability_rules';
        return $wpdb->delete($table, array('id' => $id));
    }
}
