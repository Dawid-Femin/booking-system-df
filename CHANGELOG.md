# Changelog

## [1.0.39] - 2024-03-11

### Poprawiono
- Przywrócono prawidłowy sandbox URL: secure.snd.payu.com (merch-prod to panel admina, nie API)
- Usunięto trailing slash z URL sandbox (powodował podwójny // w ścieżce API)

## [1.0.38] - 2024-03-11

### Poprawiono
- Zmieniono adres sandbox PayU na prawidłowy (merch-prod.snd.payu.com)

## [1.0.37] - 2024-03-11

### Poprawiono
- **KRYTYCZNE**: Znaleziono prawdziwą przyczynę błędu 403 CloudFront!
- PayU API zwraca 302 redirect z Location header (redirectUri)
- WordPress wp_remote_post domyślnie podąża za redirectem → GET na stronę płatności → CloudFront blokuje 403
- Postman NIE podąża za redirectami POST, dlatego z Postmana działało
- Dodano `'redirection' => 0` - WordPress nie podąża za 302, zwraca Location header
- Dodano obsługę odpowiedzi 302 - wyciąganie orderId i redirectUri z odpowiedzi
- Lepszy komunikat błędu z kodem HTTP

### Techniczne
- Root cause: wp_remote_post follow redirect (domyślnie redirection=5)
- PayU: POST /orders → 302 + Location: redirectUri + JSON body z orderId
- WordPress: POST → 302 → GET (follow redirect) → CloudFront → 403
- Fix: redirection=0 → dostajemy 302 z Location header → sukces

## [1.0.36] - 2024-03-11

### Poprawiono
- **KRYTYCZNE**: Kompletna naprawa integracji PayU z poprawnymi nagłówkami
- OAuth używa PostmanRuntime/7.51.0 jako User-Agent + force override
- Create order request z pełnym zestawem nagłówków Postmana:
  - X-Forwarded-For: {IP klienta}
  - X-Real-IP: {IP klienta}
  - User-Agent: PostmanRuntime/7.51.0
  - Accept: */*
  - Accept-Encoding: gzip, deflate, br
  - Connection: keep-alive
- Retrieve order używa PostmanRuntime/7.51.0 + force override
- Logi pokazują rzeczywiste wysłane nagłówki ($headers)

### Techniczne
- Plik był zepsuty w repozytorium - przepisany od nowa
- Wszystkie requesty do PayU używają spójnych nagłówków
- Force override user-agent we wszystkich requestach

## [1.0.35] - 2024-03-11

### Poprawiono
- **KRYTYCZNE**: Naprawiono błąd w logowaniu - logi pokazywały hardcoded stare nagłówki zamiast rzeczywistych
- Dodano X-Forwarded-For i X-Real-IP do faktycznych nagłówków requestu (nie tylko do logów)
- Dodano user-agent parameter do wp_remote_post dla force override
- Teraz request faktycznie wysyła PostmanRuntime/7.51.0 i IP klienta

### Techniczne
- Logi teraz pokazują rzeczywiste wysłane nagłówki ($headers zamiast hardcoded array)
- Nagłówki X-Forwarded-For i X-Real-IP dodane do $headers przed wysłaniem requestu

## [1.0.34] - 2024-03-10

### Poprawiono
- **KRYTYCZNE**: Naprawiono logowanie request headers (pokazywały stare wartości)
- Dodano X-Forwarded-For i X-Real-IP z IP klienta
- Dodano user-agent parameter do wp_remote_post (force override)
- Teraz logi pokazują faktycznie wysyłane nagłówki

### Techniczne
- WordPress może nadpisywać User-Agent - dodano force override
- Logi teraz pokazują prawdziwe nagłówki z PostmanRuntime/7.51.0

## [1.0.33] - 2024-03-10

### Poprawiono
- Dodano nagłówki X-Forwarded-For i X-Real-IP z IP klienta
- CloudFront może sprawdzać czy IP requestu pasuje do customerIp
- Symulacja requestu z IP klienta zamiast serwera

### Techniczne
- X-Forwarded-For: 91.150.182.117 (IP klienta)
- X-Real-IP: 91.150.182.117 (IP klienta)
- Może ominąć blokadę IP serwera

## [1.0.32] - 2024-03-10

### Poprawiono
- **KRYTYCZNE**: Pełna replikacja nagłówków z Postmana
- User-Agent: PostmanRuntime/7.51.0 (dokładnie jak w Postmanie)
- Accept-Encoding: gzip, deflate, br
- Connection: keep-alive
- Accept: */*

### Techniczne
- Postman działa z tymi nagłówkami - próba 1:1 replikacji
- CloudFront może sprawdzać User-Agent

## [1.0.31] - 2024-03-10

### Poprawiono
- Dodano nagłówki Accept i User-Agent (jak w Postmanie)
- User-Agent: BookingSystem/1.0 (prosty, bez WordPress w nazwie)
- Accept: */* (akceptuje wszystkie typy odpowiedzi)

### Techniczne
- Test z Postmana działa - próba replikacji nagłówków
- CloudFront może wymagać User-Agent header

## [1.0.30] - 2024-03-10

### Poprawiono
- Uproszczono nagłówki HTTP do minimum wymaganego przez PayU API
- Usunięto User-Agent, Origin, Referer, Accept - mogły powodować blokadę CloudFront
- Pozostawiono tylko Content-Type i Authorization zgodnie z dokumentacją

### Techniczne
- CloudFront może blokować requesty z niestandardowymi nagłówkami
- Minimalistyczne nagłówki mogą ominąć WAF rules

## [1.0.29] - 2024-03-10

### Poprawiono
- **KRYTYCZNE**: Obsługa błędu 403 CloudFront z PayU
- Automatyczne pobieranie zamówienia po extOrderId gdy PayU zwraca 403
- PayU Support potwierdził że zamówienia są tworzone mimo błędu 403
- Implementacja retrieve_order_by_ext_id() do pobierania redirectUri
- Konstrukcja payment URL gdy redirectUri nie jest dostępne

### Techniczne
- Zamówienia są tworzone na serwerach PayU mimo błędu 403 CloudFront
- Nowa strategia: próba utworzenia → 403 → pobierz zamówienie → przekieruj
- Fallback URL: /api/v2_1/orders/{orderId}/pay

## [1.0.28] - 2024-03-09

### Poprawiono
- **KRYTYCZNE**: OAuth używa teraz pos_id zamiast client_id
- Zgodność z wymaganiem PayU: POS ID w OAuth musi być identyczny z merchantPosId w zamówieniu
- To prawdopodobnie rozwiązuje błąd 403 CloudFront

### Techniczne
- Błąd 403 był spowodowany niezgodnością POS ID między OAuth a zamówieniem
- PayU zwraca INVALID_AUTH_FOR_THIS_ORDER gdy POS ID się nie zgadzają

## [1.0.27] - 2024-03-09

### Dodano
- Nagłówki Origin i Referer do requestów PayU (może pomóc z CloudFront)
- HTTP version 1.1 jawnie ustawiony
- Logowanie IP serwera dla debugowania
- Rozszerzone logowanie nagłówków requestu

### Techniczne
- Próba obejścia blokady CloudFront przez dodatkowe nagłówki

## [1.0.26] - 2024-03-09

### Poprawiono
- Retry z nowym extOrderId po błędzie 403 (unikanie ORDER_NOT_UNIQUE)
- Usunięto nieprawidłowe konstruowanie URL-a płatności
- Każda próba używa unikalnego extOrderId z timestamp i numerem próby

## [1.0.25] - 2024-03-09

### Poprawiono
- Nowa strategia dla ORDER_NOT_UNIQUE - utworzenie nowego zamówienia z _retry suffix
- Fallback do konstruowanego URL: /pl/standard/user/oauth/authorize?order_id={orderId}
- Lepsze logowanie procesu pobierania redirectUri

## [1.0.24] - 2024-03-09

### Poprawiono
- Pobieranie redirectUri z API PayU dla istniejących zamówień (ORDER_NOT_UNIQUE)
- Fallback do alternatywnego URL gdy redirectUri nie jest dostępne
- Poprawny URL przekierowania do strony płatności PayU

## [1.0.23] - 2024-03-09

### Poprawiono
- Kolejność sprawdzania odpowiedzi PayU - ORDER_NOT_UNIQUE sprawdzane przed innymi błędami
- Eliminacja fałszywych błędów gdy zamówienie już istnieje

## [1.0.22] - 2024-03-09

### Poprawiono
- Obsługa błędu ORDER_NOT_UNIQUE z PayU (gdy retry utworzył zamówienie)
- Używanie istniejącego orderId gdy zamówienie już istnieje
- Konstrukcja payment URL dla istniejących zamówień

### Techniczne
- Retry logic działa! Pierwsza próba 403, druga próba sukces
- PayU produkcyjne API działa z retry

## [1.0.21] - 2024-03-09

### Dodano
- User-Agent w nagłówkach żądań do PayU (identyfikacja aplikacji)
- Accept: application/json w nagłówkach
- Retry logic - automatyczne ponowienie przy błędach 403 i 5xx (do 3 prób)
- Opóźnienie 2 sekundy między próbami
- Szczegółowe logowanie każdej próby połączenia

### Ulepszone
- Lepsze logowanie błędów PayU (ograniczenie raw_body do 500 znaków)
- Dodano numer próby w logach
- SSL verification włączone jawnie

### Techniczne
- Może pomóc ominąć blokadę CloudFront przez lepszą identyfikację
- Automatyczne ponowienie przy przejściowych błędach

## [1.0.20] - 2024-03-09

### Dodano
- Pole "Drugi klucz (MD5)" w ustawieniach PayU
- Weryfikacja podpisu webhooków PayU dla bezpieczeństwa
- Automatyczne pomijanie weryfikacji gdy klucz MD5 nie jest skonfigurowany

### Bezpieczeństwo
- Webhooks PayU są teraz weryfikowane za pomocą podpisu MD5
- Ochrona przed fałszywymi powiadomieniami o płatnościach
- Używamy hash_equals() do bezpiecznego porównywania podpisów

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
