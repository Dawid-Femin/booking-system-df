<?php
/**
 * Calendar widget view.
 */
if (!defined('ABSPATH')) exit;

$start_date = date('Y-m-d');
$end_date = date('Y-m-d', strtotime('+30 days'));

$slots_result = Availability_Manager::get_available_slots($type_id, $start_date, $end_date);
$slots = $slots_result->is_success() ? $slots_result->get_data() : array();
?>

<div class="booking-calendar">
    <h3><?php echo esc_html($type->name); ?></h3>
    <p><?php _e('Dostępne terminy w ciągu najbliższych 30 dni:', 'booking-system-df'); ?></p>
    
    <?php if (empty($slots)): ?>
        <p><?php _e('Brak dostępnych terminów.', 'booking-system-df'); ?></p>
    <?php else: ?>
        <div class="booking-slots">
            <?php
            $current_date = '';
            foreach ($slots as $slot):
                $slot_date = $slot->start->format('Y-m-d');
                
                if ($slot_date !== $current_date):
                    if ($current_date !== '') echo '</div>';
                    $current_date = $slot_date;
                    ?>
                    <div class="booking-day">
                        <h4><?php echo esc_html($slot->start->format('l, d.m.Y')); ?></h4>
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
