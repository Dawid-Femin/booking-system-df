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
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
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
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
