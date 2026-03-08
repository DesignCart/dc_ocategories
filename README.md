# Design Cart pCategories

Moduł PrestaShop 9 umożliwiający wybór kategorii ze sklepu i wyświetlanie ich na stronie głównej w konfigurowalnym gridzie.

**Autor:** Design Cart  
**Wersja:** 1.0.0  
**Zgodność:** PrestaShop 9.x

## Instalacja

1. Skopiuj folder `dc_pcategories` do `/modules/` (np. `/var/www/html/selmago/modules/dc_pcategories`).
2. W panelu admina: **Moduły** → wyszukaj „Design Cart pCategories” → **Zainstaluj**.
3. Skonfiguruj moduł (Konfiguruj) i przypisz hook **displayHome** (lub **displayWrapperTop**) do szablonu, jeśli to konieczne w Twoim motywie.

## Konfiguracja (zakładki)

### Intro
- **Tytuł sekcji (H2)** – wielojęzyczny tytuł nad sekcją kategorii.
- **Opis** – wielojęzyczny opis pod tytułem.

### Kategorie
- Siatka wszystkich kategorii sklepu (z pominięciem kategorii głównej i root).
- Każdy kafelek: **nazwa kategorii** (u góry), **breadcrumb** od głównej kategorii lub napis „Kategoria główna” (u dołu), w środku ikona:
  - **plus-circle** (szara) – kategoria nie jest wybrana,
  - **check-circle** (zielona) – kategoria wybrana.
- Kliknięcie kafelka dodaje/usuwa kategorię z wyświetlania. **Zapisz wybrane kategorie** zapisuje zmiany.

### Design
- **Ogólnie:** tło całego modułu.
- **Intro:** rozmiar i kolor czcionki tytułu, rozmiar i kolor opisu, wyrównanie (środek/lewo), separator &lt;hr&gt;.
- **Grid kategorii:** liczba kategorii w rzędzie (2–6), tło kafelka, pokazywanie zdjęcia kategorii i jego szerokość (%), kolor i rozmiar nazwy kategorii, wyrównanie nazwy.

## Front

Sekcja wyświetla się w miejscu hooka **displayHome** (lub **displayWrapperTop**).  
Cały kafelek (zdjęcie + nazwa) jest linkiem do danej kategorii.

## Wymagania

- PrestaShop 9.0.x
- PHP 7.4+
