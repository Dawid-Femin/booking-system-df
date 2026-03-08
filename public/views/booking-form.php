<?php
/**
 * Booking form view - compact interactive calendar.
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
                <?php echo esc_html($type->price . ' ' . $type->currency); ?>
            </span>
        </div>
    </div>
    
    <form method="post" class="booking-form" id="booking-form">
        <?php wp_nonce_field('booking_form', 'booking_nonce'); ?>
        <input type="hidden" name="type_id" value="<?php echo esc_attr($type_id); ?>">
        <input type="hidden" name="start_datetime" id="start_datetime" required>
        <input type="hidden" name="end_datetime" id="end_datetime" required>
        
        <?php
        // Generate available slots
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime('+30 days'));
        
        $slots_result = Availability_Manager::get_available_slots($type_id, $start_date, $end_date);
        $slots = $slots_result->is_success() ? $slots_result->get_data() : array();
        
        // Group slots by date
        $slots_by_date = array();
        foreach ($slots as $slot) {
            $date = $slot->start->format('Y-m-d');
            if (!isset($slots_by_date[$date])) {
                $slots_by_date[$date] = array();
            }
            $slots_by_date[$date][] = $slot;
        }
        
        $polish_days = array(
            'Monday' => 'Pon', 'Tuesday' => 'Wt', 'Wednesday' => 'Śr',
            'Thursday' => 'Czw', 'Friday' => 'Pt', 'Saturday' => 'Sob', 'Sunday' => 'Nie'
        );
        
        $polish_days_full = array(
            'Monday' => 'Poniedziałek', 'Tuesday' => 'Wtorek', 'Wednesday' => 'Środa',
            'Thursday' => 'Czwartek', 'Friday' => 'Piątek', 'Saturday' => 'Sobota', 'Sunday' => 'Niedziela'
        );
        
        $polish_months = array(
            '01' => 'stycznia', '02' => 'lutego', '03' => 'marca', '04' => 'kwietnia',
            '05' => 'maja', '06' => 'czerwca', '07' => 'lipca', '08' => 'sierpnia',
            '09' => 'września', '10' => 'października', '11' => 'listopada', '12' => 'grudnia'
        );
        ?>
        
        <?php if (empty($slots)): ?>
            <div class="booking-no-slots">
                <p><?php _e('Brak dostępnych terminów w ciągu najbliższych 30 dni.', 'booking-system-df'); ?></p>
                <p><?php _e('Skontaktuj się z nami bezpośrednio.', 'booking-system-df'); ?></p>
            </div>
        <?php else: ?>
            <div class="booking-step active" id="step-1">
                <h3 class="step-title"><?php _e('Wybierz dzień', 'booking-system-df'); ?></h3>
                
                <div class="booking-mini-calendar">
                    <?php
                    $current_date = new DateTime($start_date);
                    $end_date_obj = new DateTime($end_date);
                    $week_count = 0;
                    
                    while ($current_date <= $end_date_obj && $week_count < 2):
                        $week_start = clone $current_date;
                        ?>
                        <div class="calendar-week">
                            <?php
                            for ($i = 0; $i < 7 && $current_date <= $end_date_obj; $i++):
                                $date_str = $current_date->format('Y-m-d');
                                $has_slots = isset($slots_by_date[$date_str]);
                                $day_name = $polish_days[$current_date->format('l')];
                                $day_name_full = $polish_days_full[$current_date->format('l')];
                                $day_num = $current_date->format('d');
                                $month = $polish_months[$current_date->format('m')];
                                $full_date_label = $day_name_full . ', ' . $day_num . ' ' . $month;
                                ?>
                                <button type="button" 
                                        class="calendar-day <?php echo $has_slots ? 'has-slots' : 'no-slots'; ?>"
                                        data-date="<?php echo esc_attr($date_str); ?>"
                                        data-full-date="<?php echo esc_attr($full_date_label); ?>"
                                        <?php echo !$has_slots ? 'disabled' : ''; ?>>
                                    <span class="day-name"><?php echo esc_html($day_name); ?></span>
                                    <span class="day-num"><?php echo esc_html($day_num); ?></span>
                                </button>
                                <?php
                                $current_date->modify('+1 day');
                            endfor;
                            ?>
                        </div>
                        <?php
                        $week_count++;
                    endwhile;
                    ?>
                </div>
            </div>
            
            <div class="booking-step" id="step-2" style="display:none;">
                <button type="button" class="back-link" id="back-to-calendar">
                    ← <?php _e('Zmień dzień', 'booking-system-df'); ?>
                </button>
                
                <h3 class="step-title" id="selected-day-title"></h3>
                
                <div class="booking-time-grid" id="time-slots-container">
                    <?php foreach ($slots_by_date as $date => $day_slots): ?>
                        <div class="time-slots-for-date" data-date="<?php echo esc_attr($date); ?>" style="display:none;">
                            <?php foreach ($day_slots as $slot): 
                                $day_name_full = $polish_days_full[$slot->start->format('l')];
                                $day_num = $slot->start->format('d');
                                $month = $polish_months[$slot->start->format('m')];
                                ?>
                                <button type="button" class="time-slot-btn" 
                                        data-start="<?php echo esc_attr($slot->get_start_formatted()); ?>"
                                        data-end="<?php echo esc_attr($slot->get_end_formatted()); ?>"
                                        data-display="<?php echo esc_attr($day_name_full . ', ' . $day_num . ' ' . $month . ' - ' . $slot->get_start_time()); ?>">
                                    <?php echo esc_html($slot->get_start_time()); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="booking-step" id="step-3" style="display:none;">
                <div class="selected-summary">
                    <svg width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                    <div>
                        <strong><?php _e('Wybrany termin:', 'booking-system-df'); ?></strong>
                        <span id="selected-slot-text"></span>
                    </div>
                    <button type="button" class="change-btn" id="change-slot-btn">
                        <?php _e('Zmień', 'booking-system-df'); ?>
                    </button>
                </div>
                
                <h3 class="step-title"><?php _e('Twoje dane', 'booking-system-df'); ?></h3>
                
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
                        <textarea name="patient_notes" id="patient_notes" rows="4" placeholder="<?php _e('Dodatkowe informacje...', 'booking-system-df'); ?>"></textarea>
                    </div>
                </div>
                
                <button type="submit" name="submit_booking" class="button-primary">
                    <?php _e('Umów konsultację', 'booking-system-df'); ?>
                </button>
            </div>
        <?php endif; ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    let selectedSlot = null;
    
    // Select day from calendar
    $('.calendar-day.has-slots').on('click', function() {
        const date = $(this).data('date');
        const fullDate = $(this).data('full-date');
        
        $('.calendar-day').removeClass('selected');
        $(this).addClass('selected');
        
        // Show time slots for selected date
        $('.time-slots-for-date').hide();
        $(`.time-slots-for-date[data-date="${date}"]`).show();
        
        // Update title with full date
        $('#selected-day-title').text(fullDate);
        
        // Show step 2
        $('#step-1').removeClass('active');
        $('#step-2').slideDown();
        
        $('html, body').animate({
            scrollTop: $('#step-2').offset().top - 20
        }, 300);
    });
    
    // Back to calendar
    $('#back-to-calendar').on('click', function() {
        $('#step-2').slideUp();
        $('#step-1').addClass('active');
        $('.calendar-day').removeClass('selected');
    });
    
    // Select time slot
    $(document).on('click', '.time-slot-btn', function() {
        $('.time-slot-btn').removeClass('selected');
        $(this).addClass('selected');
        
        selectedSlot = {
            start: $(this).data('start'),
            end: $(this).data('end'),
            display: $(this).data('display')
        };
        
        $('#start_datetime').val(selectedSlot.start);
        $('#end_datetime').val(selectedSlot.end);
        $('#selected-slot-text').text(selectedSlot.display);
        
        // Hide step 1 and step 2, show step 3
        $('#step-1').slideUp(400);
        $('#step-2').slideUp(400, function() {
            // After hiding steps 1 and 2, show step 3
            $('#step-3').slideDown(400, function() {
                // Scroll to selected-summary box after all animations complete
                $('html, body').animate({
                    scrollTop: $('.selected-summary').offset().top - 20
                }, 300);
            });
        });
    });
    
    // Change slot
    $('#change-slot-btn').on('click', function() {
        $('#step-3').slideUp();
        $('#step-1').slideDown().addClass('active');
        $('#step-2').hide();
        $('.calendar-day').removeClass('selected');
        $('.time-slot-btn').removeClass('selected');
        selectedSlot = null;
        $('#start_datetime').val('');
        $('#end_datetime').val('');
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
