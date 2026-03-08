# Changelog

## [1.0.4] - 2024-03-08

### Dodano
- 24-godzinny bufor rezerwacji - klienci mogą rezerwować tylko terminy za minimum 24 godziny
- Automatyczne blokowanie terminów w ciągu najbliższych 24 godzin

### Zmieniono
- `Availability_Manager` filtruje sloty, które są za mniej niż 24 godziny od obecnego czasu
- Zarówno nowy system slotów jak i stary system reguł respektują 24-godzinny bufor

### Bezpieczeństwo
- Zapobiega rezerwacjom "na ostatnią chwilę"
- Daje psychologowi czas na przygotowanie się do konsultacji

## [1.0.3] - 2024-03-08

### Dodano
- Nowy system dostępności - kalendarz 2-tygodniowy
- Możliwość dodawania wielu przedziałów czasowych dla jednego dnia (np. 8:00-12:00 i 16:00-18:00)
- Funkcja kopiowania przedziałów czasowych między dniami
- Funkcja wklejania skopiowanych przedziałów
- Przycisk "Dodaj przedział" dla każdego dnia
- Automatyczne tworzenie tabeli `booking_availability_slots` przy pierwszym wejściu
- Model `Availability_Slot` dla konkretnych dat (zamiast dni tygodnia)
- Kompatybilność wsteczna ze starym systemem reguł tygodniowych

### Zmieniono
- Widok dostępności z formularza reguł na kalendarz 2-tygodniowy
- `Availability_Manager` używa nowego systemu slotów (z fallbackiem do starych reguł)
- Ulepszona obsługa wielu przedziałów czasowych w ciągu dnia

### Techniczne
- Nowa tabela: `wp_booking_availability_slots` (date, start_time, end_time)
- JavaScript do obsługi kopiowania/wklejania przedziałów
- Automatyczna migracja przy pierwszym dostępie do strony dostępności

## [1.0.2] - 2024-03-08

### Dodano
- Możliwość edycji typów konsultacji
- Kolumna "Status" w liście typów konsultacji (aktywny/nieaktywny)
- Przycisk "Edytuj" przy każdym typie konsultacji
- Przycisk "Anuluj" podczas edycji typu
- Wyświetlanie wszystkich typów (również nieaktywnych) w panelu administracyjnym

### Poprawiono
- Formularz typów konsultacji automatycznie wypełnia się danymi podczas edycji
- Lista typów pokazuje również nieaktywne typy dla łatwiejszego zarządzania

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
