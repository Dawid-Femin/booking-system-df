# Instrukcja konfiguracji GitHub

## Utworzenie repozytorium na GitHub

1. Zaloguj się na GitHub.com
2. Kliknij "+" w prawym górnym rogu → "New repository"
3. Nazwa: `booking-system-df`
4. Opis: `System rezerwacji konsultacji psychologicznych z PayU i Google Meet`
5. Wybierz: Private (lub Public jeśli chcesz)
6. NIE zaznaczaj "Initialize with README" (już mamy pliki)
7. Kliknij "Create repository"

## Połączenie lokalnego repo z GitHub

W terminalu, w katalogu wtyczki wykonaj:

```bash
# Dodaj remote (zamień YOUR_USERNAME na swoją nazwę użytkownika GitHub)
git remote add origin https://github.com/YOUR_USERNAME/booking-system-df.git

# Wypchnij wszystkie commity
git push -u origin master
```

## Weryfikacja

Po wykonaniu powyższych kroków:
1. Odśwież stronę repozytorium na GitHub
2. Powinieneś zobaczyć wszystkie pliki
3. W zakładce "Commits" powinieneś zobaczyć 5 commitów:
   - Etap 1: Struktura podstawowa, utilities i modele danych
   - Etap 2: Core functionality - logika biznesowa
   - Etap 3: Panel administracyjny
   - Etap 4: Frontend i shortcodes
   - Etap 5: Finalizacja i dokumentacja

## Struktura projektu

```
booking-system-df/
├── admin/                  # Panel administracyjny
│   ├── css/
│   ├── js/
│   ├── views/
│   └── class-booking-system-admin.php
├── includes/               # Logika biznesowa
│   ├── models/            # Modele danych
│   ├── class-availability-manager.php
│   ├── class-booking-engine.php
│   ├── class-payu-gateway.php
│   ├── class-google-meet-integration.php
│   └── ...
├── public/                 # Frontend
│   ├── css/
│   ├── js/
│   ├── views/
│   └── class-booking-system-public.php
├── languages/              # Tłumaczenia
├── logs/                   # Logi (ignorowane przez git)
├── booking-system-df.php   # Główny plik wtyczki
├── README.md
├── DEPLOYMENT_GUIDE.md
├── CHANGELOG.md
└── .gitignore
```

## Statystyki

- **38 plików PHP**
- **5 commitów**
- **Pełna funkcjonalność**:
  - Rezerwacje z płatnością PayU
  - Automatyczne spotkania Google Meet
  - Panel administracyjny
  - Frontend z shortcodami
  - System powiadomień
  - Szyfrowanie danych
  - Automatyzacja (cron jobs)

## Następne kroki

1. Skonfiguruj GitHub Actions dla CI/CD (opcjonalnie)
2. Dodaj testy jednostkowe (opcjonalnie)
3. Utwórz releases/tags dla wersji
4. Dodaj Issues dla przyszłych funkcjonalności

## Klonowanie na innym komputerze

```bash
git clone https://github.com/YOUR_USERNAME/booking-system-df.git
cd booking-system-df
```

## Backup

Repozytorium GitHub służy jako backup. Zalecamy również:
- Regularne pushe po zmianach
- Tworzenie tagów dla stabilnych wersji
- Backup bazy danych osobno
