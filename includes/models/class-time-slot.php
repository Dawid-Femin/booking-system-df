<?php
/**
 * Time slot model.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Time_Slot {
    public $start;
    public $end;
    public $is_available;

    public function __construct(DateTime $start, DateTime $end, $is_available = true) {
        $this->start = $start;
        $this->end = $end;
        $this->is_available = $is_available;
    }

    public function get_start_formatted() {
        return $this->start->format('Y-m-d H:i:s');
    }

    public function get_end_formatted() {
        return $this->end->format('Y-m-d H:i:s');
    }

    public function get_start_time() {
        return $this->start->format('H:i');
    }

    public function get_duration_minutes() {
        $diff = $this->start->diff($this->end);
        return ($diff->h * 60) + $diff->i;
    }
}
