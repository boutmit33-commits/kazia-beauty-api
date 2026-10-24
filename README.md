# Kazia Beauty API (Backend)

REST API for Kazia Beauty, a skincare e-commerce site. Built with Laravel and MySQL. The frontend is a separate repo: [kazia-beauty-frontend](https://github.com/boutmit33-commits/kazia-beauty-frontend).

Built during my internship at Kazia Solutions.

## Features

- Product catalog with search and category filter
- Authentication with Laravel Sanctum (register, login, logout)
- Admin-only routes to create, update and delete products
- Order creation with server-side validation
- Stock is checked and updated automatically when an order is placed (inside a database transaction, so a failed order changes nothing)
- The order total is calculated on the server, not trusted from the browser

## Main endpoints

| Method | Route | Access |
|--------|-------|--------|
| GET | /api/products | Public |
| GET | /api/products/{id} | Public |
| POST / PUT / DELETE | /api/products | Admin only |
| POST | /api/orders | Public |
| POST | /api/register, /api/login | Public |
| POST | /api/logout | Logged in |

## Tech stack

- PHP, Laravel
- MySQL
- Laravel Sanctum

## Run it locally

You need PHP, Composer and MySQL.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Set your database details in `.env`, then:

```bash
php artisan migrate
php artisan db:seed --class=ProductSeeder
php artisan serve
```

The API runs at http://127.0.0.1:8000

To make an admin account, register a user and set `is_admin` to 1 for it in the database.