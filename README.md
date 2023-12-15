# Ważne informacje dotyczące istalacji projektu

**1. Skonfiguruj bazę danych oraz dokonaj migracji:**

```
 php bin/console doctrine:migrations:migrate
```

**2. Zaomportuj dane do tabel user oraz posts(Pamiętaj że po odpaleniu importu tabele zostają wyczyszczone oraz ich indeksy wyzerowane, aby dane były spójne, jeśli wczesniej ktoś się zarejestrował to te dane zostaly utracone!!!):**

```
 symfony console app:import-posts
```
