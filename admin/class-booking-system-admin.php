<?php
/**
 * Admin area functionality.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Booking_System_Admin {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, BOOKING_SYSTEM_DF_PLUGIN_URL . 'admin/css/admin.css', array(), $this->version);
    }

    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, BOOKING_SYSTEM_DF_PLUGIN_URL . 'admin/js/admin.js', array('jquery'), $this->version);
        
        wp_localize_script($this->plugin_name, 'bookingSystemAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('booking_system_admin'),
            'strings' => array(
                'confirm_delete' => __('Czy na pewno chcesz usunąć?', 'booking-system-df'),
                'confirm_cancel' => __('Czy na pewno chcesz anulować tę konsultację?', 'booking-system-df'),
                'confirm_confirm' => __('Czy na pewno chcesz potwierdzić tę konsultację? Zostanie utworzone spotkanie Google Meet.', 'booking-system-df'),
                'error' => __('Wystąpił błąd', 'booking-system-df'),
                'success' => __('Operacja zakończona pomyślnie', 'booking-system-df')
            )
        ));
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Rezerwacje', 'booking-system-df'),
            __('Rezerwacje', 'booking-system-df'),
            'manage_options',
            'booking-dashboard',
            array($this, 'display_dashboard_page'),
            'dashicons-calendar-alt',
            30
        );
        
        add_submenu_page(
            'booking-dashboard',
            __('Panel główny', 'booking-system-df'),
            __('Panel główny', 'booking-system-df'),
            'manage_options',
            'booking-dashboard',
            array($this, 'display_dashboard_page')
        );
        
        add_submenu_page(
            'booking-dashboard',
            __('Konsultacje', 'booking-system-df'),
            __('Konsultacje', 'booking-system-df'),
            'manage_options',
            'booking-consultations',
            array($this, 'display_consultations_page')
        );
        
        add_submenu_page(
            'booking-dashboard',
            __('Typy konsultacji', 'booking-system-df'),
            __('Typy konsultacji', 'booking-system-df'),
            'manage_options',
            'booking-types',
            array($this, 'display_types_page')
        );
        
        add_submenu_page(
            'booking-dashboard',
            __('Dostępność', 'booking-system-df'),
            __('Dostępność', 'booking-system-df'),
            'manage_options',
            'booking-availability',
            array($this, 'display_availability_page')
        );
        
        add_submenu_page(
            'booking-dashboard',
            __('Ustawienia', 'booking-system-df'),
            __('Ustawienia', 'booking-system-df'),
            'manage_options',
            'booking-settings',
            array($this, 'display_settings_page')
        );
    }

    public function display_dashboard_page() {
        include BOOKING_SYSTEM_DF_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    public function display_consultations_page() {
        // Handle actions
        if (isset($_GET['action']) && isset($_GET['id'])) {
            $consultation_id = intval($_GET['id']);
            
            switch ($_GET['action']) {
                case 'confirm':
                    $result = Booking_Engine::confirm_consultation($consultation_id);
                    if ($result->is_success()) {
                        echo '<div class="notice notice-success"><p>' . __('Konsultacja została potwierdzona.', 'booking-system-df') . '</p></div>';
                    } else {
                        echo '<div class="notice notice-error"><p>' . esc_html($result->get_error()) . '</p></div>';
                    }
                    break;
                    
                case 'cancel':
                    $reason = isset($_POST['reason']) ? sanitize_textarea_field($_POST['reason']) : '';
                    $result = Booking_Engine::cancel_consultation($consultation_id, $reason);
                    if ($result->is_success()) {
                        echo '<div class="notice notice-success"><p>' . __('Konsultacja została anulowana.', 'booking-system-df') . '</p></div>';
                    } else {
                        echo '<div class="notice notice-error"><p>' . esc_html($result->get_error()) . '</p></div>';
                    }
                    break;
            }
        }
        
        include BOOKING_SYSTEM_DF_PLUGIN_DIR . 'admin/views/consultations.php';
    }

    public function display_types_page() {
        // Handle form submission
        if (isset($_POST['save_type'])) {
            check_admin_referer('booking_type_form');
            
            $type = new Consultation_Type();
            
            if (isset($_POST['type_id']) && $_POST['type_id']) {
                $type = Consultation_Type::get_by_id(intval($_POST['type_id']));
            }
            
            $type->name = sanitize_text_field($_POST['name']);
            $type->description = sanitize_textarea_field($_POST['description']);
            $type->duration_minutes = intval($_POST['duration_minutes']);
            $type->price = floatval($_POST['price']);
            $type->currency = sanitize_text_field($_POST['currency']);
            $type->is_active = isset($_POST['is_active']) ? 1 : 0;
            
            $type->save();
            
            echo '<div class="notice notice-success"><p>' . __('Typ konsultacji został zapisany.', 'booking-system-df') . '</p></div>';
        }
        
        include BOOKING_SYSTEM_DF_PLUGIN_DIR . 'admin/views/types.php';
    }

    public function display_availability_page() {
        // Handle form submissions
        if (isset($_POST['save_rule'])) {
            check_admin_referer('booking_availability_form');
            
            $rule = new Availability_Rule();
            
            if (isset($_POST['rule_id']) && $_POST['rule_id']) {
                $rule->id = intval($_POST['rule_id']);
            }
            
            $rule->day_of_week = intval($_POST['day_of_week']);
            $rule->start_time = sanitize_text_field($_POST['start_time']);
            $rule->end_time = sanitize_text_field($_POST['end_time']);
            $rule->is_active = isset($_POST['is_active']) ? 1 : 0;
            
            $rule->save();
            
            echo '<div class="notice notice-success"><p>' . __('Reguła dostępności została zapisana.', 'booking-system-df') . '</p></div>';
        }
        
        if (isset($_POST['save_blocked_period'])) {
            check_admin_referer('booking_blocked_period_form');
            
            $period = new Blocked_Period();
            
            if (isset($_POST['period_id']) && $_POST['period_id']) {
                $period->id = intval($_POST['period_id']);
            }
            
            $period->start_datetime = sanitize_text_field($_POST['start_datetime']);
            $period->end_datetime = sanitize_text_field($_POST['end_datetime']);
            $period->reason = sanitize_textarea_field($_POST['reason']);
            
            $period->save();
            
            echo '<div class="notice notice-success"><p>' . __('Okres blokady został zapisany.', 'booking-system-df') . '</p></div>';
        }
        
        include BOOKING_SYSTEM_DF_PLUGIN_DIR . 'admin/views/availability.php';
    }

    public function display_settings_page() {
        // Handle form submission
        if (isset($_POST['save_settings'])) {
            check_admin_referer('booking_settings_form');
            
            global $wpdb;
            $table = $wpdb->prefix . 'booking_settings';
            
            $settings = array(
                'payu_client_id' => Encryption_Helper::encrypt(sanitize_text_field($_POST['payu_client_id'])),
                'payu_client_secret' => Encryption_Helper::encrypt(sanitize_text_field($_POST['payu_client_secret'])),
                'payu_pos_id' => Encryption_Helper::encrypt(sanitize_text_field($_POST['payu_pos_id'])),
                'payu_sandbox' => isset($_POST['payu_sandbox']) ? '1' : '0',
                'google_client_id' => Encryption_Helper::encrypt(sanitize_text_field($_POST['google_client_id'])),
                'google_client_secret' => Encryption_Helper::encrypt(sanitize_text_field($_POST['google_client_secret'])),
                'google_refresh_token' => Encryption_Helper::encrypt(sanitize_text_field($_POST['google_refresh_token']))
            );
            
            foreach ($settings as $key => $value) {
                $wpdb->replace($table, array(
                    'setting_key' => $key,
                    'setting_value' => $value
                ));
            }
            
            echo '<div class="notice notice-success"><p>' . __('Ustawienia zostały zapisane.', 'booking-system-df') . '</p></div>';
        }
        
        include BOOKING_SYSTEM_DF_PLUGIN_DIR . 'admin/views/settings.php';
    }
}
