<?php
/**
 * Payment data model.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Payment_Data {
    public $order_id;
    public $status;
    public $amount;
    public $currency;
    public $completed_at;

    public function __construct($order_id = null, $status = PaymentStatus::PENDING, $amount = 0, $currency = 'PLN', $completed_at = null) {
        $this->order_id = $order_id;
        $this->status = $status;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->completed_at = $completed_at;
    }
}
