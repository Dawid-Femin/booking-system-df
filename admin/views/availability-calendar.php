<?php
/**
 * Availability calendar view - 2 weeks schedule.
 */
if (!defined('ABSPATH')) exit;

// Get date range (next 2 weeks)
$timezone = new DateTimeZone('Europe/Warsaw');
$today = new DateTime('now', $timezone);
$end_date = clone $today;
$end_date->modify('+14 days');

// Get all slots for this period
$slots_data = Availability_Slot::get_by_date_range(
    $today->format('Y-m-d'),
    $end_date->format('Y-m-d')
);

// Group slots by date
$slots_by_date = array();
foreach ($slots_data as $slot) {
    if (!isset($slots_by_date[$slot->date])) {
        $slots_by_date[$slot->date] = array();
    }
    $slots_by_date[$slot->date][] = $slot;
}

$days_pl = array(
    'Monday' => 'Poniedziałek',
    'Tuesday' => 'Wtorek',
    'Wednesday' => 'Środa',
    'Thursday' => 'Czwartek',
    'Friday' => 'Piątek',
    'Saturday' => 'Sobota',
    'Sunday' => 'Niedziela'
);
?>

<div class="wrap">
    <h1><?php _e('Grafik dostępności', 'booking-system-df'); ?></h1>
    <p><?php _e('Zdefiniuj swoją dostępność na najbliższe 2 tygodnie. Możesz dodać wiele przedziałów czasowych dla każdego dnia.', 'booking-system-df'); ?></p>
    
    <form method="post" id="availability-calendar-form">
        <?php wp_nonce_field('booking_availability_calendar_form'); ?>
        
        <div id="availability-calendar">
            <?php
            $current = clone $today;
            while ($current <= $end_date):
                $date_str = $current->format('Y-m-d');
                $day_name = $days_pl[$current->format('l')];
                $date_display = $current->format('d.m.Y');
                $slots = isset($slots_by_date[$date_str]) ? $slots_by_date[$date_str] : array();
            ?>
                <div class="availability-day" data-date="<?php echo esc_attr($date_str); ?>">
                    <h3><?php echo esc_html($day_name . ' ' . $date_display); ?></h3>
                    
                    <div class="availability-slots" id="slots-<?php echo esc_attr($date_str); ?>">
                        <?php if (empty($slots)): ?>
                            <p class="no-slots"><?php _e('Brak dostępności', 'booking-system-df'); ?></p>
                        <?php else: ?>
                            <?php foreach ($slots as $slot): ?>
                                <div class="availability-slot">
                                    <input type="hidden" name="slots[<?php echo esc_attr($date_str); ?>][]" value="<?php echo esc_attr($slot->start_time . '|' . $slot->end_time); ?>">
                                    <span class="slot-time"><?php echo esc_html(substr($slot->start_time, 0, 5) . ' - ' . substr($slot->end_time, 0, 5)); ?></span>
                                    <button type="button" class="button button-small copy-slot" data-time="<?php echo esc_attr($slot->start_time . '|' . $slot->end_time); ?>"><?php _e('Kopiuj', 'booking-system-df'); ?></button>
                                    <button type="button" class="button button-small delete-slot"><?php _e('Usuń', 'booking-system-df'); ?></button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="availability-actions">
                        <button type="button" class="button add-slot-btn" data-date="<?php echo esc_attr($date_str); ?>"><?php _e('+ Dodaj przedział', 'booking-system-df'); ?></button>
                        <button type="button" class="button paste-slot-btn" data-date="<?php echo esc_attr($date_str); ?>" style="display:none;"><?php _e('Wklej', 'booking-system-df'); ?></button>
                    </div>
                    
                    <!-- Add slot form (hidden by default) -->
                    <div class="add-slot-form" style="display:none;">
                        <input type="time" class="slot-start" value="09:00">
                        <span> - </span>
                        <input type="time" class="slot-end" value="17:00">
                        <button type="button" class="button button-primary save-slot-btn"><?php _e('Dodaj', 'booking-system-df'); ?></button>
                        <button type="button" class="button cancel-slot-btn"><?php _e('Anuluj', 'booking-system-df'); ?></button>
                    </div>
                </div>
            <?php
                $current->modify('+1 day');
            endwhile;
            ?>
        </div>
        
        <p class="submit">
            <input type="submit" name="save_availability_calendar" class="button button-primary button-large" value="<?php _e('Zapisz grafik', 'booking-system-df'); ?>">
        </p>
    </form>
</div>

<style>
.availability-day {
    background: #fff;
    border: 1px solid #ccd0d4;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 4px;
}

.availability-day h3 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #e5e5e5;
}

.availability-slots {
    min-height: 40px;
    margin: 15px 0;
}

.availability-slot {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px;
    background: #f0f0f1;
    border-radius: 3px;
    margin-bottom: 8px;
}

.slot-time {
    flex: 1;
    font-weight: 500;
}

.no-slots {
    color: #646970;
    font-style: italic;
    margin: 10px 0;
}

.availability-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.add-slot-form {
    margin-top: 10px;
    padding: 10px;
    background: #f6f7f7;
    border-radius: 3px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.add-slot-form input[type="time"] {
    padding: 5px;
}
</style>

<script>
jQuery(document).ready(function($) {
    let copiedSlot = null;
    
    // Show add slot form
    $('.add-slot-btn').on('click', function() {
        $(this).closest('.availability-day').find('.add-slot-form').slideDown();
    });
    
    // Cancel add slot
    $('.cancel-slot-btn').on('click', function() {
        $(this).closest('.add-slot-form').slideUp();
    });
    
    // Save new slot
    $('.save-slot-btn').on('click', function() {
        const form = $(this).closest('.add-slot-form');
        const day = $(this).closest('.availability-day');
        const date = day.data('date');
        const startTime = form.find('.slot-start').val();
        const endTime = form.find('.slot-end').val();
        
        if (!startTime || !endTime) {
            alert('<?php _e('Wypełnij obie godziny', 'booking-system-df'); ?>');
            return;
        }
        
        if (startTime >= endTime) {
            alert('<?php _e('Godzina rozpoczęcia musi być wcześniejsza niż zakończenia', 'booking-system-df'); ?>');
            return;
        }
        
        addSlot(date, startTime, endTime);
        form.slideUp();
        form.find('.slot-start').val('09:00');
        form.find('.slot-end').val('17:00');
    });
    
    // Delete slot
    $(document).on('click', '.delete-slot', function() {
        $(this).closest('.availability-slot').remove();
        updateNoSlotsMessage();
    });
    
    // Copy slot
    $(document).on('click', '.copy-slot', function() {
        copiedSlot = $(this).data('time');
        $('.paste-slot-btn').show();
        alert('<?php _e('Przedział skopiowany! Kliknij "Wklej" przy wybranym dniu.', 'booking-system-df'); ?>');
    });
    
    // Paste slot
    $('.paste-slot-btn').on('click', function() {
        if (!copiedSlot) return;
        
        const date = $(this).data('date');
        const [startTime, endTime] = copiedSlot.split('|');
        
        addSlot(date, startTime, endTime);
    });
    
    function addSlot(date, startTime, endTime) {
        const slotsContainer = $('#slots-' + date);
        const noSlots = slotsContainer.find('.no-slots');
        
        if (noSlots.length) {
            noSlots.remove();
        }
        
        const slotHtml = `
            <div class="availability-slot">
                <input type="hidden" name="slots[${date}][]" value="${startTime}|${endTime}">
                <span class="slot-time">${startTime.substring(0,5)} - ${endTime.substring(0,5)}</span>
                <button type="button" class="button button-small copy-slot" data-time="${startTime}|${endTime}"><?php _e('Kopiuj', 'booking-system-df'); ?></button>
                <button type="button" class="button button-small delete-slot"><?php _e('Usuń', 'booking-system-df'); ?></button>
            </div>
        `;
        
        slotsContainer.append(slotHtml);
    }
    
    function updateNoSlotsMessage() {
        $('.availability-slots').each(function() {
            const slots = $(this).find('.availability-slot');
            if (slots.length === 0 && $(this).find('.no-slots').length === 0) {
                $(this).append('<p class="no-slots"><?php _e('Brak dostępności', 'booking-system-df'); ?></p>');
            }
        });
    }
});
</script>
