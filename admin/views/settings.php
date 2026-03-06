<?php
/**
 * Settings view.
 */
if (!defined('ABSPATH')) exit;

// Load current settings
global $wpdb;
$table = $wpdb->prefix . 'booking_settings';

$payu_client_id = '';
$payu_client_secret = '';
$payu_pos_id = '';
$payu_sandbox = '';
$google_client_id = '';
$google_client_secret = '';
$google_refresh_token = '';

// Fetch and decrypt settings
$settings = $wpdb->get_results("SELECT setting_key, setting_value FROM $table", OBJECT_K);

if (isset($settings['payu_client_id'])) {
    $payu_client_id = Encryption_Helper::decrypt($settings['payu_client_id']->setting_value);
}
if (isset($settings['payu_client_secret'])) {
    $payu_client_secret = Encryption_Helper::decrypt($settings['payu_client_secret']->setting_value);
}
if (isset($settings['payu_pos_id'])) {
    $payu_pos_id = Encryption_Helper::decrypt($settings['payu_pos_id']->setting_value);
}
if (isset($settings['payu_sandbox'])) {
    $payu_sandbox = $settings['payu_sandbox']->setting_value;
}
if (isset($settings['google_client_id'])) {
    $google_client_id = Encryption_Helper::decrypt($settings['google_client_id']->setting_value);
}
if (isset($settings['google_client_secret'])) {
    $google_client_secret = Encryption_Helper::decrypt($settings['google_client_secret']->setting_value);
}
if (isset($settings['google_refresh_token'])) {
    $google_refresh_token = Encryption_Helper::decrypt($settings['google_refresh_token']->setting_value);
}
?>

<div class="wrap">
    <h1><?php _e('Ustawienia', 'booking-system-df'); ?></h1>
    
    <form method="post">
        <?php wp_nonce_field('booking_settings_form'); ?>
        
        <h2><?php _e('PayU', 'booking-system-df'); ?></h2>
        <table class="form-table">
            <tr>
                <th><label><?php _e('Client ID', 'booking-system-df'); ?></label></th>
                <td><input type="text" name="payu_client_id" class="regular-text" value="<?php echo esc_attr($payu_client_id); ?>"></td>
            </tr>
            <tr>
                <th><label><?php _e('Client Secret', 'booking-system-df'); ?></label></th>
                <td><input type="password" name="payu_client_secret" class="regular-text" value="<?php echo esc_attr($payu_client_secret); ?>"></td>
            </tr>
            <tr>
                <th><label><?php _e('POS ID', 'booking-system-df'); ?></label></th>
                <td><input type="text" name="payu_pos_id" class="regular-text" value="<?php echo esc_attr($payu_pos_id); ?>"></td>
            </tr>
            <tr>
                <th><label><?php _e('Tryb Sandbox', 'booking-system-df'); ?></label></th>
                <td>
                    <input type="checkbox" name="payu_sandbox" <?php checked($payu_sandbox, '1'); ?>>
                    <p class="description"><?php _e('Używaj środowiska testowego PayU', 'booking-system-df'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label><?php _e('Wymuś PayU na localhost', 'booking-system-df'); ?></label></th>
                <td>
                    <input type="checkbox" name="payu_force_on_localhost" <?php 
                        if (isset($settings['payu_force_on_localhost'])) {
                            checked($settings['payu_force_on_localhost']->setting_value, '1');
                        }
                    ?>>
                    <p class="description"><?php _e('Przekieruj do PayU nawet na localhost (może zwrócić błąd 403)', 'booking-system-df'); ?></p>
                </td>
            </tr>
        </table>
        
        <h2><?php _e('Google Meet', 'booking-system-df'); ?></h2>
        <table class="form-table">
            <tr>
                <th><label><?php _e('Client ID', 'booking-system-df'); ?></label></th>
                <td><input type="text" name="google_client_id" class="regular-text" value="<?php echo esc_attr($google_client_id); ?>"></td>
            </tr>
            <tr>
                <th><label><?php _e('Client Secret', 'booking-system-df'); ?></label></th>
                <td><input type="password" name="google_client_secret" class="regular-text" value="<?php echo esc_attr($google_client_secret); ?>"></td>
            </tr>
            <tr>
                <th><label><?php _e('Refresh Token', 'booking-system-df'); ?></label></th>
                <td><input type="text" name="google_refresh_token" class="regular-text" value="<?php echo esc_attr($google_refresh_token); ?>"></td>
            </tr>
        </table>
        
        <p><input type="submit" name="save_settings" class="button button-primary" value="<?php _e('Zapisz ustawienia', 'booking-system-df'); ?>"></p>
    </form>
</div>
