## About Laravel

Laravel is a web application framework with expressive, elegant syntax.
It simplifies common tasks such as routing, database migrations, authentication,
and background jobs, making development enjoyable and productive.

## Pizza App Backend Overview

This backend powers the Pizza App, providing API endpoints for:
- User authentication (register/login)
- Pizza menu retrieval
- Order checkout and management

Built with Laravel, Eloquent ORM, and Pest for testing.

## Requirements

- PHP >= 8.1
- Composer
- MySQL/PostgreSQL (or your preferred DB)
- Node.js & npm (optional, if compiling assets)

## Installation

1. Clone the repository:
git clone <repo-url>
cd pizza-app/backend

2. Install dependencies:
composer install

3. Create .env file:
cp .env.example .env

4. Generate application key:
php artisan key:generate

5. Configure your database in .env:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pizza_app
DB_USERNAME=root
DB_PASSWORD=

6. Run migrations and seeders:
php artisan migrate --seed

## Running the Backend

php artisan serve
Default URL: http://127.0.0.1:8000

## Running Tests

- Run all tests:
./vendor/bin/pest

- Run a specific test file:
./vendor/bin/pest tests/Feature/AuthTest.php

## Notes

- RefreshDatabase is enabled for Feature tests
- UserFactory now has role => 'customer' by default
- Tests included:
  - Registration/login (AuthTest)
  - GET /api/pizzas (PizzaMenuTest)
  - Order checkout (OrderCheckoutTest) – guest vs authenticated

## Main API Endpoints

- Auth:
  - POST /api/register
  - POST /api/login
- Pizzas:
  - GET /api/pizzas
- Orders Checkout:
  - POST /api/orders/checkout