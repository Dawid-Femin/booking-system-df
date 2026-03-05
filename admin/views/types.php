<?php
/**
 * Consultation types view.
 */
if (!defined('ABSPATH')) exit;

$types = Consultation_Type::get_all_active();
?>

<div class="wrap">
    <h1><?php _e('Typy konsultacji', 'booking-system-df'); ?></h1>
    
    <h2><?php _e('Dodaj nowy typ', 'booking-system-df'); ?></h2>
    <form method="post">
        <?php wp_nonce_field('booking_type_form'); ?>
        <table class="form-table">
            <tr>
                <th><label><?php _e('Nazwa', 'booking-system-df'); ?></label></th>
                <td><input type="text" name="name" required class="regular-text"></td>
            </tr>
            <tr>
                <th><label><?php _e('Opis', 'booking-system-df'); ?></label></th>
                <td><textarea name="description" class="large-text"></textarea></td>
            </tr>
            <tr>
                <th><label><?php _e('Czas trwania (minuty)', 'booking-system-df'); ?></label></th>
                <td><input type="number" name="duration_minutes" value="60" required></td>
            </tr>
            <tr>
                <th><label><?php _e('Cena', 'booking-system-df'); ?></label></th>
                <td><input type="number" step="0.01" name="price" required></td>
            </tr>
            <tr>
                <th><label><?php _e('Waluta', 'booking-system-df'); ?></label></th>
                <td><input type="text" name="currency" value="PLN" required></td>
            </tr>
            <tr>
                <th><label><?php _e('Aktywny', 'booking-system-df'); ?></label></th>
                <td><input type="checkbox" name="is_active" checked></td>
            </tr>
        </table>
        <p><input type="submit" name="save_type" class="button button-primary" value="<?php _e('Zapisz', 'booking-system-df'); ?>"></p>
    </form>
    
    <h2><?php _e('Istniejące typy', 'booking-system-df'); ?></h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Nazwa', 'booking-system-df'); ?></th>
                <th><?php _e('Czas trwania', 'booking-system-df'); ?></th>
                <th><?php _e('Cena', 'booking-system-df'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($types as $type): ?>
                <tr>
                    <td><?php echo esc_html($type->name); ?></td>
                    <td><?php echo esc_html($type->duration_minutes . ' min'); ?></td>
                    <td><?php echo esc_html($type->price . ' ' . $type->currency); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
