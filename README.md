# OpenAPI in Laravel

This is an example repository to demonstrate how we use OpenAPI at [Steadfast Collective](https://www.steadfastcollective.com).

## Compatibility
This example uses:
 - Laravel 12
 - PHP 8.4

## Setup
To run the examples locally:

```bash
git clone git@github.com:steadfast-collective/openapi-in-laravel.git
cd openapi-in-laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan test
```

You should see 2 tests fail and 4 tests pass.

# OpenAPI Specification Testing
We use [openapi-httpfoundation-testing](https://github.com/osteel/openapi-httpfoundation-testing) to validate our OpenAPI Specification file against the Requests and Responses in our tests.

This helps keep our documentation in line with our implementation.

## See Also
 - [Ostel's HttpFoundation Testing exmple](https://github.com/osteel/openapi-httpfoundation-testing-laravel-example)
