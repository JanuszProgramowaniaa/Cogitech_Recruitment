# Ważne informacje dotyczące istalacji projektu

## Instalacja

### Sklonuj repozytorium
```git clone https://github.com/yourusername/ChicChic-Shop.git```

### Instalacja Composer
- Upewnij się, że na Twoim systemie jest zainstalowany PHP oraz Composer. Jeśli nie masz jeszcze Composera, możesz go pobrać i zainstalować, kierując się instrukcjami dostępnymi na oficjalnej stronie [Composer](https://getcomposer.org/).
- Zainstaluj paczki composera
```
 composer install
```
### Baza danych
- Stworz bazę mySql dla projektu
- Skonfiguruj połączenie w pliku .env/local 
```DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name```
- Dokonaj migracji
```
 php bin/console doctrine:migrations:migrate
```
### Import tabel user oraz posts z Api
- Zaomportuj dane do tabel user oraz posts(Pamiętaj że po odpaleniu importu tabele zostają wyczyszczone oraz ich indeksy wyzerowane, aby dane były spójne, jeśli wczesniej ktoś się zarejestrował to te dane zostaly utracone!!!)
```
 symfony console app:import-posts
```

### Uruchom server symfony
Uruchomienie serwera symfony:
```symfony server:start```
Projekt będzie dostępny pod http://localhost:8000 w twojej przeglądarce.

