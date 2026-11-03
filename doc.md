# Abia Tech Hub Incubation

Abia Tech Hub Incubation is a Laravel web application for the Hub's public programme information, startup applications, customer account access, and staff administration.

## Features

- Public pages for the programme, eligibility, partners, FAQs, and application information.
- Validated startup applications with institution, team, sector, stage, and mentoring details.
- Newsletter subscriptions.
- Customer accounts created through single-use email sign-in links that expire after 15 minutes.
- A staff dashboard for applications, bookings, and subscriber activity, with application status updates.
- Role-based staff access, email invitations, two-factor authentication (TOTP), and audit logging.
- Contact details are restricted to OWNER and ADMIN roles. OWNER accounts manage the team and audit log.

## Technology

- PHP 8.5 (Composer currently allows PHP `^8.3`).
- Laravel 13.
- SQLite by default in `.env.example`; MySQL and other Laravel-supported databases can be configured.
- Blade views, Vite 8, and Tailwind CSS 4.
- PHPUnit 12 for unit and feature tests.

You will need PHP with the extensions required by Laravel and the selected database driver, Composer, Node.js, and npm.

## Getting Started

From the project root, install the application dependencies and build the frontend:

```sh
composer run setup
```

The setup script installs Composer and npm packages, creates `.env` from `.env.example` if needed, generates the application key, runs migrations, and builds the frontend assets. It does not overwrite an existing `.env` file.

### Database and sessions

The example environment uses SQLite. Configure `DB_CONNECTION` and the related `DB_*` values in `.env` if you use another database. Run `php artisan migrate` whenever the schema changes.

The application uses database sessions by default. The repository does not currently include a migration for the `sessions` table, although staff invitation and security flows query that table to revoke sessions. For those flows, generate the migration once and run it:

```sh
php artisan make:session-table
php artisan migrate
```

Keep the generated migration in version control so other environments receive the same schema. A file-based session driver can be used for basic local page development by setting `SESSION_DRIVER=file` in `.env`, but database-backed session revocation requires the sessions table and database session driver.

### Create the initial owner

Add an owner email and a password of at least 12 characters to `.env`:

```dotenv
OWNER_EMAIL=owner@example.com
OWNER_PASSWORD=replace-with-a-strong-password
```

Then seed the database:

```sh
php artisan db:seed
```

The seeder creates or updates the OWNER account. Sign in at `/admin/login`; the owner must enroll in two-factor authentication before accessing the staff dashboard. Do not use a real password in documentation, source control, or shared terminal output.

### Start development

Run the configured Laravel development processes:

```sh
composer run dev
```

This starts the Laravel server, queue listener, and Vite. The default application URL is `http://localhost:8000`. Stop the processes with `Ctrl+C`.

To run processes separately, use two terminals:

```sh
php artisan serve
```

```sh
npm run dev
```

For a production frontend build, run `npm run build`.

## Mail Configuration

The example environment uses the `log` mail driver. In local development, sign-in links, team invitations, and application notification emails are written to `storage/logs/laravel.log` instead of being delivered. Configure a real mail transport in `.env` for delivery. Set `NOTIFY_EMAIL` to receive new application notifications; it is optional.

## Routes

| Path | Purpose |
| --- | --- |
| `/` | Home page |
| `/about` | About the Hub |
| `/who-can-apply` | Eligibility information |
| `/program` | Programme information |
| `/partners` | Partner information |
| `/faqs` | Frequently asked questions |
| `/apply` | Application form |
| `/account` | Customer account page and magic-link request |
| `/auth/verify` | Confirm a customer sign-in link |
| `/admin/login` | Staff sign-in |
| `/admin/setup` | Set a password from a staff invitation |
| `/admin/security` | Configure staff two-factor authentication |
| `/admin/mfa` | Verify a staff two-factor code |
| `/admin` | Staff dashboard |
| `/admin/team` | OWNER team management |
| `/admin/audit` | OWNER audit log |

Application submissions are sent with `POST /apply`; newsletter subscriptions use `POST /newsletter`. The web routes and authorization middleware are defined in [`routes/web.php`](routes/web.php).

## Roles and Security

- `CUSTOMER` accounts are created on successful email-link sign-in.
- `STAFF` can access the staff dashboard.
- `ADMIN` can manage application status and view applicant contact details. ADMIN accounts require MFA.
- `OWNER` has ADMIN capabilities and can invite or remove staff and view the audit log. OWNER accounts require MFA.

Staff invitations expire after 48 hours. Customer sign-in links are single-use and expire after 15 minutes. Staff authentication uses a password and, for OWNER and ADMIN accounts, a time-based one-time password. Authentication attempts are rate-limited, and sensitive account actions are recorded in the audit log.

Keep `.env` and production credentials private. In production, set `APP_DEBUG=false`, use a unique `APP_KEY`, configure HTTPS and a real mail transport, and use managed database, session, cache, and queue services appropriate to the deployment.

## Tests and Code Style

Run the test suite with either command:

```sh
composer test
```

```sh
vendor/bin/phpunit
```

Tests use in-memory SQLite, array sessions and cache, synchronous queues, and an array mailer as configured in `phpunit.xml`.

Format changed PHP files with:

```sh
vendor/bin/pint --dirty --format agent
```

## Project Structure

| Path | Contents |
| --- | --- |
| `app/Http/Controllers` | Public and admin request handlers |
| `app/Http/Middleware` | Authentication, authorization, and request middleware |
| `app/Models` | Eloquent models for users, applications, bookings, messages, subscribers, tokens, and audit logs |
| `app/Support` | Shared security, audit, masking, and TOTP helpers |
| `database/migrations` | Database schema migrations |
| `database/seeders` | Database seeders, including the configurable owner account |
| `resources/views` | Blade pages and shared view layouts |
| `resources/css` and `resources/js` | Frontend entry points for Vite |
| `routes/web.php` | Web routes |
| `tests/Feature` and `tests/Unit` | Feature and unit tests |

## Useful Commands

```sh
php artisan route:list
php artisan migrate:status
php artisan db:seed
php artisan test
npm run build
```