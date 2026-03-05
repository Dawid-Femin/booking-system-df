<?php
/**
 * Booking engine - core business logic.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Booking_Engine {

    public static function create_consultation($consultation_type_id, $start_datetime, $end_datetime, Patient_Data $patient_data) {
        global $wpdb;
        
        // Validate inputs
        $validation = $patient_data->validate();
        if ($validation->is_failure()) {
            return $validation;
        }
        
        $type_validation = Booking_Validator::validate_consultation_type_id($consultation_type_id);
        if ($type_validation->is_failure()) {
            return $type_validation;
        }
        
        // Start transaction
        $wpdb->query('START TRANSACTION');
        // Note: SERIALIZABLE isolation level removed for compatibility
        
        try {
            // Check slot availability (with lock)
            if (!Availability_Manager::is_slot_available($start_datetime, $end_datetime)) {
                $wpdb->query('ROLLBACK');
                return Result::failure(__('Wybrany termin jest już zajęty.', 'booking-system-df'), 'SLOT_TAKEN');
            }
            
            // Get consultation type
            $type = Consultation_Type::get_by_id($consultation_type_id);
            
            // Create consultation object
            $consultation = new Consultation();
            $consultation->consultation_type_id = $consultation_type_id;
            $consultation->start_datetime = $start_datetime;
            $consultation->end_datetime = $end_datetime;
            $consultation->status = ConsultationStatus::PENDING_PAYMENT;
            $consultation->patient_data = $patient_data;
            $consultation->payment_data = new Payment_Data(
                null,
                PaymentStatus::PENDING,
                $type->price,
                $type->currency
            );
            $consultation->confirmation_token = bin2hex(random_bytes(32));
            
            // Save consultation
            $consultation_id = $consultation->save();
            
            if (!$consultation_id) {
                $wpdb->query('ROLLBACK');
                Booking_System_Logger::log_error('Failed to save consultation', array('critical' => true));
                return Result::failure(__('Nie udało się utworzyć rezerwacji.', 'booking-system-df'));
            }
            
            $consultation->id = $consultation_id;
            
            // Create PayU order
            $payu = new PayU_Gateway();
            $payment_result = $payu->create_order(
                $consultation_id,
                $type->price,
                $type->currency,
                $type->name,
                $patient_data->email,
                $patient_data->name
            );
            
            if ($payment_result->is_failure()) {
                $wpdb->query('ROLLBACK');
                return $payment_result;
            }
            
            $payment_data = $payment_result->get_data();
            
            // Update consultation with payment order ID
            $consultation->payment_data->order_id = $payment_data['order_id'];
            $consultation->save();
            
            $wpdb->query('COMMIT');
            
            Booking_System_Logger::log_info('Consultation created', array(
                'consultation_id' => $consultation_id,
                'order_id' => $payment_data['order_id']
            ));
            
            return Result::success(array(
                'consultation_id' => $consultation_id,
                'redirect_url' => $payment_data['redirect_url']
            ));
            
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            Booking_System_Logger::log_error('Consultation creation failed: ' . $e->getMessage(), array('critical' => true));
            return Result::failure(__('Wystąpił błąd podczas tworzenia rezerwacji.', 'booking-system-df'));
        }
    }

    public static function handle_payment_completed($order_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultations';
        
        $consultation_row = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE payment_order_id = %s",
            $order_id
        ));
        
        if (!$consultation_row) {
            Booking_System_Logger::log_error('Consultation not found for order', array('order_id' => $order_id));
            return Result::failure('Nie znaleziono rezerwacji.');
        }
        
        $consultation = Consultation::get_by_id($consultation_row->id);
        
        if ($consultation->payment_data->status === PaymentStatus::COMPLETED) {
            return Result::success(); // Already processed
        }
        
        // Update payment status
        $consultation->payment_data->status = PaymentStatus::COMPLETED;
        $consultation->payment_data->completed_at = current_time('mysql');
        $consultation->status = ConsultationStatus::PAYMENT_COMPLETED;
        $consultation->save();
        
        // Send confirmation email to patient
        Notification_System::send_payment_confirmation($consultation);
        
        // Notify admin
        Notification_System::send_admin_new_consultation($consultation);
        
        Booking_System_Logger::log_info('Payment completed', array(
            'consultation_id' => $consultation->id,
            'order_id' => $order_id
        ));
        
        return Result::success();
    }

    public static function confirm_consultation($consultation_id) {
        $consultation = Consultation::get_by_id($consultation_id);
        
        if (!$consultation) {
            return Result::failure(__('Nie znaleziono konsultacji.', 'booking-system-df'));
        }
        
        if ($consultation->status !== ConsultationStatus::PAYMENT_COMPLETED) {
            return Result::failure(__('Konsultacja nie może być potwierdzona w tym statusie.', 'booking-system-df'));
        }
        
        // Create Google Meet
        $google_meet = new Google_Meet_Integration();
        $meet_result = $google_meet->create_meeting($consultation);
        
        if ($meet_result->is_failure()) {
            return $meet_result;
        }
        
        $meet_data = $meet_result->get_data();
        
        // Update consultation
        $consultation->google_meet_link = $meet_data['meet_link'];
        $consultation->google_meet_event_id = $meet_data['event_id'];
        $consultation->status = ConsultationStatus::CONFIRMED;
        $consultation->confirmed_at = current_time('mysql');
        $consultation->save();
        
        // Send confirmation email with Meet link
        Notification_System::send_consultation_confirmed($consultation);
        
        Booking_System_Logger::log_info('Consultation confirmed', array(
            'consultation_id' => $consultation_id
        ));
        
        return Result::success();
    }

    public static function cancel_consultation($consultation_id, $reason = '') {
        $consultation = Consultation::get_by_id($consultation_id);
        
        if (!$consultation) {
            return Result::failure(__('Nie znaleziono konsultacji.', 'booking-system-df'));
        }
        
        if ($consultation->status === ConsultationStatus::CANCELLED) {
            return Result::failure(__('Konsultacja jest już anulowana.', 'booking-system-df'));
        }
        
        // Delete Google Meet if exists
        if ($consultation->google_meet_event_id) {
            $google_meet = new Google_Meet_Integration();
            $google_meet->delete_meeting($consultation->google_meet_event_id);
        }
        
        // Process refund if payment was completed
        if ($consultation->payment_data->status === PaymentStatus::COMPLETED) {
            $payu = new PayU_Gateway();
            $refund_result = $payu->refund_payment(
                $consultation->payment_data->order_id,
                $consultation->payment_data->amount,
                'Anulowanie konsultacji: ' . $reason
            );
            
            if ($refund_result->is_success()) {
                $consultation->refund_amount = $consultation->payment_data->amount;
                $consultation->refund_status = 'pending';
                $consultation->payment_data->status = PaymentStatus::REFUNDED;
                
                // Log refund
                Refund_Logger::log_refund(
                    $consultation->id,
                    $consultation->payment_data->order_id,
                    $consultation->payment_data->amount,
                    $reason
                );
            }
        }
        
        // Update consultation
        $consultation->status = ConsultationStatus::CANCELLED;
        $consultation->cancelled_at = current_time('mysql');
        $consultation->cancellation_reason = $reason;
        $consultation->save();
        
        // Send cancellation notice
        Notification_System::send_cancellation_notice($consultation);
        
        Booking_System_Logger::log_info('Consultation cancelled', array(
            'consultation_id' => $consultation_id,
            'reason' => $reason
        ));
        
        return Result::success();
    }

    public static function mark_as_completed($consultation_id) {
        $consultation = Consultation::get_by_id($consultation_id);
        
        if (!$consultation) {
            return Result::failure(__('Nie znaleziono konsultacji.', 'booking-system-df'));
        }
        
        $consultation->status = ConsultationStatus::COMPLETED;
        $consultation->save();
        
        Booking_System_Logger::log_info('Consultation marked as completed', array(
            'consultation_id' => $consultation_id
        ));
        
        return Result::success();
    }
}
