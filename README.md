# Laravel Database Studio & Table Manager GUI

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laraforge/database-studio.svg?style=flat-shadow)](https://packagist.org/packages/laraforge/database-studio)
[![Total Downloads](https://img.shields.io/packagist/dt/laraforge/database-studio.svg?style=flat-shadow)](https://packagist.org/packages/laraforge/database-studio)
[![License](https://img.shields.io/packagist/l/laraforge/database-studio.svg?style=flat-shadow)](https://packagist.org/packages/laraforge/database-studio)

A self-contained, Navicat & phpMyAdmin grade **Database Studio & Table Manager GUI** for Laravel 10, 11, 12, and 13 applications.

---

## 🌟 Key Features

- 📊 **Interactive Database Dashboard**: View table engine types, estimated row counts, data/index disk sizes, and table collations.
- 🛠️ **Visual Table Creator Wizard**: Design new database tables, define column data types, precision, nullability, auto-increments, and default values.
- 🔍 **Schema Inspector & Data Grid**: Inspect columns, indexes, foreign keys, filter records in real-time, and browse live rows.
- 💻 **Interactive SQL Console**: Execute raw `SELECT`, `INSERT`, `UPDATE`, `ALTER`, or `CREATE` queries directly from your browser.
- 📥 **One-Click CSV / Excel Exporter**: Export filtered table records directly to UTF-8 CSV or XML Excel formats.
- 🔒 **Safety Guards**: Protect critical system tables (`migrations`, `failed_jobs`, `users`) from accidental truncation or dropping.

---

## 🚀 Installation Guide

### Step 1: Install via Composer

Add the package to your Laravel project via Composer:

```bash
composer require laraforge/database-studio
```

*(For local repository development, add `"laraforge/database-studio": "*"` with path repository mapping in your host project's `composer.json`)*.

---

### Step 2: Publish Configuration (Optional)

Publish the `database-studio.php` config file to customize route path, middleware, and protected tables:

```bash
php artisan vendor:publish --tag=database-studio-config
```

---

### Step 3: Access the Web Dashboard

Open your browser and navigate to:

```text
http://your-app.test/database-studio
```

---

## ⚙️ Configuration (`config/database-studio.php`)

```php
return [
    'enabled' => env('DB_STUDIO_ENABLED', true),
    'path' => env('DB_STUDIO_PATH', 'database-studio'),
    'api_prefix' => env('DB_STUDIO_API_PREFIX', 'api/v1/database-manager'),
    'middleware' => [
        'web' => ['web'],
        'api' => ['api'],
    ],
    'connection' => env('DB_STUDIO_CONNECTION', null),
    'protected_tables' => [
        'migrations',
        'failed_jobs',
        'personal_access_tokens',
    ],
];
```

---

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
