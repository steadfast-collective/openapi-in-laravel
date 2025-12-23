<?php

namespace Tests\Feature\Concerns;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use League\OpenAPIValidation\Schema\Exception\InvalidSchema;
use Osteel\OpenApi\Testing\Exceptions\ValidationException;
use Osteel\OpenApi\Testing\ValidatorBuilder;

/**
 * Adds custom wrappers around the $this->json() methods which also automatically validate
 * the requests against our OpenAPI schema.
 *
 * @mixin \Tests\TestCase
 */
trait HasValidatingJsonMethods
{
    public bool $apiValidationEnabled = true;

    /**
     * Override the json method to always validate against the spec.
     */
    public function json($method, $uri, array $data = [], array $headers = [], $options = 0)
    {
        $response = parent::json(
            $method,
            $uri,
            $data, $headers, $options
        );

        $this->assertApiTestResponseMatchesSpec($response);

        return $response;
    }

    public function getApiValidator()
    {
        static $validator;

        if (! isset($validator)) {
            $validator = ValidatorBuilder::fromYamlFile(
                base_path('resources/api-docs/example-api.yaml')
            )->getValidator();
        }

        return $validator;
    }

    /**
     * After making a test request, validate the request and response against the
     * OpenAPI Specification.
     */
    public function assertApiTestResponseMatchesSpec(TestResponse $testResponse): self
    {
        /** @var \Illuminate\Http\Request $request */
        $request = $testResponse->baseRequest;
        /** @var \Illuminate\Http\JsonResponse $response */
        $response = $testResponse->baseResponse;

        if ($this->apiValidationEnabled == false) {
            return $this;
        }
        $route = Route::current();

        throw_unless(
            $route->matches($request),
            'Unable to automatically detect the current route for OpenAPI Test',
        );

        $method = strtolower($request->getMethod());

        // Since Sanctum's test methods don't use the auth header, we have to
        // inject them to pass the validation.
        $addedAuthHeader = false;
        if (! $request->hasHeader('Authorization')) {
            $request->headers->add(['Authorization' => 'secret_key']);
            $addedAuthHeader = true;
        }

        try {
            $this->getApiValidator()->$method($request, $route->uri());
        } catch (ValidationException|InvalidSchema $e) {
            // In case of a failure, dump the body to help with debugging
            // TODO: Find a prettier way of outputting this.
            fwrite(STDERR, "Request JSON payload:\n\n".print_r($request->json(), true)."\n");
            throw $e;
        }
        try {
            $this->getApiValidator()->$method($response, $route->uri());
        } catch (ValidationException|InvalidSchema $e) {
            // In case of a failure, dump the body to help with debugging
            // TODO: Find a prettier way of outputting this.
            fwrite(STDERR, "Response JSON payload:\n\n".print_r($response->getData(), true)."\n");
            throw $e;
        }

        // Tidy up after ourselves
        if ($addedAuthHeader) {
            $testResponse->baseRequest->headers->remove('Authorization');
        }

        return $this;
    }

    /**
     * Run a closure without validating against the API spec.
     *
     * Example usage:
     *
     *   $response = $this->withoutApiValidation(
     *       fn() => $this->putJson('/api/products', $body)
     *   );
     */
    public function withoutApiValidation(Closure $callback)
    {
        $this->apiValidationEnabled = false;
        try {
            return $callback();
        } finally {
            $this->apiValidationEnabled = true;
        }
    }
}
