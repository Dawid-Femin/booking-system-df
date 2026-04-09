<?php
/**
 * Consultations list view.
 */
if (!defined('ABSPATH')) exit;

global $wpdb;
$table = $wpdb->prefix . 'booking_consultations';
$consultations = $wpdb->get_results("SELECT * FROM $table ORDER BY start_datetime DESC LIMIT 50");
?>

<div class="wrap">
    <h1><?php _e('Konsultacje', 'booking-system-df'); ?></h1>

    <form method="post" action="<?php echo admin_url('admin.php?page=booking-consultations'); ?>">
        <?php wp_nonce_field('booking_bulk_action'); ?>

        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <select name="bulk_action">
                    <option value=""><?php _e('Akcje masowe', 'booking-system-df'); ?></option>
                    <option value="delete"><?php _e('Usuń', 'booking-system-df'); ?></option>
                </select>
                <input type="submit" name="do_bulk_action" class="button action" value="<?php _e('Zastosuj', 'booking-system-df'); ?>">
            </div>
        </div>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <td class="manage-column column-cb check-column">
                        <input type="checkbox" id="cb-select-all">
                    </td>
                    <th>ID</th>
                    <th><?php _e('Data', 'booking-system-df'); ?></th>
                    <th><?php _e('Pacjent', 'booking-system-df'); ?></th>
                    <th><?php _e('Status', 'booking-system-df'); ?></th>
                    <th><?php _e('Kwota', 'booking-system-df'); ?></th>
                    <th><?php _e('Akcje', 'booking-system-df'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultations as $row): ?>
                    <?php $consultation = Consultation::get_by_id($row->id); ?>
                    <tr>
                        <th class="check-column">
                            <input type="checkbox" name="consultation_ids[]" value="<?php echo $consultation->id; ?>">
                        </th>
                        <td><?php echo esc_html($consultation->id); ?></td>
                        <td><?php echo esc_html(date('d.m.Y H:i', strtotime($consultation->start_datetime))); ?></td>
                        <td><?php echo esc_html($consultation->patient_data->name); ?></td>
                        <td><?php echo esc_html(ConsultationStatus::get_label($consultation->status)); ?></td>
                        <td><?php echo esc_html($consultation->payment_data->amount . ' ' . $consultation->payment_data->currency); ?></td>
                        <td>
                            <?php if ($consultation->status === ConsultationStatus::PAYMENT_COMPLETED): ?>
                                <a href="?page=booking-consultations&action=confirm&id=<?php echo $consultation->id; ?>" class="button button-primary">
                                    <?php _e('Potwierdź', 'booking-system-df'); ?>
                                </a>
                            <?php endif; ?>
                            <a href="?page=booking-consultations&action=edit&id=<?php echo $consultation->id; ?>" class="button">
                                <?php _e('Edytuj', 'booking-system-df'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="tablenav bottom">
            <div class="alignleft actions bulkactions">
                <select name="bulk_action">
                    <option value=""><?php _e('Akcje masowe', 'booking-system-df'); ?></option>
                    <option value="delete"><?php _e('Usuń', 'booking-system-df'); ?></option>
                </select>
                <input type="submit" name="do_bulk_action" class="button action" value="<?php _e('Zastosuj', 'booking-system-df'); ?>">
            </div>
        </div>
    </form>
</div>

<script>
document.getElementById('cb-select-all').addEventListener('change', function() {
    document.querySelectorAll('input[name="consultation_ids[]"]').forEach(cb => cb.checked = this.checked);
});
</script>
