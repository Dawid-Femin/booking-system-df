<?php
/**
 * Booking form view - modern calendar design.
 */
if (!defined('ABSPATH')) exit;
?>

<div class="booking-form-container">
    <div class="booking-header">
        <h2><?php echo esc_html($type->name); ?></h2>
        <p class="booking-description"><?php echo esc_html($type->description); ?></p>
        <div class="booking-meta">
            <span class="booking-duration">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM7 4v4.5l3.5 2 .5-.9-3-1.7V4H7z"/>
                </svg>
                <?php echo esc_html($type->duration_minutes); ?> min
            </span>
            <span class="booking-price">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zm1 4H7v1h2v1H7v1h2v1H7v1h2v1H7v1h2a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1z"/>
                </svg>
                <?php echo esc_html($type->price . ' ' . $type->currency); ?>
            </span>
        </div>
    </div>
    
    <form method="post" class="booking-form" id="booking-form">
        <?php wp_nonce_field('booking_form', 'booking_nonce'); ?>
        <input type="hidden" name="type_id" value="<?php echo esc_attr($type_id); ?>">
        <input type="hidden" name="start_datetime" id="start_datetime" required>
        <input type="hidden" name="end_datetime" id="end_datetime" required>
        
        <div class="booking-step" id="step-1">
            <h3 class="step-title">
                <span class="step-number">1</span>
                <?php _e('Wybierz termin', 'booking-system-df'); ?>
            </h3>
            
            <?php
            // Generate available slots
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d', strtotime('+30 days'));
            
            $slots_result = Availability_Manager::get_available_slots($type_id, $start_date, $end_date);
            $slots = $slots_result->is_success() ? $slots_result->get_data() : array();
            ?>
            
            <?php if (empty($slots)): ?>
                <div class="booking-no-slots">
                    <svg width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 0a8 8 0 1 0 0 16A8 8 0 0 0 8 0zM7 4h2v5H7V4zm0 6h2v2H7v-2z"/>
                    </svg>
                    <p><?php _e('Brak dostępnych terminów w ciągu najbliższych 30 dni.', 'booking-system-df'); ?></p>
                    <p><?php _e('Skontaktuj się z nami bezpośrednio.', 'booking-system-df'); ?></p>
                </div>
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
                
                $polish_months = array(
                    '01' => 'stycznia', '02' => 'lutego', '03' => 'marca', '04' => 'kwietnia',
                    '05' => 'maja', '06' => 'czerwca', '07' => 'lipca', '08' => 'sierpnia',
                    '09' => 'września', '10' => 'października', '11' => 'listopada', '12' => 'grudnia'
                );
                ?>
                
                <div class="booking-calendar-grid">
                    <?php
                    $current_date = '';
                    $day_count = 0;
                    foreach ($slots as $slot):
                        $slot_date = $slot->start->format('Y-m-d');
                        
                        if ($slot_date !== $current_date):
                            if ($current_date !== '') echo '</div></div>';
                            $current_date = $slot_date;
                            $day_name_en = $slot->start->format('l');
                            $day_name_pl = isset($polish_days[$day_name_en]) ? $polish_days[$day_name_en] : $day_name_en;
                            $day_num = $slot->start->format('d');
                            $month = $polish_months[$slot->start->format('m')];
                            $day_count++;
                            ?>
                            <div class="booking-day-card">
                                <div class="booking-day-header">
                                    <span class="day-name"><?php echo esc_html($day_name_pl); ?></span>
                                    <span class="day-date"><?php echo esc_html($day_num . ' ' . $month); ?></span>
                                </div>
                                <div class="booking-time-slots">
                        <?php endif; ?>
                        
                        <button type="button" class="booking-time-slot" 
                                data-start="<?php echo esc_attr($slot->get_start_formatted()); ?>"
                                data-end="<?php echo esc_attr($slot->get_end_formatted()); ?>"
                                data-display="<?php echo esc_attr($day_name_pl . ', ' . $day_num . ' ' . $month . ' - ' . $slot->get_start_time()); ?>">
                            <?php echo esc_html($slot->get_start_time()); ?>
                        </button>
                        
                    <?php endforeach; ?>
                    </div></div>
                </div>
                
                <div class="selected-slot-display" id="selected-slot-display" style="display:none;">
                    <div class="selected-slot-content">
                        <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                        <div>
                            <strong><?php _e('Wybrany termin:', 'booking-system-df'); ?></strong>
                            <span id="selected-slot-text"></span>
                        </div>
                        <button type="button" class="change-slot-btn" id="change-slot-btn">
                            <?php _e('Zmień', 'booking-system-df'); ?>
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="booking-step" id="step-2" style="display:none;">
            <h3 class="step-title">
                <span class="step-number">2</span>
                <?php _e('Twoje dane', 'booking-system-df'); ?>
            </h3>
            
            <div class="form-grid">
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
                
                <div class="form-group form-group-full">
                    <label for="patient_notes"><?php _e('Notatki (opcjonalnie)', 'booking-system-df'); ?></label>
                    <textarea name="patient_notes" id="patient_notes" rows="4" placeholder="<?php _e('Dodatkowe informacje, które chcesz przekazać...', 'booking-system-df'); ?>"></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="button button-secondary" id="back-to-calendar">
                    <?php _e('← Wróć do kalendarza', 'booking-system-df'); ?>
                </button>
                <button type="submit" name="submit_booking" class="button button-primary">
                    <?php _e('Przejdź do płatności →', 'booking-system-df'); ?>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    let selectedSlot = null;
    
    // Handle time slot selection
    $('.booking-time-slot').on('click', function() {
        $('.booking-time-slot').removeClass('selected');
        $(this).addClass('selected');
        
        selectedSlot = {
            start: $(this).data('start'),
            end: $(this).data('end'),
            display: $(this).data('display')
        };
        
        $('#start_datetime').val(selectedSlot.start);
        $('#end_datetime').val(selectedSlot.end);
        $('#selected-slot-text').text(selectedSlot.display);
        $('#selected-slot-display').slideDown();
        
        // Auto-scroll to next step
        setTimeout(function() {
            $('#step-2').slideDown();
            $('html, body').animate({
                scrollTop: $('#step-2').offset().top - 20
            }, 500);
        }, 300);
    });
    
    // Change slot button
    $('#change-slot-btn').on('click', function() {
        $('.booking-time-slot').removeClass('selected');
        $('#selected-slot-display').slideUp();
        $('#step-2').slideUp();
        selectedSlot = null;
        $('#start_datetime').val('');
        $('#end_datetime').val('');
    });
    
    // Back to calendar button
    $('#back-to-calendar').on('click', function() {
        $('html, body').animate({
            scrollTop: $('#step-1').offset().top - 20
        }, 500);
    });
    
    // Form validation
    $('#booking-form').on('submit', function(e) {
        if (!selectedSlot) {
            e.preventDefault();
            alert('<?php _e('Proszę wybrać termin konsultacji', 'booking-system-df'); ?>');
            return false;
        }
    });
});
</script>
