<?php
/**
 * Public-facing functionality.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Booking_System_Public {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, BOOKING_SYSTEM_DF_PLUGIN_URL . 'public/css/public.css', array(), $this->version);
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, BOOKING_SYSTEM_DF_PLUGIN_URL . 'public/js/public.js', array('jquery'), $this->version);
        
        wp_localize_script($this->plugin_name, 'bookingSystemPublic', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('booking_system_public'),
            'strings' => array(
                'loading' => __('Ładowanie...', 'booking-system-df'),
                'select_slot' => __('Wybierz termin', 'booking-system-df'),
                'no_slots' => __('Brak dostępnych terminów', 'booking-system-df'),
                'error' => __('Wystąpił błąd', 'booking-system-df'),
                'required_field' => __('To pole jest wymagane', 'booking-system-df'),
                'invalid_email' => __('Nieprawidłowy adres email', 'booking-system-df'),
                'invalid_phone' => __('Nieprawidłowy numer telefonu', 'booking-system-df')
            )
        ));
    }

    /**
     * Shortcode: [booking_calendar type_id="1"]
     */
    public function booking_calendar_shortcode($atts) {
        $atts = shortcode_atts(array(
            'type_id' => 0
        ), $atts);
        
        $type_id = intval($atts['type_id']);
        
        if ($type_id <= 0) {
            return '<p>' . __('Nieprawidłowy typ konsultacji.', 'booking-system-df') . '</p>';
        }
        
        $type = Consultation_Type::get_by_id($type_id);
        
        if (!$type) {
            return '<p>' . __('Nie znaleziono typu konsultacji.', 'booking-system-df') . '</p>';
        }
        
        ob_start();
        include BOOKING_SYSTEM_DF_PLUGIN_DIR . 'public/views/calendar.php';
        return ob_get_clean();
    }

    /**
     * Shortcode: [booking_form type_id="1"]
     */
    public function booking_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'type_id' => 0
        ), $atts);
        
        $type_id = intval($atts['type_id']);
        
        if ($type_id <= 0) {
            return '<p>' . __('Nieprawidłowy typ konsultacji.', 'booking-system-df') . '</p>';
        }
        
        $type = Consultation_Type::get_by_id($type_id);
        
        if (!$type) {
            return '<p>' . __('Nie znaleziono typu konsultacji.', 'booking-system-df') . '</p>';
        }
        
        // Handle form submission
        if (isset($_POST['submit_booking'])) {
            $result = $this->process_booking_form();
            
            if ($result->is_success()) {
                $data = $result->get_data();
                // Use JavaScript redirect instead of wp_redirect to avoid headers already sent error
                echo '<script>window.location.href = "' . esc_js($data['redirect_url']) . '";</script>';
                echo '<p>' . __('Przekierowywanie do płatności...', 'booking-system-df') . '</p>';
                return;
            } else {
                echo '<div class="booking-error">' . esc_html($result->get_error()) . '</div>';
            }
        }
        
        ob_start();
        include BOOKING_SYSTEM_DF_PLUGIN_DIR . 'public/views/booking-form.php';
        return ob_get_clean();
    }

    /**
     * Shortcode: [my_consultations]
     */
    public function my_consultations_shortcode($atts) {
        if (!isset($_GET['email']) || !isset($_GET['token'])) {
            return '<p>' . __('Nieprawidłowy link dostępu.', 'booking-system-df') . '</p>';
        }
        
        $email = sanitize_email($_GET['email']);
        $token = sanitize_text_field($_GET['token']);
        
        global $wpdb;
        $table = $wpdb->prefix . 'booking_consultations';
        
        $consultations = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table WHERE confirmation_token = %s ORDER BY start_datetime DESC",
            $token
        ));
        
        if (empty($consultations)) {
            return '<p>' . __('Nie znaleziono konsultacji.', 'booking-system-df') . '</p>';
        }
        
        ob_start();
        include BOOKING_SYSTEM_DF_PLUGIN_DIR . 'public/views/my-consultations.php';
        return ob_get_clean();
    }

    private function process_booking_form() {
        if (!isset($_POST['booking_nonce']) || !wp_verify_nonce($_POST['booking_nonce'], 'booking_form')) {
            return Result::failure(__('Nieprawidłowe żądanie.', 'booking-system-df'));
        }
        
        $type_id = intval($_POST['type_id']);
        $start_datetime = sanitize_text_field($_POST['start_datetime']);
        $end_datetime = sanitize_text_field($_POST['end_datetime']);
        
        $patient_data = new Patient_Data(
            sanitize_text_field($_POST['patient_name']),
            sanitize_email($_POST['patient_email']),
            sanitize_text_field($_POST['patient_phone']),
            sanitize_textarea_field($_POST['patient_notes'])
        );
        
        return Booking_Engine::create_consultation($type_id, $start_datetime, $end_datetime, $patient_data);
    }
}
