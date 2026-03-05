<?php
/**
 * Patient data model.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Patient_Data {
    public $name;
    public $email;
    public $phone;
    public $notes;

    public function __construct($name, $email, $phone, $notes = '') {
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->notes = $notes;
    }

    public function validate() {
        $name_result = Booking_Validator::validate_name($this->name);
        if ($name_result->is_failure()) {
            return $name_result;
        }
        $this->name = $name_result->get_data();

        $email_result = Booking_Validator::validate_email($this->email);
        if ($email_result->is_failure()) {
            return $email_result;
        }
        $this->email = $email_result->get_data();

        $phone_result = Booking_Validator::validate_phone($this->phone);
        if ($phone_result->is_failure()) {
            return $phone_result;
        }
        $this->phone = $phone_result->get_data();

        $notes_result = Booking_Validator::validate_notes($this->notes);
        if ($notes_result->is_failure()) {
            return $notes_result;
        }
        $this->notes = $notes_result->get_data();

        return Result::success();
    }
}
