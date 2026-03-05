# System Rezerwacji Konsultacji DF

System rezerwacji konsultacji psychologicznych z integracją PayU i Google Meet.

## Autor
Dawid Femin

## Funkcjonalności

- Rezerwacja konsultacji online
- Integracja z PayU (płatność przed rezerwacją)
- Automatyczne tworzenie spotkań Google Meet
- System powiadomień email
- Panel administracyjny
- Szyfrowanie danych pacjentów
- Automatyczne przypomnienia

## Wymagania

- WordPress 5.0+
- PHP 7.4+
- MySQL 5.7+
- Rozszerzenie PHP: sodium lub openssl
- Konto PayU
- Konto Google Cloud (dla Google Meet)

## Instalacja

1. Skopiuj folder wtyczki do `wp-content/plugins/`
2. Aktywuj wtyczkę w panelu WordPress
3. Skonfiguruj ustawienia w menu "Rezerwacje"

## Konfiguracja

### PayU
1. Uzyskaj dane dostępowe z PayU
2. Wprowadź Client ID, Client Secret i POS ID w ustawieniach
3. Wybierz tryb (sandbox/production)

### Google Meet
1. Utwórz projekt w Google Cloud Console
2. Włącz Google Calendar API
3. Utwórz OAuth 2.0 credentials
4. Wprowadź dane w ustawieniach wtyczki

## Licencja

GPL-2.0+
