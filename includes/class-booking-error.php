<?php
/**
 * Custom error classes for the booking system.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */

class Booking_Error extends Exception {
    protected $error_code;
    protected $user_message;
    
    public function __construct($message, $error_code = 'BOOKING_ERROR', $user_message = null) {
        parent::__construct($message);
        $this->error_code = $error_code;
        $this->user_message = $user_message ?: $message;
    }
    
    public function get_error_code() {
        return $this->error_code;
    }
    
    public function get_user_message() {
        return $this->user_message;
    }
}

class Booking_Validation_Error extends Booking_Error {
    public function __construct($message, $user_message = null) {
        parent::__construct($message, 'VALIDATION_ERROR', $user_message);
    }
}

class Booking_Payment_Error extends Booking_Error {
    public function __construct($message, $user_message = null) {
        parent::__construct($message, 'PAYMENT_ERROR', $user_message);
    }
}

class Booking_Availability_Error extends Booking_Error {
    public function __construct($message, $user_message = null) {
        parent::__construct($message, 'AVAILABILITY_ERROR', $user_message);
    }
}

class Booking_Database_Error extends Booking_Error {
    public function __construct($message, $user_message = null) {
        parent::__construct($message, 'DATABASE_ERROR', $user_message);
    }
}

class Booking_API_Error extends Booking_Error {
    public function __construct($message, $user_message = null) {
        parent::__construct($message, 'API_ERROR', $user_message);
    }
}
