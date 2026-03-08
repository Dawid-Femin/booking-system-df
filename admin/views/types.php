<?php
/**
 * Consultation types view.
 */
if (!defined('ABSPATH')) exit;

// Get all types (including inactive for editing)
global $wpdb;
$table = $wpdb->prefix . 'booking_consultation_types';
$types = $wpdb->get_results("SELECT * FROM $table ORDER BY name ASC");

// Check if editing
$editing_type = null;
if (isset($_GET['edit']) && $_GET['edit']) {
    $editing_type = Consultation_Type::get_by_id(intval($_GET['edit']));
}
?>

<div class="wrap">
    <h1><?php _e('Typy konsultacji', 'booking-system-df'); ?></h1>
    
    <h2><?php echo $editing_type ? __('Edytuj typ', 'booking-system-df') : __('Dodaj nowy typ', 'booking-system-df'); ?></h2>
    <form method="post">
        <?php wp_nonce_field('booking_type_form'); ?>
        <?php if ($editing_type): ?>
            <input type="hidden" name="type_id" value="<?php echo esc_attr($editing_type->id); ?>">
        <?php endif; ?>
        <table class="form-table">
            <tr>
                <th><label><?php _e('Nazwa', 'booking-system-df'); ?></label></th>
                <td><input type="text" name="name" required class="regular-text" value="<?php echo $editing_type ? esc_attr($editing_type->name) : ''; ?>"></td>
            </tr>
            <tr>
                <th><label><?php _e('Opis', 'booking-system-df'); ?></label></th>
                <td><textarea name="description" class="large-text"><?php echo $editing_type ? esc_textarea($editing_type->description) : ''; ?></textarea></td>
            </tr>
            <tr>
                <th><label><?php _e('Czas trwania (minuty)', 'booking-system-df'); ?></label></th>
                <td><input type="number" name="duration_minutes" value="<?php echo $editing_type ? esc_attr($editing_type->duration_minutes) : '60'; ?>" required></td>
            </tr>
            <tr>
                <th><label><?php _e('Cena', 'booking-system-df'); ?></label></th>
                <td><input type="number" step="0.01" name="price" value="<?php echo $editing_type ? esc_attr($editing_type->price) : ''; ?>" required></td>
            </tr>
            <tr>
                <th><label><?php _e('Waluta', 'booking-system-df'); ?></label></th>
                <td><input type="text" name="currency" value="<?php echo $editing_type ? esc_attr($editing_type->currency) : 'PLN'; ?>" required></td>
            </tr>
            <tr>
                <th><label><?php _e('Aktywny', 'booking-system-df'); ?></label></th>
                <td><input type="checkbox" name="is_active" <?php echo ($editing_type && $editing_type->is_active) || !$editing_type ? 'checked' : ''; ?>></td>
            </tr>
        </table>
        <p>
            <input type="submit" name="save_type" class="button button-primary" value="<?php _e('Zapisz', 'booking-system-df'); ?>">
            <?php if ($editing_type): ?>
                <a href="<?php echo admin_url('admin.php?page=booking-types'); ?>" class="button"><?php _e('Anuluj', 'booking-system-df'); ?></a>
            <?php endif; ?>
        </p>
    </form>
    
    <h2><?php _e('Istniejące typy', 'booking-system-df'); ?></h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Nazwa', 'booking-system-df'); ?></th>
                <th><?php _e('Czas trwania', 'booking-system-df'); ?></th>
                <th><?php _e('Cena', 'booking-system-df'); ?></th>
                <th><?php _e('Status', 'booking-system-df'); ?></th>
                <th><?php _e('Akcje', 'booking-system-df'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($types)): ?>
                <tr>
                    <td colspan="5"><?php _e('Brak typów konsultacji', 'booking-system-df'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($types as $type): ?>
                    <tr>
                        <td><?php echo esc_html($type->name); ?></td>
                        <td><?php echo esc_html($type->duration_minutes . ' min'); ?></td>
                        <td><?php echo esc_html($type->price . ' ' . $type->currency); ?></td>
                        <td><?php echo $type->is_active ? __('Aktywny', 'booking-system-df') : __('Nieaktywny', 'booking-system-df'); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=booking-types&edit=' . $type->id); ?>" class="button button-small"><?php _e('Edytuj', 'booking-system-df'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
