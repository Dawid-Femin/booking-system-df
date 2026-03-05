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
            <?php foreach ($rules as $rule): ?>
                <tr>
                    <td><?php echo esc_html($rule->day_of_week); ?></td>
                    <td><?php echo esc_html($rule->start_time . ' - ' . $rule->end_time); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
