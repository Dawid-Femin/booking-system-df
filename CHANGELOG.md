# Changelog

## [1.0.19] - 2024-03-08

### Zmieniono
- Zwiększono offset scrollowania z -50px na -100px
- Jeszcze więcej przestrzeni nad elementami dla optymalnej widoczności

## [1.0.18] - 2024-03-08

### Zmieniono
- Zwiększono offset scrollowania z -20px na -50px
- Więcej przestrzeni nad kafelkiem "Wybrany termin" dla lepszej widoczności
- Dotyczy scrollowania po wyborze dnia i po wyborze godziny

## [1.0.17] - 2024-03-08

### Poprawiono
- Sekwencja animacji przy wyborze godziny - najpierw ukrywają się kroki 1 i 2, potem pokazuje się krok 3
- Eliminacja wielokrotnego scrollowania - scroll wykonuje się tylko raz po zakończeniu wszystkich animacji
- Płynniejsze przejście między krokami rezerwacji

## [1.0.16] - 2024-03-08

### Poprawiono
- Scroll do kafelka "Wybrany termin" po wyborze godziny
- Dodano callback do slideDown animation dla prawidłowego scrollowania
- Użytkownik widzi pełny kontekst swojego wyboru po animacji

## [1.0.15] - 2024-03-08

### Poprawiono
- Scroll do kafelka "Wybrany termin" zamiast do początku formularza
- Lepszy UX - użytkownik widzi pełny kontekst swojego wyboru

## [1.0.14] - 2024-03-08

### Zmieniono
- Ukrywanie sekcji "Wybierz dzień" po wyborze godziny
- Przycisk "Zmień" pokazuje z powrotem kalendarz dni

### Ulepszone
- Czystszy widok - widoczny tylko aktywny krok
- Lepszy flow rezerwacji

## [1.0.13] - 2024-03-08

### Poprawiono
- Przywrócenie border w selected-summary (2px solid #C8FEC2)

## [1.0.12] - 2024-03-08

### Zmieniono
- Kolor tła booking-header z #C8FEC2 na #f4faf5
- Kolor tła z #f8fdf5 na #f4faf5 (selected-summary, input focus)
- Bordery buttonów dni i godzin na none (czysty design)

### Ulepszone
- Bardziej subtelne tło nagłówka
- Czystszy wygląd buttonów bez borderów
- Spójność kolorystyczna

## [1.0.11] - 2024-03-08

### Dodano
- Tło #C8FEC2 dla booking-header
- Padding 30px dla booking-header
- Border-radius 26px dla booking-header

### Ulepszone
- Wizualne wyróżnienie nagłówka formularza rezerwacji
- Spójność z designem buttonów

## [1.0.10] - 2024-03-08

### Zmieniono
- Grid layout na flexbox z flex-wrap dla godzin
- Min-width: 85px dla buttonów godzin (spójna szerokość z dniami)
- Padding: 14px 10px dla lepszego dopasowania
- Responsive: min-width: 75px i padding: 12px 8px na mobile

### Ulepszone
- Spójna szerokość buttonów dni i godzin
- Lepsze dopasowanie layoutu
- Flexbox zapewnia bardziej elastyczny układ

## [1.0.9] - 2024-03-08

### Dodano
- Border 1px solid #ddd do buttonów godzin
- Grid layout dla `.time-slots-for-date` (godziny obok siebie)
- Pełna nazwa dnia i data w tytule kroku 2 (np. "Czwartek, 12 marca")
- Data-attribute `data-full-date` dla buttonów dni

### Zmieniono
- Godziny wyświetlają się obok siebie w grid layout z odstępami
- Tytuł wybranego dnia pokazuje pełną nazwę zamiast skrótu
- Responsive grid dla mobile (4 kolumny)

### Poprawiono
- Layout slotów czasowych (grid zamiast vertical stack)
- Czytelność wybranej daty dla użytkownika

## [1.0.8] - 2024-03-08

### Dodano
- Sloty czasowe co 30 minut (możliwość rezerwacji np. 8:30, 9:00, 9:30)
- Nadpisanie różowych kolorów z motywu za pomocą !important

### Zmieniono
- Godziny układają się obok siebie w grid layout (4 kolumny na mobile, auto-fill na desktop)
- Zmieniono interwał generowania slotów z duration_minutes na 30 minut
- Zaktualizowano przyciski w widoku potwierdzenia do stylu strony (#C8FEC2)
- Dodano !important do wszystkich buttonów, aby nadpisać style motywu

### Poprawiono
- Usunięto różowy kolor z wybranych elementów
- Poprawiono layout slotów czasowych (obok siebie zamiast pod sobą)
- Zwiększono elastyczność rezerwacji (więcej dostępnych godzin)

## [1.0.7] - 2024-03-08

### Zmieniono
- Dostosowano style buttonów do designu strony (Elementor)
- Kolor tła buttonów: #C8FEC2
- Kolor hover: #AEE7A5
- Font: "Lora", Sans-serif
- Border-radius: 26px (zaokrąglone rogi)
- Usunięto bordery z buttonów (czysty design)
- Zaktualizowano wszystkie elementy interaktywne (dni kalendarza, sloty czasowe, przyciski)
- Dostosowano inputy i textarea do stylu strony
- Ujednolicono typografię z czcionką Lora

### Ulepszone
- Spójność wizualna z główną stroną
- Bardziej elegancki i minimalistyczny wygląd
- Lepsze dopasowanie do brandingu

## [1.0.6] - 2024-03-08

### Dodano
- Kompaktowy mini-kalendarz z widokiem 2 tygodni (grid 7 dni)
- 3-krokowy proces rezerwacji (dzień → godzina → dane)
- Minimalistyczny design z zielonym akcentem (#7fb069)
- Interaktywny wybór dnia z wizualnym feedbackiem
- Osobny widok slotów czasowych dla wybranego dnia
- Przycisk "Zmień dzień" do powrotu do kalendarza
- Przycisk "Zmień" w podsumowaniu wybranego terminu

### Zmieniono
- Przeprojektowano kalendarz z 14 kart dni na kompaktowy grid
- Uproszczono UI - więcej białej przestrzeni, czystsza typografia
- Zmieniono kolor akcentu na #7fb069 (zielony z witryny)
- Poprawiono responsywność na urządzeniach mobilnych
- Zoptymalizowano layout dla lepszej czytelności

### Ulepszone
- Bardziej intuicyjny flow rezerwacji
- Lepsza wizualna hierarchia kroków
- Smooth transitions między krokami
- Kompaktowy widok kalendarza (zamiast długiej listy kart)

## [1.0.5] - 2024-03-08

### Dodano
- Nowoczesny kalendarz rezerwacji z kartami dni
- 2-krokowy proces rezerwacji (wybór terminu → dane osobowe)
- Wizualne potwierdzenie wybranego terminu (zielony box)
- Ikony SVG dla czasu trwania, ceny i potwierdzenia
- Auto-scroll do formularza po wyborze terminu
- Przycisk "Zmień" do zmiany wybranego terminu
- Przycisk "Wróć do kalendarza" w kroku 2
- Gradient design dla nagłówków kart dni
- Smooth animations i hover effects

### Zmieniono
- Przeprojektowano formularz rezerwacji z focus na UX
- Grid layout dla kart dni (responsywny)
- Nowoczesne przyciski z gradientami i cieniami
- Lepsze formatowanie dat (polskie nazwy miesięcy)
- Responsywny design dla urządzeń mobilnych

### Ulepszone
- Czytelność kalendarza
- Wizualna hierarchia informacji
- Interaktywność (hover states, transitions)
- Mobile-friendly layout

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
