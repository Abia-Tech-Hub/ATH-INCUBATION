# Laravel project instructions

This project is a Laravel 13 application running on PHP 8.5.

- Keep application code in the standard Laravel directories.
- Use `php artisan make:* --no-interaction` for new Laravel classes and migrations.
- Run `vendor/bin/pint --dirty --format agent` after changing PHP files.
- Run the narrowest relevant tests with `vendor/bin/phpunit`; this project uses an in-memory SQLite database for tests.
- Do not reintroduce the retired Next.js or Prisma application.
