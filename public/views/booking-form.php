<?php
/**
 * Booking form view.
 */
if (!defined('ABSPATH')) exit;
?>

<div class="booking-form-container">
    <h2><?php echo esc_html($type->name); ?></h2>
    <p><?php echo esc_html($type->description); ?></p>
    <p class="booking-price"><strong><?php _e('Cena:', 'booking-system-df'); ?></strong> <?php echo esc_html($type->price . ' ' . $type->currency); ?></p>
    
    <form method="post" class="booking-form" id="booking-form">
        <?php wp_nonce_field('booking_form', 'booking_nonce'); ?>
        <input type="hidden" name="type_id" value="<?php echo esc_attr($type_id); ?>">
        
        <div class="form-group">
            <label><?php _e('Wybierz termin', 'booking-system-df'); ?> *</label>
            <div id="booking-calendar-widget" data-type-id="<?php echo esc_attr($type_id); ?>"></div>
            <input type="hidden" name="start_datetime" id="start_datetime" required>
            <input type="hidden" name="end_datetime" id="end_datetime" required>
        </div>
        
        <div class="form-group">
            <label for="patient_name"><?php _e('Imię i nazwisko', 'booking-system-df'); ?> *</label>
            <input type="text" name="patient_name" id="patient_name" required>
        </div>
        
        <div class="form-group">
            <label for="patient_email"><?php _e('Email', 'booking-system-df'); ?> *</label>
            <input type="email" name="patient_email" id="patient_email" required>
        </div>
        
        <div class="form-group">
            <label for="patient_phone"><?php _e('Telefon', 'booking-system-df'); ?> *</label>
            <input type="tel" name="patient_phone" id="patient_phone" required>
        </div>
        
        <div class="form-group">
            <label for="patient_notes"><?php _e('Notatki (opcjonalnie)', 'booking-system-df'); ?></label>
            <textarea name="patient_notes" id="patient_notes" rows="4"></textarea>
        </div>
        
        <div class="form-group">
            <button type="submit" name="submit_booking" class="button button-primary">
                <?php _e('Przejdź do płatności', 'booking-system-df'); ?>
            </button>
        </div>
    </form>
</div>
