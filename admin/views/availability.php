<?php
/**
 * Availability rules view.
 */
if (!defined('ABSPATH')) exit;

$rules = Availability_Rule::get_all_active();
$blocked = Blocked_Period::get_all();
?>

<div class="wrap">
    <h1><?php _e('Dostępność', 'booking-system-df'); ?></h1>
    
    <h2><?php _e('Reguły dostępności', 'booking-system-df'); ?></h2>
    <form method="post">
        <?php wp_nonce_field('booking_availability_form'); ?>
        <table class="form-table">
            <tr>
                <th><label><?php _e('Dzień tygodnia', 'booking-system-df'); ?></label></th>
                <td>
                    <select name="day_of_week" required>
                        <option value="1"><?php _e('Poniedziałek', 'booking-system-df'); ?></option>
                        <option value="2"><?php _e('Wtorek', 'booking-system-df'); ?></option>
                        <option value="3"><?php _e('Środa', 'booking-system-df'); ?></option>
                        <option value="4"><?php _e('Czwartek', 'booking-system-df'); ?></option>
                        <option value="5"><?php _e('Piątek', 'booking-system-df'); ?></option>
                        <option value="6"><?php _e('Sobota', 'booking-system-df'); ?></option>
                        <option value="7"><?php _e('Niedziela', 'booking-system-df'); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label><?php _e('Od godziny', 'booking-system-df'); ?></label></th>
                <td><input type="time" name="start_time" required></td>
            </tr>
            <tr>
                <th><label><?php _e('Do godziny', 'booking-system-df'); ?></label></th>
                <td><input type="time" name="end_time" required></td>
            </tr>
            <tr>
                <th><label><?php _e('Aktywna', 'booking-system-df'); ?></label></th>
                <td><input type="checkbox" name="is_active" value="1" checked></td>
            </tr>
        </table>
        <p><input type="submit" name="save_rule" class="button button-primary" value="<?php _e('Dodaj regułę', 'booking-system-df'); ?>"></p>
    </form>
    
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Dzień', 'booking-system-df'); ?></th>
                <th><?php _e('Godziny', 'booking-system-df'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rules)): ?>
                <tr>
                    <td colspan="2"><?php _e('Brak reguł dostępności. Dodaj pierwszą regułę powyżej.', 'booking-system-df'); ?></td>
                </tr>
            <?php else: ?>
                <?php 
                $days = array(
                    1 => __('Poniedziałek', 'booking-system-df'),
                    2 => __('Wtorek', 'booking-system-df'),
                    3 => __('Środa', 'booking-system-df'),
                    4 => __('Czwartek', 'booking-system-df'),
                    5 => __('Piątek', 'booking-system-df'),
                    6 => __('Sobota', 'booking-system-df'),
                    7 => __('Niedziela', 'booking-system-df')
                );
                foreach ($rules as $rule): ?>
                    <tr>
                        <td><?php echo esc_html($days[$rule->day_of_week]); ?></td>
                        <td><?php echo esc_html($rule->start_time . ' - ' . $rule->end_time); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
