<?php
/**
 * Google Meet integration for creating video meetings.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Google_Meet_Integration {

    private $client_id;
    private $client_secret;
    private $refresh_token;
    private $access_token;
    private $token_expires_at;

    public function __construct() {
        $this->load_settings();
    }

    private function load_settings() {
        global $wpdb;
        $table = $wpdb->prefix . 'booking_settings';
        
        $settings = $wpdb->get_results(
            "SELECT setting_key, setting_value FROM $table WHERE setting_key LIKE 'google_%'"
        );
        
        foreach ($settings as $setting) {
            $value = $setting->setting_value;
            
            switch ($setting->setting_key) {
                case 'google_client_id':
                    $this->client_id = Encryption_Helper::decrypt($value);
                    break;
                case 'google_client_secret':
                    $this->client_secret = Encryption_Helper::decrypt($value);
                    break;
                case 'google_refresh_token':
                    $this->refresh_token = Encryption_Helper::decrypt($value);
                    break;
            }
        }
    }

    private function get_access_token() {
        if ($this->access_token && $this->token_expires_at > time()) {
            return $this->access_token;
        }

        $url = 'https://oauth2.googleapis.com/token';
        
        $response = wp_remote_post($url, array(
            'body' => array(
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'refresh_token' => $this->refresh_token,
                'grant_type' => 'refresh_token'
            ),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            Booking_System_Logger::log_error('Google OAuth failed: ' . $response->get_error_message());
            throw new Booking_API_Error('Nie udało się połączyć z Google API.');
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!isset($body['access_token'])) {
            Booking_System_Logger::log_error('Google OAuth response invalid', array('response' => $body));
            throw new Booking_API_Error('Nie udało się uzyskać tokenu dostępu Google.');
        }

        $this->access_token = $body['access_token'];
        $this->token_expires_at = time() + $body['expires_in'] - 60;
        
        return $this->access_token;
    }

    public function create_meeting($consultation) {
        $max_retries = 3;
        $retry_count = 0;
        
        while ($retry_count < $max_retries) {
            try {
                return $this->attempt_create_meeting($consultation);
            } catch (Exception $e) {
                $retry_count++;
                
                if ($retry_count >= $max_retries) {
                    Booking_System_Logger::log_error(
                        'Google Meet creation failed after ' . $max_retries . ' attempts: ' . $e->getMessage(),
                        array('consultation_id' => $consultation->id, 'critical' => true)
                    );
                    return Result::failure($e->getMessage());
                }
                
                Booking_System_Logger::log_warning(
                    'Google Meet creation attempt ' . $retry_count . ' failed, retrying...',
                    array('consultation_id' => $consultation->id)
                );
                
                sleep(2 * $retry_count);
            }
        }
    }

    private function attempt_create_meeting($consultation) {
        $token = $this->get_access_token();
        
        $type = Consultation_Type::get_by_id($consultation->consultation_type_id);
        
        $start = new DateTime($consultation->start_datetime, new DateTimeZone('Europe/Warsaw'));
        $end = new DateTime($consultation->end_datetime, new DateTimeZone('Europe/Warsaw'));
        
        $event_data = array(
            'summary' => $type->name . ' - ' . $consultation->patient_data->name,
            'description' => 'Konsultacja psychologiczna',
            'start' => array(
                'dateTime' => $start->format('c'),
                'timeZone' => 'Europe/Warsaw'
            ),
            'end' => array(
                'dateTime' => $end->format('c'),
                'timeZone' => 'Europe/Warsaw'
            ),
            'attendees' => array(
                array('email' => $consultation->patient_data->email)
            ),
            'conferenceData' => array(
                'createRequest' => array(
                    'requestId' => 'consultation_' . $consultation->id . '_' . time(),
                    'conferenceSolutionKey' => array(
                        'type' => 'hangoutsMeet'
                    )
                )
            ),
            'reminders' => array(
                'useDefault' => false,
                'overrides' => array(
                    array('method' => 'email', 'minutes' => 1440), // 24h
                    array('method' => 'email', 'minutes' => 60)    // 1h
                )
            )
        );

        $url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events?conferenceDataVersion=1';
        
        $response = wp_remote_post($url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($event_data),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            throw new Booking_API_Error('Nie udało się utworzyć spotkania Google Meet: ' . $response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!isset($body['id'])) {
            Booking_System_Logger::log_error('Google Calendar event creation failed', array('response' => $body));
            throw new Booking_API_Error('Nieprawidłowa odpowiedź z Google Calendar API.');
        }

        $meet_link = isset($body['conferenceData']['entryPoints'][0]['uri']) 
            ? $body['conferenceData']['entryPoints'][0]['uri'] 
            : null;

        if (!$meet_link) {
            Booking_System_Logger::log_error('Google Meet link not found in response', array('response' => $body));
            throw new Booking_API_Error('Nie udało się uzyskać linku do spotkania Google Meet.');
        }

        Booking_System_Logger::log_info('Google Meet created', array(
            'consultation_id' => $consultation->id,
            'event_id' => $body['id']
        ));

        return Result::success(array(
            'event_id' => $body['id'],
            'meet_link' => $meet_link
        ));
    }

    public function delete_meeting($event_id) {
        try {
            $token = $this->get_access_token();
            
            $url = 'https://www.googleapis.com/calendar/v3/calendars/primary/events/' . urlencode($event_id);
            
            $response = wp_remote_request($url, array(
                'method' => 'DELETE',
                'headers' => array(
                    'Authorization' => 'Bearer ' . $token
                ),
                'timeout' => 30
            ));

            if (is_wp_error($response)) {
                Booking_System_Logger::log_error('Google Meet deletion failed: ' . $response->get_error_message());
                return Result::failure('Nie udało się usunąć spotkania Google Meet.');
            }

            $code = wp_remote_retrieve_response_code($response);
            
            if ($code !== 204 && $code !== 410) { // 204 = deleted, 410 = already deleted
                Booking_System_Logger::log_error('Google Meet deletion unexpected response', array('code' => $code));
                return Result::failure('Nieoczekiwana odpowiedź podczas usuwania spotkania.');
            }

            Booking_System_Logger::log_info('Google Meet deleted', array('event_id' => $event_id));

            return Result::success();

        } catch (Exception $e) {
            Booking_System_Logger::log_error('Google Meet deletion exception: ' . $e->getMessage());
            return Result::failure($e->getMessage());
        }
    }
}
