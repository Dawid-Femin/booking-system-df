<?php
/**
 * PayU payment gateway integration.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class PayU_Gateway {

    private $client_id;
    private $client_secret;
    private $pos_id;
    private $is_sandbox;
    private $force_on_localhost;
    private $access_token;
    private $token_expires_at;

    public function __construct() {
        $this->load_settings();
    }

    private function load_settings() {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_settings';
        
        $settings = $wpdb->get_results(
            "SELECT setting_key, setting_value FROM $table WHERE setting_key LIKE 'payu_%'"
        );
        
        foreach ($settings as $setting) {
            $value = $setting->setting_value;
            
            switch ($setting->setting_key) {
                case 'payu_client_id':
                    $this->client_id = Encryption_Helper::decrypt($value);
                    break;
                case 'payu_client_secret':
                    $this->client_secret = Encryption_Helper::decrypt($value);
                    break;
                case 'payu_pos_id':
                    $this->pos_id = Encryption_Helper::decrypt($value);
                    break;
                case 'payu_sandbox':
                    $this->is_sandbox = ($value === '1');
                    break;
                case 'payu_force_on_localhost':
                    $this->force_on_localhost = ($value === '1');
                    break;
            }
        }
    }

    private function get_api_url() {
        return $this->is_sandbox 
            ? 'https://secure.snd.payu.com' 
            : 'https://secure.payu.com';
    }

    private function get_access_token() {
        if ($this->access_token && $this->token_expires_at > time()) {
            return $this->access_token;
        }

        $url = $this->get_api_url() . '/pl/standard/user/oauth/authorize';
        
        $response = wp_remote_post($url, array(
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded'
            ),
            'body' => array(
                'grant_type' => 'client_credentials',
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret
            ),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            Booking_System_Logger::log_error('PayU OAuth failed: ' . $response->get_error_message());
            throw new Booking_API_Error('Nie udało się połączyć z PayU.');
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!isset($body['access_token'])) {
            Booking_System_Logger::log_error('PayU OAuth response invalid', array(
                'response' => $body,
                'response_code' => wp_remote_retrieve_response_code($response),
                'raw_body' => wp_remote_retrieve_body($response)
            ));
            throw new Booking_API_Error('Nie udało się uzyskać tokenu dostępu PayU.');
        }

        $this->access_token = $body['access_token'];
        $this->token_expires_at = time() + $body['expires_in'] - 60;
        
        Booking_System_Logger::log_info('PayU OAuth token obtained', array(
            'token_length' => strlen($this->access_token),
            'expires_in' => $body['expires_in']
        ));
        
        return $this->access_token;
    }

    public function create_order($consultation_id, $amount, $currency, $description, $customer_email, $customer_name) {
        // Development mode - skip PayU on localhost (unless forced)
        $is_localhost = (strpos(home_url(), 'localhost') !== false || strpos(home_url(), '.local') !== false || strpos(home_url(), '127.0.0.1') !== false);
        
        if ($is_localhost && !$this->force_on_localhost) {
            Booking_System_Logger::log_info('PayU skipped - development mode (localhost detected, not forced)', array(
                'consultation_id' => $consultation_id,
                'amount' => $amount,
                'home_url' => home_url()
            ));
            
            return Result::success(array(
                'order_id' => 'DEV_ORDER_' . $consultation_id . '_' . time(),
                'redirect_url' => home_url('/wp-json/booking-system-df/v1/payment-return?consultation_id=' . $consultation_id . '&dev_mode=1')
            ));
        }
        
        if ($is_localhost && $this->force_on_localhost) {
            Booking_System_Logger::log_info('PayU forced on localhost - may return 403 error', array(
                'consultation_id' => $consultation_id,
                'amount' => $amount
            ));
        }
        
        try {
            $token = $this->get_access_token();
            
            $url = $this->get_api_url() . '/api/v2_1/orders';
            
            $order_data = array(
                'notifyUrl' => home_url('/wp-json/booking-system-df/v1/payu-webhook'),
                'customerIp' => $this->get_client_ip(),
                'merchantPosId' => $this->pos_id,
                'description' => $description,
                'currencyCode' => $currency,
                'totalAmount' => intval($amount * 100), // Convert to grosze
                'extOrderId' => 'consultation_' . $consultation_id . '_' . time(),
                'buyer' => array(
                    'email' => $customer_email,
                    'firstName' => $customer_name,
                    'language' => 'pl'
                ),
                'products' => array(
                    array(
                        'name' => $description,
                        'unitPrice' => intval($amount * 100),
                        'quantity' => 1
                    )
                ),
                'continueUrl' => home_url('/wp-json/booking-system-df/v1/payment-return?consultation_id=' . $consultation_id)
            );

            $response = wp_remote_post($url, array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ),
                'body' => json_encode($order_data),
                'timeout' => 30
            ));

            if (is_wp_error($response)) {
                Booking_System_Logger::log_error('PayU create order failed: ' . $response->get_error_message());
                throw new Booking_Payment_Error('Nie udało się utworzyć zamówienia w PayU.');
            }

            $response_code = wp_remote_retrieve_response_code($response);
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $raw_body = wp_remote_retrieve_body($response);
            
            Booking_System_Logger::log_info('PayU create order response', array(
                'code' => $response_code,
                'body' => $body,
                'raw_body' => $raw_body,
                'headers' => wp_remote_retrieve_headers($response),
                'order_data' => $order_data,
                'api_url' => $url
            ));
            
            if (!isset($body['orderId']) || !isset($body['redirectUri'])) {
                Booking_System_Logger::log_error('PayU create order response invalid', array('response' => $body));
                throw new Booking_Payment_Error('Nieprawidłowa odpowiedź z PayU.');
            }

            Booking_System_Logger::log_info('PayU order created', array(
                'order_id' => $body['orderId'],
                'consultation_id' => $consultation_id
            ));

            return Result::success(array(
                'order_id' => $body['orderId'],
                'redirect_url' => $body['redirectUri']
            ));

        } catch (Exception $e) {
            Booking_System_Logger::log_error('PayU create order exception: ' . $e->getMessage(), array('critical' => true));
            return Result::failure($e->getMessage());
        }
    }

    public function verify_payment($order_id) {
        try {
            $token = $this->get_access_token();
            
            $url = $this->get_api_url() . '/api/v2_1/orders/' . $order_id;
            
            $response = wp_remote_get($url, array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $token
                ),
                'timeout' => 30
            ));

            if (is_wp_error($response)) {
                Booking_System_Logger::log_error('PayU verify payment failed: ' . $response->get_error_message());
                return Result::failure('Nie udało się zweryfikować płatności.');
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);
            
            if (!isset($body['orders'][0])) {
                return Result::failure('Nie znaleziono zamówienia.');
            }

            $order = $body['orders'][0];
            
            return Result::success(array(
                'status' => $order['status'],
                'amount' => $order['totalAmount'] / 100,
                'currency' => $order['currencyCode']
            ));

        } catch (Exception $e) {
            Booking_System_Logger::log_error('PayU verify payment exception: ' . $e->getMessage());
            return Result::failure($e->getMessage());
        }
    }

    public function refund_payment($order_id, $amount, $description) {
        try {
            $token = $this->get_access_token();
            
            $url = $this->get_api_url() . '/api/v2_1/orders/' . $order_id . '/refunds';
            
            $refund_data = array(
                'description' => $description,
                'amount' => intval($amount * 100)
            );

            $response = wp_remote_post($url, array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token
                ),
                'body' => json_encode($refund_data),
                'timeout' => 30
            ));

            if (is_wp_error($response)) {
                Booking_System_Logger::log_error('PayU refund failed: ' . $response->get_error_message());
                throw new Booking_Payment_Error('Nie udało się zainicjować zwrotu płatności.');
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);
            
            if (!isset($body['refund'])) {
                Booking_System_Logger::log_error('PayU refund response invalid', array('response' => $body));
                throw new Booking_Payment_Error('Nieprawidłowa odpowiedź z PayU podczas zwrotu.');
            }

            Booking_System_Logger::log_info('PayU refund initiated', array(
                'order_id' => $order_id,
                'refund_id' => $body['refund']['refundId'],
                'amount' => $amount
            ));

            return Result::success(array(
                'refund_id' => $body['refund']['refundId'],
                'status' => $body['refund']['status']
            ));

        } catch (Exception $e) {
            Booking_System_Logger::log_error('PayU refund exception: ' . $e->getMessage(), array('critical' => true));
            return Result::failure($e->getMessage());
        }
    }

    private function get_client_ip() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1';
    }
}
