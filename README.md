# MTGO Bot Frontend (Mezzio)

A modern web interface for managing MTGO trading bots, built on the Mezzio microframework.

## Features

- User authentication and authorization
- Bot management interface
- Real-time trade monitoring
- Responsive design for all devices
- Built on PSR-15 middleware

## Requirements

- PHP 8.0 or later
- Composer
- MySQL 5.7+ or MariaDB 10.2+
- Node.js and NPM (for frontend assets)

## Installation

1. Clone the repository:
   ```bash
   git clone <repository-url> mtgo-bot-frontend
   cd mtgo-bot-frontend
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install frontend dependencies:
   ```bash
   npm install
   ```

4. Copy the configuration file:
   ```bash
   cp config/autoload/local.php.dist config/autoload/local.php
   ```

5. Update the configuration in `config/autoload/local.php` with your database credentials and other settings.

6. Run database migrations:
   ```bash
   ./vendor/bin/laminas-migration migrate
   ```

7. Build frontend assets:
   ```bash
   npm run build
   ```

## Development

### Enable Development Mode

To enable development mode, run:

```bash
composer development-enable
```

This will enable development-specific features like error display and disable caching.

### Running the Development Server

Start the built-in PHP development server:

```bash
composer serve
```

Then visit http://localhost:8080 in your browser.

### Running Tests

Run PHPUnit tests:

```bash
composer test
```

Run PHP_CodeSniffer for code style checking:

```bash
composer cs-check
```

Fix code style issues automatically:

```bash
composer cs-fix
```

### Clearing Cache

Clear the application cache:

```bash
composer clear-cache
```

Clear the configuration cache:

```bash
rm -rf data/cache/*
```

## Production

For production, make sure to disable development mode:

```bash
composer development-disable
```

And optimize the autoloader:

```bash
composer dump-autoload --optimize
```

