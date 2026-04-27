# OpenAPI in Laravel

This is an example repository to demonstrate how we use OpenAPI at [Steadfast Collective](https://www.steadfastcollective.com).


It showcases two techniques we use:
 - OpenAPI Specification Testing
 - OpenAPI Document Generation


There is an acompanying blog post here: [https://steadfastcollective.com/articles/api-design-with-laravel-for-third-parties-you-dont-control](https://steadfastcollective.com/articles/api-design-with-laravel-for-third-parties-you-dont-control)

## Compatibility
This example uses:
 - Laravel 12
 - PHP 8.4
 - OpenAPI Specification v3.1.0 in YAML

## Getting Started
Our OpenAPI file is located in: [resources/api-docs/example-api.yaml](resources/api-docs/example-api.yaml)

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

To build the API Docs locally you also need to install `redocly`:

```bash
npm install @redocly/cli -g
npm run lint-api-docs
npm run build-api-docs
```

You should see several linting errors, and viewable documentation will be generated in your public/build directory

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
# OpenAPI Documentation Generation
This repository also demonstrates our use of [redocly-cli](https://github.com/Redocly/redoc).

We consider our OpenAPI Specification file similar to an asset, so we store it in the `resources/` directory and build our docs along with any other front-end assets.

Make sure you've installed redocly-cli: `npm install @redocly/cli -g`

Run `npm run lint-api-docs` to check your API docs are valid. Use this in your CI pipeline or GitHub Actions.

Run `npm run build-api-docs` to build your user-facing API documentation in `build/api-docs.html`. Use this in your deployment script.

## Redocly as a Requirement
As an uncommon requirement, we add this to our `composer.json` to ensure `composer.json` fails if redocly has not been installed:
```json
{
    "post-install-cmd": [
        "echo 'Check that the redocly-cli command is available on the command line'",
        "redocly --version"
    ]
}
```

# See Also
 - [learn.openapis.org](https://learn.openapis.org/) - A great starting place for learning to write OpenAPI Specifications
 - [spec.openapis.org](https://spec.openapis.org/) - The official specification for OpenAPI
 - [Ostel's HttpFoundation Testing exmple](https://github.com/osteel/openapi-httpfoundation-testing-laravel-example)
 - [jsonapi.org](https://jsonapi.org/) - Don't re-invent the wheel when designing your API. Someone has already figured out how to do pagination.
 - [Redocly Extension for VS Code](https://marketplace.visualstudio.com/items?itemName=Redocly.openapi-vs-code)
