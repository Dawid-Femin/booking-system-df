<?php
/**
 * Edit consultation view.
 */
if (!defined('ABSPATH')) exit;

$consultation_id = intval($_GET['id']);
$consultation = Consultation::get_by_id($consultation_id);

if (!$consultation) {
    echo '<div class="notice notice-error"><p>' . __('Nie znaleziono konsultacji.', 'booking-system-df') . '</p></div>';
    return;
}

$types = Consultation_Type::get_all_active();
$start = new DateTime($consultation->start_datetime, new DateTimeZone('Europe/Warsaw'));
$end = new DateTime($consultation->end_datetime, new DateTimeZone('Europe/Warsaw'));
?>

<div class="wrap">
    <h1><?php printf(__('Edytuj konsultację #%d', 'booking-system-df'), $consultation->id); ?></h1>
    <a href="?page=booking-consultations" class="page-title-action"><?php _e('← Wróć do listy', 'booking-system-df'); ?></a>

    <form method="post" action="?page=booking-consultations&action=edit&id=<?php echo $consultation->id; ?>">
        <?php wp_nonce_field('booking_edit_consultation_' . $consultation->id); ?>

        <table class="form-table">
            <tr>
                <th><?php _e('Typ konsultacji', 'booking-system-df'); ?></th>
                <td>
                    <select name="consultation_type_id">
                        <?php foreach ($types as $type): ?>
                            <option value="<?php echo $type->id; ?>" <?php selected($consultation->consultation_type_id, $type->id); ?>>
                                <?php echo esc_html($type->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e('Data i godzina rozpoczęcia', 'booking-system-df'); ?></th>
                <td>
                    <input type="datetime-local" name="start_datetime"
                        value="<?php echo $start->format('Y-m-d\TH:i'); ?>" required>
                </td>
            </tr>
            <tr>
                <th><?php _e('Data i godzina zakończenia', 'booking-system-df'); ?></th>
                <td>
                    <input type="datetime-local" name="end_datetime"
                        value="<?php echo $end->format('Y-m-d\TH:i'); ?>" required>
                </td>
            </tr>
            <tr>
                <th><?php _e('Status', 'booking-system-df'); ?></th>
                <td>
                    <select name="status">
                        <?php foreach (ConsultationStatus::get_all() as $s): ?>
                            <option value="<?php echo $s; ?>" <?php selected($consultation->status, $s); ?>>
                                <?php echo esc_html(ConsultationStatus::get_label($s)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php _e('Imię i nazwisko pacjenta', 'booking-system-df'); ?></th>
                <td><input type="text" name="patient_name" value="<?php echo esc_attr($consultation->patient_data->name); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><?php _e('Email pacjenta', 'booking-system-df'); ?></th>
                <td><input type="email" name="patient_email" value="<?php echo esc_attr($consultation->patient_data->email); ?>" class="regular-text" required></td>
            </tr>
            <tr>
                <th><?php _e('Telefon pacjenta', 'booking-system-df'); ?></th>
                <td><input type="text" name="patient_phone" value="<?php echo esc_attr($consultation->patient_data->phone); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><?php _e('Notatki', 'booking-system-df'); ?></th>
                <td><textarea name="patient_notes" rows="4" class="large-text"><?php echo esc_textarea($consultation->patient_data->notes); ?></textarea></td>
            </tr>
        </table>

        <?php if ($consultation->google_meet_link): ?>
            <p><strong><?php _e('Google Meet:', 'booking-system-df'); ?></strong>
                <a href="<?php echo esc_url($consultation->google_meet_link); ?>" target="_blank"><?php echo esc_html($consultation->google_meet_link); ?></a>
            </p>
            <p class="description"><?php _e('Jeśli zmienisz datę/godzinę, event w Google Calendar zostanie zaktualizowany i pacjent otrzyma email z nowym terminem.', 'booking-system-df'); ?></p>
        <?php endif; ?>

        <p class="submit">
            <input type="submit" name="save_consultation" class="button button-primary" value="<?php _e('Zapisz zmiany', 'booking-system-df'); ?>">
        </p>
    </form>
</div>
