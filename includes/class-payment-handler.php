<?php
/**
 * Payment handler for PayU webhooks and returns.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Payment_Handler {

    public static function init() {
        add_action('rest_api_init', array(__CLASS__, 'register_routes'));
    }

    public static function register_routes() {
        register_rest_route('booking-system-df/v1', '/payu-webhook', array(
            'methods' => 'POST',
            'callback' => array(__CLASS__, 'handle_payu_webhook'),
            'permission_callback' => '__return_true'
        ));
        
        register_rest_route('booking-system-df/v1', '/payment-return', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'handle_payment_return'),
            'permission_callback' => '__return_true'
        ));
    }

    public static function handle_payu_webhook($request) {
        $body = $request->get_body();
        $data = json_decode($body, true);
        
        Booking_System_Logger::log_info('PayU webhook received', array('data' => $data));
        
        // Verify signature if MD5 key is configured
        $signature = $request->get_header('OpenPayu-Signature');
        if ($signature) {
            $is_valid = self::verify_payu_signature($body, $signature);
            
            if (!$is_valid) {
                Booking_System_Logger::log_error('PayU webhook signature verification failed', array(
                    'signature' => $signature,
                    'body_length' => strlen($body)
                ));
                return new WP_REST_Response(array('error' => 'Invalid signature'), 401);
            }
            
            Booking_System_Logger::log_info('PayU webhook signature verified');
        }
        
        if (!isset($data['order'])) {
            Booking_System_Logger::log_error('Invalid PayU webhook data');
            return new WP_REST_Response(array('error' => 'Invalid data'), 400);
        }
        
        $order = $data['order'];
        $order_id = $order['orderId'];
        $status = $order['status'];
        
        if ($status === 'COMPLETED') {
            $result = Booking_Engine::handle_payment_completed($order_id);
            
            if ($result->is_failure()) {
                Booking_System_Logger::log_error('Failed to process payment completion', array(
                    'order_id' => $order_id,
                    'error' => $result->get_error()
                ));
            }
        }
        
        return new WP_REST_Response(array('status' => 'ok'), 200);
    }
    
    private static function verify_payu_signature($body, $signature_header) {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_settings';
        
        $md5_key_encrypted = $wpdb->get_var(
            $wpdb->prepare("SELECT setting_value FROM $table WHERE setting_key = %s", 'payu_md5_key')
        );
        
        if (!$md5_key_encrypted) {
            // MD5 key not configured - skip verification
            Booking_System_Logger::log_info('PayU MD5 key not configured - skipping signature verification');
            return true;
        }
        
        $md5_key = Encryption_Helper::decrypt($md5_key_encrypted);
        
        // Parse signature header: algorithm=MD5;signature=xxxxx
        $parts = explode(';', $signature_header);
        $signature = null;
        
        foreach ($parts as $part) {
            if (strpos($part, 'signature=') === 0) {
                $signature = substr($part, strlen('signature='));
                break;
            }
        }
        
        if (!$signature) {
            return false;
        }
        
        // Calculate expected signature
        $expected_signature = md5($body . $md5_key);
        
        return hash_equals($expected_signature, $signature);
    }

    public static function handle_payment_return($request) {
        $consultation_id = $request->get_param('consultation_id');
        $dev_mode = $request->get_param('dev_mode');
        
        if (!$consultation_id) {
            wp_redirect(home_url());
            exit;
        }
        
        $consultation = Consultation::get_by_id($consultation_id);
        
        if (!$consultation) {
            wp_redirect(home_url());
            exit;
        }
        
        // Development mode - auto-complete payment
        if ($dev_mode === '1') {
            Booking_Engine::handle_payment_completed($consultation->payment_data->order_id);
            
            $redirect_url = add_query_arg(array(
                'booking_status' => 'success',
                'consultation_id' => $consultation_id,
                'dev_mode' => '1'
            ), home_url('/potwierdzenie-platnosci/'));
            
            wp_redirect($redirect_url);
            exit;
        }
        
        // Verify payment status with PayU
        $payu = new PayU_Gateway();
        $verify_result = $payu->verify_payment($consultation->payment_data->order_id);
        
        if ($verify_result->is_success()) {
            $payment_data = $verify_result->get_data();
            
            if ($payment_data['status'] === 'COMPLETED') {
                Booking_Engine::handle_payment_completed($consultation->payment_data->order_id);
                
                $redirect_url = add_query_arg(array(
                    'booking_status' => 'success',
                    'consultation_id' => $consultation_id
                ), home_url('/potwierdzenie-platnosci/'));
                
                wp_redirect($redirect_url);
                exit;
            }
        }
        
        $redirect_url = add_query_arg(array(
            'booking_status' => 'pending',
            'consultation_id' => $consultation_id
        ), home_url('/potwierdzenie-platnosci/'));
        
        wp_redirect($redirect_url);
        exit;
    }
}

Payment_Handler::init();
