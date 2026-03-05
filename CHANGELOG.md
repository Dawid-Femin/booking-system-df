# Changelog

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
