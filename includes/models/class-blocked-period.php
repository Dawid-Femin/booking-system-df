<?php
/**
 * Blocked period model.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Blocked_Period {
    public $id;
    public $start_datetime;
    public $end_datetime;
    public $reason;

    public function __construct($id = null, $start_datetime = null, $end_datetime = null, $reason = '') {
        $this->id = $id;
        $this->start_datetime = $start_datetime;
        $this->end_datetime = $end_datetime;
        $this->reason = $reason;
    }

    public static function get_all() {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_blocked_periods';
        
        $rows = $wpdb->get_results(
            "SELECT * FROM $table ORDER BY start_datetime ASC"
        );
        
        $periods = array();
        foreach ($rows as $row) {
            $periods[] = new self(
                $row->id,
                $row->start_datetime,
                $row->end_datetime,
                $row->reason
            );
        }
        
        return $periods;
    }

    public static function get_for_range($start, $end) {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_blocked_periods';
        
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
            WHERE (start_datetime <= %s AND end_datetime >= %s)
            OR (start_datetime >= %s AND start_datetime < %s)
            ORDER BY start_datetime ASC",
            $end, $start, $start, $end
        ));
        
        $periods = array();
        foreach ($rows as $row) {
            $periods[] = new self(
                $row->id,
                $row->start_datetime,
                $row->end_datetime,
                $row->reason
            );
        }
        
        return $periods;
    }

    public function save() {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_blocked_periods';
        
        $data = array(
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'reason' => $this->reason
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
        $table = $wpdb->prefix . 'booking_blocked_periods';
        return $wpdb->delete($table, array('id' => $id));
    }
}
