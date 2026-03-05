<?php
/**
 * Consultation model.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Consultation {
    public $id;
    public $consultation_type_id;
    public $start_datetime;
    public $end_datetime;
    public $status;
    public $patient_data;
    public $payment_data;
    public $google_meet_link;
    public $google_meet_event_id;
    public $confirmation_token;
    public $confirmed_at;
    public $cancelled_at;
    public $cancellation_reason;
    public $refund_amount;
    public $refund_status;
    public $refund_completed_at;
    public $created_at;
    public $updated_at;

    public function __construct() {
        $this->status = ConsultationStatus::PENDING_PAYMENT;
        $this->patient_data = new Patient_Data('', '', '');
        $this->payment_data = new Payment_Data();
    }

    public static function get_by_id($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultations';
        
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $id
        ));
        
        if (!$row) {
            return null;
        }
        
        return self::from_db_row($row);
    }

    public static function get_by_confirmation_token($token) {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultations';
        
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE confirmation_token = %s",
            $token
        ));
        
        if (!$row) {
            return null;
        }
        
        return self::from_db_row($row);
    }

    private static function from_db_row($row) {
        $consultation = new self();
        $consultation->id = $row->id;
        $consultation->consultation_type_id = $row->consultation_type_id;
        $consultation->start_datetime = $row->start_datetime;
        $consultation->end_datetime = $row->end_datetime;
        $consultation->status = $row->status;
        
        // Decrypt patient data
        $consultation->patient_data = new Patient_Data(
            Encryption_Helper::decrypt($row->patient_name_encrypted),
            Encryption_Helper::decrypt($row->patient_email_encrypted),
            Encryption_Helper::decrypt($row->patient_phone_encrypted),
            Encryption_Helper::decrypt($row->patient_notes_encrypted)
        );
        
        $consultation->payment_data = new Payment_Data(
            $row->payment_order_id,
            $row->payment_status,
            $row->payment_amount,
            $row->payment_currency,
            $row->payment_completed_at
        );
        
        $consultation->google_meet_link = $row->google_meet_link;
        $consultation->google_meet_event_id = $row->google_meet_event_id;
        $consultation->confirmation_token = $row->confirmation_token;
        $consultation->confirmed_at = $row->confirmed_at;
        $consultation->cancelled_at = $row->cancelled_at;
        $consultation->cancellation_reason = $row->cancellation_reason;
        $consultation->refund_amount = $row->refund_amount;
        $consultation->refund_status = $row->refund_status;
        $consultation->refund_completed_at = $row->refund_completed_at;
        $consultation->created_at = $row->created_at;
        $consultation->updated_at = $row->updated_at;
        
        return $consultation;
    }

    public function save() {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultations';
        
        $data = array(
            'consultation_type_id' => $this->consultation_type_id,
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'status' => $this->status,
            'patient_name_encrypted' => Encryption_Helper::encrypt($this->patient_data->name),
            'patient_email_encrypted' => Encryption_Helper::encrypt($this->patient_data->email),
            'patient_phone_encrypted' => Encryption_Helper::encrypt($this->patient_data->phone),
            'patient_notes_encrypted' => Encryption_Helper::encrypt($this->patient_data->notes),
            'payment_order_id' => $this->payment_data->order_id,
            'payment_status' => $this->payment_data->status,
            'payment_amount' => $this->payment_data->amount,
            'payment_currency' => $this->payment_data->currency,
            'payment_completed_at' => $this->payment_data->completed_at,
            'google_meet_link' => $this->google_meet_link,
            'google_meet_event_id' => $this->google_meet_event_id,
            'confirmation_token' => $this->confirmation_token,
            'confirmed_at' => $this->confirmed_at,
            'cancelled_at' => $this->cancelled_at,
            'cancellation_reason' => $this->cancellation_reason,
            'refund_amount' => $this->refund_amount,
            'refund_status' => $this->refund_status,
            'refund_completed_at' => $this->refund_completed_at
        );
        
        if ($this->id) {
            $wpdb->update($table, $data, array('id' => $this->id));
        } else {
            $wpdb->insert($table, $data);
            $this->id = $wpdb->insert_id;
        }
        
        return $this->id;
    }

    public static function get_upcoming($limit = 10) {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultations';
        
        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table 
            WHERE start_datetime > NOW() 
            AND status IN (%s, %s)
            ORDER BY start_datetime ASC 
            LIMIT %d",
            ConsultationStatus::CONFIRMED,
            ConsultationStatus::PAYMENT_COMPLETED,
            $limit
        ));
        
        $consultations = array();
        foreach ($rows as $row) {
            $consultations[] = self::from_db_row($row);
        }
        
        return $consultations;
    }
}
