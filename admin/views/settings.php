<?php
/**
 * Settings view.
 */
if (!defined('ABSPATH')) exit;
?>

<div class="wrap">
    <h1><?php _e('Ustawienia', 'booking-system-df'); ?></h1>
    
    <form method="post">
        <?php wp_nonce_field('booking_settings_form'); ?>
        
        <h2><?php _e('PayU', 'booking-system-df'); ?></h2>
        <table class="form-table">
            <tr>
                <th><label><?php _e('Client ID', 'booking-system-df'); ?></label></th>
                <td><input type="text" name="payu_client_id" class="regular-text"></td>
            </tr>
            <tr>
                <th><label><?php _e('Client Secret', 'booking-system-df'); ?></label></th>
                <td><input type="password" name="payu_client_secret" class="regular-text"></td>
            </tr>
            <tr>
                <th><label><?php _e('POS ID', 'booking-system-df'); ?></label></th>
                <td><input type="text" name="payu_pos_id" class="regular-text"></td>
            </tr>
            <tr>
                <th><label><?php _e('Tryb Sandbox', 'booking-system-df'); ?></label></th>
                <td><input type="checkbox" name="payu_sandbox"></td>
            </tr>
        </table>
        
        <h2><?php _e('Google Meet', 'booking-system-df'); ?></h2>
        <table class="form-table">
            <tr>
                <th><label><?php _e('Client ID', 'booking-system-df'); ?></label></th>
                <td><input type="text" name="google_client_id" class="regular-text"></td>
            </tr>
            <tr>
                <th><label><?php _e('Client Secret', 'booking-system-df'); ?></label></th>
                <td><input type="password" name="google_client_secret" class="regular-text"></td>
            </tr>
            <tr>
                <th><label><?php _e('Refresh Token', 'booking-system-df'); ?></label></th>
                <td><input type="text" name="google_refresh_token" class="regular-text"></td>
            </tr>
        </table>
        
        <p><input type="submit" name="save_settings" class="button button-primary" value="<?php _e('Zapisz ustawienia', 'booking-system-df'); ?>"></p>
    </form>
</div>
