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
