# NUH Hospital System

Hospital Management System built with Laravel.

## Modules

- Leave Requests Management
- Medical Positions Management
- Administrative Positions Management
- Training Programs Management
- Complaints Management

## Technologies

- Laravel
- Blade
- Bootstrap
- MySQL

## How to Run

1. Clone the repository

git clone https://github.com/ahmedmahfouz2004/NUH-Hospital.git

2. Install dependencies

composer install

3. Generate key

php artisan key:generate

4. Run migrations

php artisan migrate

5. Start server

php artisan serve

## Production Demo Seeding

Railway pre-deploy command:

```bash
php artisan migrate --force && php artisan db:seed --force && php artisan config:clear && php artisan cache:clear
```

The seeders are idempotent and update only records with stable demo identities. They do not truncate tables or require `migrate:fresh`.

Verify a deployment:

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan tinker
```

Then run:

```php
Department::count();
Doctor::count();
Room::count();
Staff::count();
Patient::count();
Appointment::count();
```

On an otherwise empty database, expect 17 departments, at least 34 doctors, 8 rooms, 7 staff members, 24 patients/users, and at least 238 appointments. Existing production records are preserved, so deployed totals may be higher.
