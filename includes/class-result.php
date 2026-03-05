<?php
/**
 * Result wrapper class for operations.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Result {
    private $success;
    private $data;
    private $error;
    private $error_code;
    
    private function __construct($success, $data = null, $error = null, $error_code = null) {
        $this->success = $success;
        $this->data = $data;
        $this->error = $error;
        $this->error_code = $error_code;
    }
    
    public static function success($data = null) {
        return new self(true, $data);
    }
    
    public static function failure($error, $error_code = 'ERROR') {
        return new self(false, null, $error, $error_code);
    }
    
    public function is_success() {
        return $this->success;
    }
    
    public function is_failure() {
        return !$this->success;
    }
    
    public function get_data() {
        return $this->data;
    }
    
    public function get_error() {
        return $this->error;
    }
    
    public function get_error_code() {
        return $this->error_code;
    }
}
