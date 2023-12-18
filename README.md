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

## Zdjęcia

### Posty
Mamy tutaj posty, wyświetlane jest 5 postów za pomoca paginacji, aby nie wywalić serwera
![Posty](https://raw.githubusercontent.com/JanuszProgramowaniaa/Cogitech_Recruitment/master/public/images/posty.jpg)

### Postman
Mamy tutaj pokazany strzał przez postmana na adres/api/posts
![Posty](https://raw.githubusercontent.com/JanuszProgramowaniaa/Cogitech_Recruitment/master/public/images/api.jpg)

### Api Platform
Tutaj mamy pokazane dostepne endpoiny w api platform
![Api platform](https://raw.githubusercontent.com/JanuszProgramowaniaa/Cogitech_Recruitment/master/public/images/Api-platform.jpg)

### Import tabel
Tutaj mamy pokazany import tabel z api do naszej bazy danych za pomoca komendy
![import](https://raw.githubusercontent.com/JanuszProgramowaniaa/Cogitech_Recruitment/master/public/images/import.jpg)

### Test komendy
Tutaj widzimy działanie testu dla ImportPostsCommand
![Test](https://raw.githubusercontent.com/JanuszProgramowaniaa/Cogitech_Recruitment/master/public/images/test.jpg)
