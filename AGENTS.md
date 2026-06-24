# Agent Guide: T-World

## Architecture & Conventions
- **Static vs PHP:** Do not edit `.html` files in root or `pages/` (these are older static prototypes). Work only on `.php` counterparts.
- **CSRF Protection:** Forms handling POST actions must include CSRF tokens. Use `csrf_input()` in forms and validate via `require_valid_csrf($redirectPath)` or similar in handlers.
- **Paths & Imports:** Every page sets `$basePath`, sets `$pageTitle` and `$pageCss`, then requires `includes/functions.php` and `includes/header.php`. Always use `base_path($url)` and wrap outputs in `h($var)` for XSS protection.
- **Tooling Limitations:** There is no package manager (npm/composer), no test runner, and PHP/MySQL CLI tools may not be on PATH. Test changes manually via local browser (`http://t-world.test` in Laragon).
- **Database Credentials:** Local configuration is kept in `config/database.php`.

## Database Setup & Sync
- **Config:** Copy `config/database.example.php` to `config/database.php`.
- **Import schema:**
  ```cmd
  database\import-laragon.bat
  ```
  *(Or execute manually if Laragon MySQL path varies: `mysql -uroot < database/schema.sql`)*
- **Demo Admin:**
  - Email: `admin@t-world.test`
  - Password: `Malicha@123`

## Product Images
- Admin uploads go to `uploads/products/`; runtime upload files are gitignored.
- Seeded catalog images live in `images/`. Do not move admin uploads there.
