<?php
/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Booking_System {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->version = BOOKING_SYSTEM_DF_VERSION;
        $this->plugin_name = 'booking-system-df';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    private function load_dependencies() {
        require_once BOOKING_SYSTEM_DF_PLUGIN_DIR . 'includes/class-booking-system-loader.php';
        
        $this->loader = new Booking_System_Loader();
        Booking_System_Loader::load_all();
    }

    private function set_locale() {
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
    }

    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'booking-system-df',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

    private function define_admin_hooks() {
        if (is_admin()) {
            $admin = new Booking_System_Admin($this->get_plugin_name(), $this->get_version());
            
            add_action('admin_enqueue_scripts', array($admin, 'enqueue_styles'));
            add_action('admin_enqueue_scripts', array($admin, 'enqueue_scripts'));
            add_action('admin_menu', array($admin, 'add_admin_menu'));
        }
    }

    private function define_public_hooks() {
        $public = new Booking_System_Public($this->get_plugin_name(), $this->get_version());
        
        add_action('wp_enqueue_scripts', array($public, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($public, 'enqueue_scripts'));
        
        // Register shortcodes
        add_shortcode('booking_calendar', array($public, 'booking_calendar_shortcode'));
        add_shortcode('booking_form', array($public, 'booking_form_shortcode'));
        add_shortcode('my_consultations', array($public, 'my_consultations_shortcode'));
        add_shortcode('booking_confirmation', array($public, 'booking_confirmation_shortcode'));
    }

    public function run() {
        // Plugin is initialized
    }

    public function get_plugin_name() {
        return $this->plugin_name;
    }

    public function get_version() {
        return $this->version;
    }
}
