<?php
/**
 * Consultation type model.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Consultation_Type {
    public $id;
    public $name;
    public $description;
    public $duration_minutes;
    public $price;
    public $currency;
    public $is_active;

    public function __construct($id = null, $name = '', $description = '', $duration_minutes = 60, $price = 0, $currency = 'PLN', $is_active = true) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->duration_minutes = $duration_minutes;
        $this->price = $price;
        $this->currency = $currency;
        $this->is_active = $is_active;
    }

    public static function get_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultation_types';
        
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $id
        ));
        
        if (!$row) {
            return null;
        }
        
        return new self(
            $row->id,
            $row->name,
            $row->description,
            $row->duration_minutes,
            $row->price,
            $row->currency,
            (bool)$row->is_active
        );
    }

    public static function get_all_active() {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultation_types';
        
        $rows = $wpdb->get_results(
            "SELECT * FROM $table WHERE is_active = 1 ORDER BY name ASC"
        );
        
        $types = array();
        foreach ($rows as $row) {
            $types[] = new self(
                $row->id,
                $row->name,
                $row->description,
                $row->duration_minutes,
                $row->price,
                $row->currency,
                (bool)$row->is_active
            );
        }
        
        return $types;
    }

    public function save() {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultation_types';
        
        $data = array(
            'name' => $this->name,
            'description' => $this->description,
            'duration_minutes' => $this->duration_minutes,
            'price' => $this->price,
            'currency' => $this->currency,
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
}
