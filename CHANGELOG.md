# Changelog

## [1.0.1] - 2024-03-08

### Dodano
- Opcja "Tryb deweloperski (pomiń PayU)" w ustawieniach dla testów na produkcji
- Strona potwierdzenia rezerwacji z shortcode [booking_confirmation]
- Opcja "Wymuś PayU na localhost" dla testowania integracji lokalnie

### Poprawiono
- Błąd wyświetlania zapisanych danych PayU w ustawieniach
- Problem z kalendarzem nie wyświetlającym się w formularzu rezerwacji
- Błąd DateTime przy generowaniu slotów czasowych (usunięto sekundy z formatu czasu)
- Usunięto SERIALIZABLE transaction isolation dla kompatybilności z MySQL
- Zmieniono redirect z wp_redirect na JavaScript redirect w shortcode
- Dodano checkbox "is_active" w formularzu reguł dostępności
- Poprawiono wyświetlanie polskich nazw dni tygodnia

### Techniczne
- Dodano szczegółowe logowanie dla debugowania PayU (raw_body, headers, API URL)
- Tryb deweloperski automatycznie akceptuje płatności bez przekierowania do PayU
- Obsługa błędu 403 z PayU Sandbox na niektórych domenach

## [1.0.0] - 2024-03-05

### Dodano
- System rezerwacji konsultacji psychologicznych
- Integracja z PayU (płatność przed rezerwacją)
- Integracja z Google Meet (automatyczne tworzenie spotkań)
- Panel administracyjny z 5 stronami
- System powiadomień email (6 typów)
- Szyfrowanie danych pacjentów
- Automatyczne przypomnienia (24h i 1h przed konsultacją)
- System logowania z rotacją plików
- Obsługa zwrotów płatności
- 3 shortcody dla frontendu
- Walidacja danych
- Obsługa stref czasowych (Europe/Warsaw)
- Generowanie plików ICS dla kalendarzy
- Cron jobs dla automatyzacji
- Dokumentacja wdrożenia

### Bezpieczeństwo
- Szyfrowanie danych wrażliwych (sodium/AES-256-CBC)
- Prepared statements dla wszystkich zapytań SQL
- Escaped output
- Nonce verification
- Ochrona katalogów .htaccess
- Walidacja wszystkich inputów

### Techniczne
- WordPress 5.0+ compatibility
- PHP 7.4+ compatibility
- MySQL 5.7+ compatibility
- InnoDB tables with proper indexes
- Transaction support (SERIALIZABLE)
- Error handling with retry mechanism
- Comprehensive logging system
