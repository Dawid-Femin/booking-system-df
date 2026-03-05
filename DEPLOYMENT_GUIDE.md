# Przewodnik wdrożenia - System Rezerwacji Konsultacji DF

## Wymagania

- WordPress 5.0+
- PHP 7.4+
- MySQL 5.7+
- Rozszerzenie PHP: sodium lub openssl
- Konto PayU
- Konto Google Cloud (dla Google Meet)

## Instalacja

### 1. Instalacja wtyczki

1. Skopiuj folder `booking-system-df` do `wp-content/plugins/`
2. Aktywuj wtyczkę w panelu WordPress
3. Wtyczka automatycznie utworzy tabele w bazie danych

### 2. Konfiguracja PayU

1. Zaloguj się do panelu PayU
2. Przejdź do Ustawienia → Punkty płatności
3. Skopiuj:
   - Client ID
   - Client Secret
   - POS ID
4. W WordPress przejdź do Rezerwacje → Ustawienia
5. Wprowadź dane PayU
6. Dla testów zaznacz "Tryb Sandbox"

### 3. Konfiguracja Google Meet

1. Przejdź do [Google Cloud Console](https://console.cloud.google.com/)
2. Utwórz nowy projekt
3. Włącz Google Calendar API
4. Utwórz OAuth 2.0 credentials:
   - Typ: Web application
   - Authorized redirect URIs: `https://twoja-domena.pl/oauth2callback`
5. Pobierz Client ID i Client Secret
6. Uzyskaj Refresh Token (użyj OAuth Playground)
7. Wprowadź dane w WordPress → Rezerwacje → Ustawienia

### 4. Konfiguracja dostępności

1. Przejdź do Rezerwacje → Dostępność
2. Dodaj reguły dostępności (np. Poniedziałek 9:00-17:00)
3. Opcjonalnie dodaj okresy blokady (urlopy, święta)

### 5. Dodanie typów konsultacji

1. Przejdź do Rezerwacje → Typy konsultacji
2. Dodaj typy (np. "Konsultacja 60 min")
3. Ustaw czas trwania i cenę

### 6. Dodanie shortcodów na stronie

Utwórz strony WordPress i dodaj shortcody:

**Strona rezerwacji:**
```
[booking_form type_id="1"]
```

**Strona z kalendarzem:**
```
[booking_calendar type_id="1"]
```

**Strona "Moje konsultacje":**
```
[my_consultations]
```

## Testowanie

### Test płatności (Sandbox)

1. Zaznacz "Tryb Sandbox" w ustawieniach PayU
2. Dokonaj testowej rezerwacji
3. Użyj danych testowych PayU do płatności
4. Sprawdź czy konsultacja pojawia się w panelu

### Test Google Meet

1. Potwierdź konsultację w panelu admin
2. Sprawdź czy email z linkiem został wysłany
3. Sprawdź czy spotkanie pojawiło się w Google Calendar

## Cron Jobs

Wtyczka używa WordPress Cron do:
- Wysyłania przypomnień 24h przed konsultacją
- Wysyłania przypomnień 1h przed konsultacją
- Oznaczania zakończonych konsultacji
- Czyszczenia starych danych

Upewnij się, że WordPress Cron działa poprawnie.

## Bezpieczeństwo

- Dane pacjentów są szyfrowane w bazie danych
- Credentials PayU i Google są szyfrowane
- Logi są chronione przez .htaccess
- Używane są prepared statements dla zapytań SQL

## Wsparcie

W razie problemów sprawdź logi w `wp-content/plugins/booking-system-df/logs/`

## Backup

Zalecamy regularne backupy:
- Bazy danych (tabele wp_booking_*)
- Plików wtyczki
- Klucza szyfrowania (opcja: booking_system_df_encryption_key)
