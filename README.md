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

## Example Test Failure
If you make a request (or return a response) in one of your tests which isn't documented in your OpenAPI Specification file then your test will fail and tell you exactly why:

```
FAILED  Tests\Feature\ManualValidationTest > it can add pet name   ValidationException

Body does not match schema for content-type "application/json" for Request [put /api/pet-name]: Keyword validation failed: Required property 'name' must be present in the object Field: name

at vendor/osteel/openapi-httpfoundation-testing/src/Exceptions/ValidationException.php:27
    23▕                 $message .= sprintf(' Field: %s', implode('.', $breadCrumb->buildChain()));
    24▕             }
    25▕         }
    26▕ 
➜  27▕         return new ValidationException($message, 0, $previous);
    28▕     }
    29▕ }
    30▕ 

    +16 vendor frames 
17  tests/Feature/ManualValidationTest.php:58


Tests:    1 failed (0 assertions)
Duration: 0.22s
```

## See Also
 - [Ostel's HttpFoundation Testing exmple](https://github.com/osteel/openapi-httpfoundation-testing-laravel-example)
