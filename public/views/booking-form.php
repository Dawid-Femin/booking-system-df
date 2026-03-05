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
            
            <?php
            // Generate available slots
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d', strtotime('+30 days'));
            
            $slots_result = Availability_Manager::get_available_slots($type_id, $start_date, $end_date);
            $slots = $slots_result->is_success() ? $slots_result->get_data() : array();
            ?>
            
            <div class="booking-calendar">
                <?php if (empty($slots)): ?>
                    <p class="booking-error"><?php _e('Brak dostępnych terminów w ciągu najbliższych 30 dni. Skontaktuj się z nami bezpośrednio.', 'booking-system-df'); ?></p>
                <?php else: ?>
                    <?php
                    $polish_days = array(
                        'Monday' => 'Poniedziałek',
                        'Tuesday' => 'Wtorek',
                        'Wednesday' => 'Środa',
                        'Thursday' => 'Czwartek',
                        'Friday' => 'Piątek',
                        'Saturday' => 'Sobota',
                        'Sunday' => 'Niedziela'
                    );
                    ?>
                    <div class="booking-slots">
                        <?php
                        $current_date = '';
                        foreach ($slots as $slot):
                            $slot_date = $slot->start->format('Y-m-d');
                            
                            if ($slot_date !== $current_date):
                                if ($current_date !== '') echo '</div></div>';
                                $current_date = $slot_date;
                                $day_name_en = $slot->start->format('l');
                                $day_name_pl = isset($polish_days[$day_name_en]) ? $polish_days[$day_name_en] : $day_name_en;
                                ?>
                                <div class="booking-day">
                                    <h4><?php echo esc_html($day_name_pl . ', ' . $slot->start->format('d.m.Y')); ?></h4>
                                    <div class="booking-day-slots">
                            <?php endif; ?>
                            
                            <button type="button" class="booking-slot-button" 
                                    data-start="<?php echo esc_attr($slot->get_start_formatted()); ?>"
                                    data-end="<?php echo esc_attr($slot->get_end_formatted()); ?>">
                                <?php echo esc_html($slot->get_start_time()); ?>
                            </button>
                            
                        <?php endforeach; ?>
                        </div></div>
                    </div>
                <?php endif; ?>
            </div>
            
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
