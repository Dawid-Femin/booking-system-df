<?php
/**
 * Booking confirmation view.
 */
if (!defined('ABSPATH')) exit;
?>

<div class="booking-confirmation">
    <?php if ($booking_status === 'success'): ?>
        <div class="booking-success">
            <h2>✅ <?php _e('Rezerwacja potwierdzona!', 'booking-system-df'); ?></h2>
            
            <?php if ($dev_mode === '1'): ?>
                <div class="dev-mode-notice" style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px;">
                    <strong>🔧 Tryb deweloperski</strong><br>
                    <?php _e('Płatność została automatycznie zaakceptowana (localhost).', 'booking-system-df'); ?>
                </div>
            <?php endif; ?>
            
            <div class="confirmation-details" style="background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 5px;">
                <h3><?php _e('Szczegóły rezerwacji:', 'booking-system-df'); ?></h3>
                <p><strong><?php _e('Numer rezerwacji:', 'booking-system-df'); ?></strong> #<?php echo esc_html($consultation->id); ?></p>
                <p><strong><?php _e('Data i godzina:', 'booking-system-df'); ?></strong> <?php echo esc_html(date('d.m.Y H:i', strtotime($consultation->start_datetime))); ?></p>
                <p><strong><?php _e('Typ konsultacji:', 'booking-system-df'); ?></strong> <?php 
                    $type = Consultation_Type::get_by_id($consultation->consultation_type_id);
                    echo esc_html($type ? $type->name : 'N/A');
                ?></p>
                <p><strong><?php _e('Status:', 'booking-system-df'); ?></strong> <?php echo esc_html(ConsultationStatus::get_label($consultation->status)); ?></p>
                <p><strong><?php _e('Kwota:', 'booking-system-df'); ?></strong> <?php echo esc_html($consultation->payment_data->amount . ' ' . $consultation->payment_data->currency); ?></p>
            </div>
            
            <div class="next-steps" style="background: #e7f3ff; padding: 20px; margin: 20px 0; border-radius: 5px;">
                <h3><?php _e('Co dalej?', 'booking-system-df'); ?></h3>
                <ol>
                    <li><?php _e('Właściciel otrzymał powiadomienie o Twojej rezerwacji.', 'booking-system-df'); ?></li>
                    <li><?php _e('Po zatwierdzeniu rezerwacji otrzymasz email z linkiem do spotkania Google Meet.', 'booking-system-df'); ?></li>
                    <li><?php _e('Sprawdź swoją skrzynkę email (również folder SPAM).', 'booking-system-df'); ?></li>
                </ol>
            </div>
            
            <p style="text-align: center; margin-top: 30px;">
                <a href="<?php echo home_url(); ?>" class="button" style="display: inline-block; padding: 12px 24px; background: #0073aa; color: white; text-decoration: none; border-radius: 5px;">
                    <?php _e('Powrót do strony głównej', 'booking-system-df'); ?>
                </a>
            </p>
        </div>
        
    <?php elseif ($booking_status === 'pending'): ?>
        <div class="booking-pending">
            <h2>⏳ <?php _e('Oczekiwanie na potwierdzenie płatności', 'booking-system-df'); ?></h2>
            
            <div class="confirmation-details" style="background: #fff3cd; padding: 20px; margin: 20px 0; border-radius: 5px;">
                <p><?php _e('Twoja płatność jest w trakcie przetwarzania.', 'booking-system-df'); ?></p>
                <p><?php _e('Otrzymasz email z potwierdzeniem, gdy płatność zostanie zrealizowana.', 'booking-system-df'); ?></p>
            </div>
            
            <div class="confirmation-details" style="background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 5px;">
                <h3><?php _e('Szczegóły rezerwacji:', 'booking-system-df'); ?></h3>
                <p><strong><?php _e('Numer rezerwacji:', 'booking-system-df'); ?></strong> #<?php echo esc_html($consultation->id); ?></p>
                <p><strong><?php _e('Data i godzina:', 'booking-system-df'); ?></strong> <?php echo esc_html(date('d.m.Y H:i', strtotime($consultation->start_datetime))); ?></p>
                <p><strong><?php _e('Status:', 'booking-system-df'); ?></strong> <?php echo esc_html(ConsultationStatus::get_label($consultation->status)); ?></p>
            </div>
            
            <p style="text-align: center; margin-top: 30px;">
                <a href="<?php echo home_url(); ?>" class="button" style="display: inline-block; padding: 12px 24px; background: #0073aa; color: white; text-decoration: none; border-radius: 5px;">
                    <?php _e('Powrót do strony głównej', 'booking-system-df'); ?>
                </a>
            </p>
        </div>
        
    <?php else: ?>
        <div class="booking-error">
            <h2>❌ <?php _e('Wystąpił problem', 'booking-system-df'); ?></h2>
            <p><?php _e('Nie udało się przetworzyć Twojej rezerwacji.', 'booking-system-df'); ?></p>
            <p><?php _e('Skontaktuj się z nami, jeśli problem będzie się powtarzał.', 'booking-system-df'); ?></p>
            
            <p style="text-align: center; margin-top: 30px;">
                <a href="<?php echo home_url(); ?>" class="button" style="display: inline-block; padding: 12px 24px; background: #0073aa; color: white; text-decoration: none; border-radius: 5px;">
                    <?php _e('Powrót do strony głównej', 'booking-system-df'); ?>
                </a>
            </p>
        </div>
    <?php endif; ?>
</div>
