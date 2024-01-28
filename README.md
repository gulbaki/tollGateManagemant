<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Payment Processing System

This project handles payment processing and entry tracking for a system that integrates with Tag-QR payment services. It provides an API for initiating payments, validating requests, and creating entries within a transactional system.

## Features

- Validate payment requests using a secure hash.
- Create transaction entries after successful payment validation.
- Callback handling for both success and failure cases.

## Installation

To install the project, follow these steps:

1. Clone the repository: git clone https://github.com/your-username/your-project-name.git


2. Install dependencies:

3. Copy `.env.example` to `.env` and configure your environment variables:

4. Generate an application key:

5. Run migrations: `php artisan migrate`

6.  Seed the database: `php artisan db:seed`

## Handling Callbacks
The system will automatically send a callback to the provided success or failure URL, including the transaction hash in the request.


## Testing

`php artisan test`


## Usage

### Note!
Since we do not create users, it can be tested on a single user with the static information provided!

To initiate a payment process, make a POST request to the `/api/payment/process` endpoint with the following payload:

```json
{
"price": "55.33",
"user_id": "3b43544b-10b4-45e2-ab81-2b2b3f1917e8",
"callback_success_url": "https://case.altpay.dev/success",
"callback_fail_url": "https://case.altpay.dev/fail",
"hash": "2918f946ce80bd37e7dbf4ade4888df9d281de0d"
}```
