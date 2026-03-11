<?php
/**
 * Booking confirmation view.
 */
if (!defined('ABSPATH')) exit;

$type = Consultation_Type::get_by_id($consultation->consultation_type_id);

$polish_months = array(
    '01' => 'stycznia', '02' => 'lutego', '03' => 'marca', '04' => 'kwietnia',
    '05' => 'maja', '06' => 'czerwca', '07' => 'lipca', '08' => 'sierpnia',
    '09' => 'września', '10' => 'października', '11' => 'listopada', '12' => 'grudnia'
);

$polish_days_full = array(
    'Monday' => 'Poniedziałek', 'Tuesday' => 'Wtorek', 'Wednesday' => 'Środa',
    'Thursday' => 'Czwartek', 'Friday' => 'Piątek', 'Saturday' => 'Sobota', 'Sunday' => 'Niedziela'
);

$dt = new DateTime($consultation->start_datetime);
$day_name = $polish_days_full[$dt->format('l')] ?? $dt->format('l');
$day_num = $dt->format('d');
$month = $polish_months[$dt->format('m')] ?? $dt->format('m');
$year = $dt->format('Y');
$time = $dt->format('H:i');
$formatted_date = $day_name . ', ' . $day_num . ' ' . $month . ' ' . $year;
?>

<div class="booking-confirmation-container">

    <?php if ($booking_status === 'success'): ?>

        <div class="confirmation-header confirmation-header--success">
            <div class="confirmation-icon">
                <svg width="48" height="48" viewBox="0 0 16 16" fill="#7fb069">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
            </div>
            <h2><?php _e('Rezerwacja potwierdzona', 'booking-system-df'); ?></h2>
            <p class="confirmation-subtitle"><?php _e('Dziękujemy za dokonanie rezerwacji', 'booking-system-df'); ?></p>
        </div>

        <?php if ($dev_mode === '1'): ?>
            <div class="confirmation-card confirmation-card--dev">
                <div class="confirmation-card__icon">🔧</div>
                <div class="confirmation-card__content">
                    <strong><?php _e('Tryb deweloperski', 'booking-system-df'); ?></strong>
                    <p><?php _e('Płatność została automatycznie zaakceptowana.', 'booking-system-df'); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <div class="confirmation-card">
            <h3 class="confirmation-card__title"><?php _e('Szczegóły rezerwacji', 'booking-system-df'); ?></h3>
            <div class="confirmation-details-grid">
                <div class="confirmation-detail">
                    <span class="confirmation-detail__label"><?php _e('Konsultacja', 'booking-system-df'); ?></span>
                    <span class="confirmation-detail__value"><?php echo esc_html($type ? $type->name : 'N/A'); ?></span>
                </div>
                <div class="confirmation-detail">
                    <span class="confirmation-detail__label"><?php _e('Data', 'booking-system-df'); ?></span>
                    <span class="confirmation-detail__value"><?php echo esc_html($formatted_date); ?></span>
                </div>
                <div class="confirmation-detail">
                    <span class="confirmation-detail__label"><?php _e('Godzina', 'booking-system-df'); ?></span>
                    <span class="confirmation-detail__value"><?php echo esc_html($time); ?></span>
                </div>
                <div class="confirmation-detail">
                    <span class="confirmation-detail__label"><?php _e('Czas trwania', 'booking-system-df'); ?></span>
                    <span class="confirmation-detail__value"><?php echo esc_html($type ? $type->duration_minutes . ' min' : 'N/A'); ?></span>
                </div>
                <div class="confirmation-detail">
                    <span class="confirmation-detail__label"><?php _e('Kwota', 'booking-system-df'); ?></span>
                    <span class="confirmation-detail__value confirmation-detail__value--price"><?php echo esc_html($consultation->payment_data->amount . ' ' . $consultation->payment_data->currency); ?></span>
                </div>
                <div class="confirmation-detail">
                    <span class="confirmation-detail__label"><?php _e('Numer rezerwacji', 'booking-system-df'); ?></span>
                    <span class="confirmation-detail__value">#<?php echo esc_html($consultation->id); ?></span>
                </div>
            </div>
        </div>

        <div class="confirmation-card">
            <h3 class="confirmation-card__title"><?php _e('Co dalej?', 'booking-system-df'); ?></h3>
            <div class="confirmation-steps">
                <div class="confirmation-step">
                    <div class="confirmation-step__number">1</div>
                    <p><?php _e('Psycholog otrzymał powiadomienie o Twojej rezerwacji.', 'booking-system-df'); ?></p>
                </div>
                <div class="confirmation-step">
                    <div class="confirmation-step__number">2</div>
                    <p><?php _e('Po zatwierdzeniu otrzymasz email z linkiem do spotkania Google Meet.', 'booking-system-df'); ?></p>
                </div>
                <div class="confirmation-step">
                    <div class="confirmation-step__number">3</div>
                    <p><?php _e('Sprawdź swoją skrzynkę email (również folder SPAM).', 'booking-system-df'); ?></p>
                </div>
            </div>
        </div>

        <div class="confirmation-action">
            <a href="<?php echo home_url(); ?>" class="confirmation-btn">
                <?php _e('Powrót do strony głównej', 'booking-system-df'); ?>
            </a>
        </div>

    <?php elseif ($booking_status === 'pending'): ?>

        <div class="confirmation-header confirmation-header--pending">
            <div class="confirmation-icon">
                <svg width="48" height="48" viewBox="0 0 16 16" fill="#d4a843">
                    <path d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0zM7 4v4.5l3.5 2 .5-.9-3-1.7V4H7z"/>
                </svg>
            </div>
            <h2><?php _e('Oczekiwanie na płatność', 'booking-system-df'); ?></h2>
            <p class="confirmation-subtitle"><?php _e('Twoja płatność jest w trakcie przetwarzania', 'booking-system-df'); ?></p>
        </div>

        <div class="confirmation-card">
            <h3 class="confirmation-card__title"><?php _e('Szczegóły rezerwacji', 'booking-system-df'); ?></h3>
            <div class="confirmation-details-grid">
                <div class="confirmation-detail">
                    <span class="confirmation-detail__label"><?php _e('Konsultacja', 'booking-system-df'); ?></span>
                    <span class="confirmation-detail__value"><?php echo esc_html($type ? $type->name : 'N/A'); ?></span>
                </div>
                <div class="confirmation-detail">
                    <span class="confirmation-detail__label"><?php _e('Data', 'booking-system-df'); ?></span>
                    <span class="confirmation-detail__value"><?php echo esc_html($formatted_date); ?></span>
                </div>
                <div class="confirmation-detail">
                    <span class="confirmation-detail__label"><?php _e('Godzina', 'booking-system-df'); ?></span>
                    <span class="confirmation-detail__value"><?php echo esc_html($time); ?></span>
                </div>
                <div class="confirmation-detail">
                    <span class="confirmation-detail__label"><?php _e('Numer rezerwacji', 'booking-system-df'); ?></span>
                    <span class="confirmation-detail__value">#<?php echo esc_html($consultation->id); ?></span>
                </div>
            </div>
        </div>

        <div class="confirmation-card confirmation-card--info">
            <p><?php _e('Otrzymasz email z potwierdzeniem, gdy płatność zostanie zrealizowana.', 'booking-system-df'); ?></p>
        </div>

        <div class="confirmation-action">
            <a href="<?php echo home_url(); ?>" class="confirmation-btn">
                <?php _e('Powrót do strony głównej', 'booking-system-df'); ?>
            </a>
        </div>

    <?php else: ?>

        <div class="confirmation-header confirmation-header--error">
            <div class="confirmation-icon">
                <svg width="48" height="48" viewBox="0 0 16 16" fill="#d45050">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                </svg>
            </div>
            <h2><?php _e('Wystąpił problem', 'booking-system-df'); ?></h2>
            <p class="confirmation-subtitle"><?php _e('Nie udało się przetworzyć Twojej rezerwacji', 'booking-system-df'); ?></p>
        </div>

        <div class="confirmation-card confirmation-card--info">
            <p><?php _e('Skontaktuj się z nami, jeśli problem będzie się powtarzał.', 'booking-system-df'); ?></p>
        </div>

        <div class="confirmation-action">
            <a href="<?php echo home_url(); ?>" class="confirmation-btn">
                <?php _e('Powrót do strony głównej', 'booking-system-df'); ?>
            </a>
        </div>

    <?php endif; ?>

</div>
