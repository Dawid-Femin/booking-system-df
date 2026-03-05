<?php
/**
 * My consultations view.
 */
if (!defined('ABSPATH')) exit;
?>

<div class="my-consultations">
    <h2><?php _e('Moje konsultacje', 'booking-system-df'); ?></h2>
    
    <?php foreach ($consultations as $row): ?>
        <?php 
        $consultation = Consultation::get_by_id($row->id);
        $type = Consultation_Type::get_by_id($consultation->consultation_type_id);
        $start = new DateTime($consultation->start_datetime, new DateTimeZone('Europe/Warsaw'));
        ?>
        
        <div class="consultation-card">
            <h3><?php echo esc_html($type->name); ?></h3>
            <p><strong><?php _e('Data:', 'booking-system-df'); ?></strong> <?php echo esc_html($start->format('d.m.Y H:i')); ?></p>
            <p><strong><?php _e('Status:', 'booking-system-df'); ?></strong> <?php echo esc_html(ConsultationStatus::get_label($consultation->status)); ?></p>
            
            <?php if ($consultation->google_meet_link): ?>
                <p><strong><?php _e('Link do spotkania:', 'booking-system-df'); ?></strong></p>
                <p><a href="<?php echo esc_url($consultation->google_meet_link); ?>" target="_blank" class="button">
                    <?php _e('Dołącz do spotkania', 'booking-system-df'); ?>
                </a></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
