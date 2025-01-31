# ConcertLog

ConcertLog to aplikacja webowa, która pozwala użytkownikom rejestrować i dzielić się swoimi doświadczeniami koncertowymi. Użytkownicy mogą dodawać nowe koncerty, przeglądać swoją historię koncertów oraz zobaczyć statystyki dotyczące swojej frekwencji na koncertach.

## Funkcje

- **Autoryzacja użytkowników**: Rejestracja, logowanie i wylogowywanie.
- **Dodawanie koncertów**: Użytkownicy mogą dodawać nowe koncerty z takimi szczegółami jak artysta, data, miejsce i lokalizacja. Mogą również przesyłać zdjęcia z koncertu.
- **Przeglądanie koncertów**: Użytkownicy mogą przeglądać listę koncertów dodanych przez wszystkich użytkowników.
- **Zarządzanie profilem**: Użytkownicy mogą przeglądać i edytować swój profil, w tym zmieniać nazwę użytkownika i zdjęcie profilowe.
- **Statystyki**: Użytkownicy mogą przeglądać statystyki dotyczące swojej frekwencji na koncertach, takie jak liczba odwiedzonych koncertów, ulubiony artysta i koncerty na rok.
- **Strona administratora**: Użytkownicy z uprawnieniami administratora mogą przeglądać i zarządzać wszystkimi użytkownikami.

## Wymagania wstępne

- Docker
- Docker Compose

## Instalacja

1. **Sklonuj repozytorium**:
    ```sh
    git clone https://github.com/yourusername/concertlog.git
    cd concertlog
    ```

2. **Skonfiguruj zmienne środowiskowe**:
    - Utwórz plik [config.php](http://_vscodecontentref_/0) w katalogu głównym z następującą zawartością:
    ```php
    <?php

    const USERNAME = 'root';
    const PASSWORD = 'root';
    const HOST = 'db';
    const DATABASE = 'wdpai';
    ```

3. **Zbuduj i uruchom kontenery Docker**:
    ```sh
    docker-compose up --build
    ```

4. **Zainicjuj bazę danych**:
    - Uzyskaj dostęp do kontenera bazy danych:
    ```sh
    docker exec -it concertlog_db_1 bash
    ```
    - Uruchom skrypt SQL, aby zainicjować bazę danych:
    ```sh
    mysql -u root -p wdpai < /db/init_database.sql
    ```

5. **Uzyskaj dostęp do aplikacji**:
    - Otwórz przeglądarkę internetową i przejdź do `http://localhost:8080`.

## Utworzeni użytkownicy

Poniżsi użytkownicy zostali utworzeni w celach testowych:

- **Użytkownik 1**
  - Email: jkowalski@gmail.com
  - Password: janek123

- **Użytkownik 2**
  - Email: anna.nowak@gmail.com
  - Password: anka123

- **Użytkownik administrator**
  - Email: admin@admin.com
  - Password: admin

**Diagram ERD bazy danych:**

![Diagram ERD](https://github.com/user-attachments/assets/596c9985-b716-4fc8-8f92-80070867a2a6)

**Zrzuty ekranu z aplikacji:**

Strona główna:

![screenshot1](https://github.com/user-attachments/assets/341e8c29-cbf1-454a-8afb-8840ee43ecc9)
![screenshot2](https://github.com/user-attachments/assets/9be6ff60-dacd-4fb5-9248-1b76d7d7e83f)

Profil:

![screenshot3](https://github.com/user-attachments/assets/1e82f146-2b23-457d-8680-360ba9884523)
![screenshot4](https://github.com/user-attachments/assets/832f1ac3-4d05-4dc3-8711-ad0e85473f88)

Dodawanie koncertów:

![screenshot5](https://github.com/user-attachments/assets/0b402aed-2642-444f-96a6-41575560f1d2)

Ekrany logowania i rejestracji:

![screenshot6](https://github.com/user-attachments/assets/694bf077-b964-4f66-9fa7-fbc5e9ec81a2)
![screenshot7](https://github.com/user-attachments/assets/d12bd45e-64cc-4abd-8ba1-498661bfffe7)

Panel administratora:

![screenshot8](https://github.com/user-attachments/assets/729db5d3-f720-46a4-b959-b3612e857376)

Autor: Szymon Dral

Projekt realizowany na potrzeby zaliczenia przedmiotu Wstęp do projektowania aplikacji internetowych
