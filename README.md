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

5. Start the local development server

```bash
php -S localhost:9000 -t public
```

## Railway Production Deployment

Railway uses the repository's `nixpacks.toml` to run Nginx with PHP-FPM and
serve Laravel from `/app/public`.

Railway Start Command:

Leave the Start Command field empty. Nixpacks will generate the Nginx +
PHP-FPM start command. A Railway Start Command overrides `nixpacks.toml`, so
remove any existing `php artisan serve` command.

Railway pre-deploy command:

```bash
php artisan migrate --force && php artisan db:seed --force && php artisan config:clear && php artisan cache:clear
```

Keep the Railway `APP_URL` variable set to:

```text
https://protective-emotion-production-e78f.up.railway.app
```

Use these Railway session variables:

```dotenv
SESSION_DOMAIN=
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

`SESSION_DOMAIN` must be empty so Laravel emits a host-only cookie. Do not set
it to `.up.railway.app` or to the literal string `null`.

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

On an otherwise empty database, expect 17 departments, 41 doctors (the required 34-doctor catalog plus 7 existing local demo doctors), 8 rooms, 9 staff members, 24 patients/users, and at least 287 appointments. Existing production records are preserved, so deployed totals may be higher.

## Demo Login Accounts

New seeded accounts, and existing accounts whose password is missing, use `password123`. Seeders never replace a non-empty existing password.

### Admin

- `admin@gmail.com`

### Staff

- `admin@test.com`
- `staff@nuh.com`
- `staff22@nuh.com`
- `reception@nuh.com`
- `lab@nuh.com`
- `radiology@nuh.com`
- `nurse@nuh.com`
- `accounts@nuh.com`
- `support@nuh.com`

### Doctors

- `doctor1@nuh.com`
- `doctor2@nuh.com`
- `doctor3@nuh.com`
- `doctor4@nuh.com`
- `amrfatouh58@gmail.com`
- `www.amrfatouh22@gmail.com`
- `amr@gmail.com`
- `ahmed-hassan-d-ahmd-hsn.internal-medicine@nuh.example`
- `mariam-adel-d-mrym-aaadl.internal-medicine@nuh.example`
- `karim-nabil-d-krym-nbyl.general-surgery@nuh.example`
- `salma-youssef-d-slm-yosf.general-surgery@nuh.example`
- `omar-el-sayed-d-aamr-alsyd.orthopedics@nuh.example`
- `farida-samir-d-fryd-smyr.orthopedics@nuh.example`
- `nourhan-fathy-d-norhan-fthy.obstetrics-and-gynecology@nuh.example`
- `hana-mahmoud-d-hna-mhmod.obstetrics-and-gynecology@nuh.example`
- `youssef-mansour-d-yosf-mnsor.cardiology-catheterization@nuh.example`
- `laila-mostafa-d-lyl-mstf.cardiology-catheterization@nuh.example`
- `hisham-fouad-d-hsham-foad.intensive-care-unit-icu@nuh.example`
- `dina-magdy-d-dyna-mgdy.intensive-care-unit-icu@nuh.example`
- `mahmoud-tarek-d-mhmod-tark.urology@nuh.example`
- `rana-khaled-d-rna-khald.urology@nuh.example`
- `amr-shawky-d-aamro-shoky.dialysis-nephrology@nuh.example`
- `menna-ibrahim-d-mn-abrahym.dialysis-nephrology@nuh.example`
- `hany-galal-d-hany-glal.pediatrics@nuh.example`
- `yasmin-sherif-d-yasmyn-shryf.pediatrics@nuh.example`
- `sherif-kamal-d-shryf-kmal.ophthalmology@nuh.example`
- `aya-nader-d-ay-nadr.ophthalmology@nuh.example`
- `tamer-amin-d-tamr-amyn.ent-ear-nose-and-throat@nuh.example`
- `reem-ashraf-d-rym-ashrf.ent-ear-nose-and-throat@nuh.example`
- `nader-wahba-d-nadr-ohb.neurosurgery@nuh.example`
- `malak-saeed-d-mlk-saayd.neurosurgery@nuh.example`
- `sameh-lotfy-d-samh-ltfy.neurology-psychiatry@nuh.example`
- `nada-fawzy-d-nd-fozy.neurology-psychiatry@nuh.example`
- `basel-hamdy-d-basl-hmdy.chest-pulmonology@nuh.example`
- `heba-mokhtar-d-hb-mkhtar.chest-pulmonology@nuh.example`
- `seif-el-din-d-syf-aldyn.dermatology@nuh.example`
- `joudy-ehab-d-gody-ayhab.dermatology@nuh.example`
- `mostafa-raouf-d-mstf-roof.emergency-physicians@nuh.example`
- `nermine-adel-d-nrmyn-aaadl.emergency-physicians@nuh.example`
- `khaled-zaki-d-khald-zky.radiology@nuh.example`
- `mai-samir-d-my-smyr.radiology@nuh.example`
