<?php
/**
 * Admin dashboard view.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */

if (!defined('ABSPATH')) {
    exit;
}

$upcoming = Consultation::get_upcoming(10);

global $wpdb;
$table = $wpdb->prefix . 'booking_consultations';

$stats = array(
    'pending' => $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE status = %s",
        ConsultationStatus::PAYMENT_COMPLETED
    )),
    'confirmed' => $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE status = %s AND start_datetime > NOW()",
        ConsultationStatus::CONFIRMED
    )),
    'completed_today' => $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE status = %s AND DATE(end_datetime) = CURDATE()",
        ConsultationStatus::COMPLETED
    ))
);
?>

<div class="wrap">
    <h1><?php _e('Panel główny - System Rezerwacji', 'booking-system-df'); ?></h1>
    
    <div class="booking-stats" style="display: flex; gap: 20px; margin: 20px 0;">
        <div class="stat-box" style="background: #fff; padding: 20px; border-left: 4px solid #f39c12; flex: 1;">
            <h3><?php echo esc_html($stats['pending']); ?></h3>
            <p><?php _e('Oczekujące na potwierdzenie', 'booking-system-df'); ?></p>
        </div>
        
        <div class="stat-box" style="background: #fff; padding: 20px; border-left: 4px solid #3498db; flex: 1;">
            <h3><?php echo esc_html($stats['confirmed']); ?></h3>
            <p><?php _e('Potwierdzone nadchodzące', 'booking-system-df'); ?></p>
        </div>
        
        <div class="stat-box" style="background: #fff; padding: 20px; border-left: 4px solid #2ecc71; flex: 1;">
            <h3><?php echo esc_html($stats['completed_today']); ?></h3>
            <p><?php _e('Zakończone dzisiaj', 'booking-system-df'); ?></p>
        </div>
    </div>
    
    <h2><?php _e('Nadchodzące konsultacje', 'booking-system-df'); ?></h2>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Data i godzina', 'booking-system-df'); ?></th>
                <th><?php _e('Pacjent', 'booking-system-df'); ?></th>
                <th><?php _e('Typ', 'booking-system-df'); ?></th>
                <th><?php _e('Status', 'booking-system-df'); ?></th>
                <th><?php _e('Akcje', 'booking-system-df'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($upcoming)): ?>
                <tr>
                    <td colspan="5"><?php _e('Brak nadchodzących konsultacji', 'booking-system-df'); ?></td>
                </tr>
            <?php else: ?>
                <?php foreach ($upcoming as $consultation): ?>
                    <?php
                    $type = Consultation_Type::get_by_id($consultation->consultation_type_id);
                    $start = new DateTime($consultation->start_datetime, new DateTimeZone('Europe/Warsaw'));
                    ?>
                    <tr>
                        <td><?php echo esc_html($start->format('d.m.Y H:i')); ?></td>
                        <td><?php echo esc_html($consultation->patient_data->name); ?></td>
                        <td><?php echo esc_html($type->name); ?></td>
                        <td><?php echo esc_html(ConsultationStatus::get_label($consultation->status)); ?></td>
                        <td>
                            <a href="<?php echo esc_url(admin_url('admin.php?page=booking-consultations&action=view&id=' . $consultation->id)); ?>" class="button button-small">
                                <?php _e('Zobacz', 'booking-system-df'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
