<?php
/**
 * Notification system for sending emails.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Notification_System {

    public static function send_payment_confirmation($consultation) {
        $subject = __('Potwierdzenie płatności - Konsultacja psychologiczna', 'booking-system-df');
        
        $type = Consultation_Type::get_by_id($consultation->consultation_type_id);
        $start = new DateTime($consultation->start_datetime, new DateTimeZone('Europe/Warsaw'));
        
        $message = sprintf(
            __("Dzień dobry %s,\n\nPotwierdzamy otrzymanie płatności za konsultację psychologiczną.\n\nSzczegóły:\n- Typ: %s\n- Data: %s\n- Godzina: %s\n- Kwota: %.2f %s\n\nKonsultacja oczekuje na potwierdzenie przez psychologa. Otrzymasz kolejny email z linkiem do spotkania Google Meet po zatwierdzeniu.\n\nPozdrawiamy,\nZespół", 'booking-system-df'),
            $consultation->patient_data->name,
            $type->name,
            $start->format('d.m.Y'),
            $start->format('H:i'),
            $consultation->payment_data->amount,
            $consultation->payment_data->currency
        );
        
        return wp_mail($consultation->patient_data->email, $subject, $message);
    }

    public static function send_consultation_confirmed($consultation) {
        $subject = __('Konsultacja potwierdzona - Link do spotkania', 'booking-system-df');
        
        $type = Consultation_Type::get_by_id($consultation->consultation_type_id);
        $start = new DateTime($consultation->start_datetime, new DateTimeZone('Europe/Warsaw'));
        
        $message = sprintf(
            __("Dzień dobry %s,\n\nTwoja konsultacja została potwierdzona!\n\nSzczegóły:\n- Typ: %s\n- Data: %s\n- Godzina: %s\n\nLink do spotkania Google Meet:\n%s\n\nProsimy dołączyć na 5 minut przed rozpoczęciem.\n\nPozdrawiamy,\nZespół", 'booking-system-df'),
            $consultation->patient_data->name,
            $type->name,
            $start->format('d.m.Y'),
            $start->format('H:i'),
            $consultation->google_meet_link
        );
        
        // Attach ICS file
        $ics_file = self::generate_ics_file($consultation);
        $attachments = array($ics_file);
        
        $result = wp_mail($consultation->patient_data->email, $subject, $message, '', $attachments);
        
        // Clean up ICS file
        if (file_exists($ics_file)) {
            unlink($ics_file);
        }
        
        return $result;
    }

    public static function send_reminder_24h($consultation) {
        $subject = __('Przypomnienie: Konsultacja za 24 godziny', 'booking-system-df');
        
        $type = Consultation_Type::get_by_id($consultation->consultation_type_id);
        $start = new DateTime($consultation->start_datetime, new DateTimeZone('Europe/Warsaw'));
        
        $message = sprintf(
            __("Dzień dobry %s,\n\nPrzypominamy o jutrzejszej konsultacji:\n\n- Typ: %s\n- Data: %s\n- Godzina: %s\n\nLink do spotkania:\n%s\n\nDo zobaczenia!\n\nPozdrawiamy,\nZespół", 'booking-system-df'),
            $consultation->patient_data->name,
            $type->name,
            $start->format('d.m.Y'),
            $start->format('H:i'),
            $consultation->google_meet_link
        );
        
        return wp_mail($consultation->patient_data->email, $subject, $message);
    }

    public static function send_reminder_1h($consultation) {
        $subject = __('Przypomnienie: Konsultacja za godzinę', 'booking-system-df');
        
        $type = Consultation_Type::get_by_id($consultation->consultation_type_id);
        $start = new DateTime($consultation->start_datetime, new DateTimeZone('Europe/Warsaw'));
        
        $message = sprintf(
            __("Dzień dobry %s,\n\nTwoja konsultacja rozpocznie się za godzinę!\n\n- Godzina: %s\n\nLink do spotkania:\n%s\n\nProsimy dołączyć na 5 minut przed rozpoczęciem.\n\nDo zobaczenia!\n\nPozdrawiamy,\nZespół", 'booking-system-df'),
            $consultation->patient_data->name,
            $start->format('H:i'),
            $consultation->google_meet_link
        );
        
        return wp_mail($consultation->patient_data->email, $subject, $message);
    }

    public static function send_cancellation_notice($consultation) {
        $subject = __('Anulowanie konsultacji', 'booking-system-df');
        
        $type = Consultation_Type::get_by_id($consultation->consultation_type_id);
        $start = new DateTime($consultation->start_datetime, new DateTimeZone('Europe/Warsaw'));
        
        $message = sprintf(
            __("Dzień dobry %s,\n\nInformujemy, że konsultacja została anulowana:\n\n- Typ: %s\n- Data: %s\n- Godzina: %s\n\nPowód: %s\n\nJeśli płatność została zrealizowana, zwrot środków zostanie przetworzony w ciągu 5-7 dni roboczych.\n\nPozdrawiamy,\nZespół", 'booking-system-df'),
            $consultation->patient_data->name,
            $type->name,
            $start->format('d.m.Y'),
            $start->format('H:i'),
            $consultation->cancellation_reason ?: __('Brak podanego powodu', 'booking-system-df')
        );
        
        return wp_mail($consultation->patient_data->email, $subject, $message);
    }

    public static function send_admin_new_consultation($consultation) {
        $admin_email = get_option('admin_email');
        $subject = __('Nowa konsultacja oczekuje na potwierdzenie', 'booking-system-df');
        
        $type = Consultation_Type::get_by_id($consultation->consultation_type_id);
        $start = new DateTime($consultation->start_datetime, new DateTimeZone('Europe/Warsaw'));
        
        $confirm_url = admin_url('admin.php?page=booking-consultations&action=confirm&id=' . $consultation->id);
        
        $message = sprintf(
            __("Nowa konsultacja oczekuje na potwierdzenie:\n\n- Pacjent: %s\n- Email: %s\n- Telefon: %s\n- Typ: %s\n- Data: %s\n- Godzina: %s\n- Kwota: %.2f %s\n\nNotatki pacjenta:\n%s\n\nPotwierdź konsultację:\n%s\n\nPo potwierdzeniu zostanie automatycznie utworzone spotkanie Google Meet.", 'booking-system-df'),
            $consultation->patient_data->name,
            $consultation->patient_data->email,
            $consultation->patient_data->phone,
            $type->name,
            $start->format('d.m.Y'),
            $start->format('H:i'),
            $consultation->payment_data->amount,
            $consultation->payment_data->currency,
            $consultation->patient_data->notes ?: __('Brak', 'booking-system-df'),
            $confirm_url
        );
        
        return wp_mail($admin_email, $subject, $message);
    }

    private static function generate_ics_file($consultation) {
        $type = Consultation_Type::get_by_id($consultation->consultation_type_id);
        $start = new DateTime($consultation->start_datetime, new DateTimeZone('Europe/Warsaw'));
        $end = new DateTime($consultation->end_datetime, new DateTimeZone('Europe/Warsaw'));
        
        $ics_content = "BEGIN:VCALENDAR\r\n";
        $ics_content .= "VERSION:2.0\r\n";
        $ics_content .= "PRODID:-//Booking System DF//PL\r\n";
        $ics_content .= "BEGIN:VEVENT\r\n";
        $ics_content .= "UID:" . $consultation->id . "@booking-system-df\r\n";
        $ics_content .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
        $ics_content .= "DTSTART:" . $start->format('Ymd\THis') . "\r\n";
        $ics_content .= "DTEND:" . $end->format('Ymd\THis') . "\r\n";
        $ics_content .= "SUMMARY:" . $type->name . "\r\n";
        $ics_content .= "DESCRIPTION:Link do spotkania: " . $consultation->google_meet_link . "\r\n";
        $ics_content .= "LOCATION:" . $consultation->google_meet_link . "\r\n";
        $ics_content .= "STATUS:CONFIRMED\r\n";
        $ics_content .= "BEGIN:VALARM\r\n";
        $ics_content .= "TRIGGER:-PT1H\r\n";
        $ics_content .= "ACTION:DISPLAY\r\n";
        $ics_content .= "DESCRIPTION:Przypomnienie o konsultacji\r\n";
        $ics_content .= "END:VALARM\r\n";
        $ics_content .= "END:VEVENT\r\n";
        $ics_content .= "END:VCALENDAR\r\n";
        
        $upload_dir = wp_upload_dir();
        $ics_file = $upload_dir['basedir'] . '/consultation_' . $consultation->id . '.ics';
        
        file_put_contents($ics_file, $ics_content);
        
        return $ics_file;
    }
}
