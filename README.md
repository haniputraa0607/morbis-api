# Morbis Api Test

Laravel 10

Fitur :

1. Buat antrian tanpa login
2. Admin bisa mengakses antrian

```
composer install
cp .env.example .env
php artisan key:generate
```
Sesuaikan env (koneksi database mysql)

```
php artisan migrate
php artisan passport:install

php artisan tinker
> User::factory()->count(5)->create()

php artisan serve
```

Export Morbis.postman_collection.json sebagai collection di Postman
Expost Lokal.postman_environment.json sebagai Environments di Postman

Login Admin dengan email yang tersedia di data table users, menggunakan password '12345'
