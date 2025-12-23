<?php

namespace Tests\Feature;

use Osteel\OpenApi\Testing\ValidatorBuilder;
use Tests\TestCase;

/**
 * This is an example of using the ValidtorBuilder to create a one-off
 * OpenAPI Validator to use in a single test.
 */
class ManualValidationTest extends TestCase
{
    /**
     * Check that our API response returns the name of a pet.
     */
    public function test_it_can_read_pet_name(): void
    {
        // Make our request as usual
        $response = $this->getJson('/api/pet-name');

        // Create the validator, and check that both our request and response
        // match the OpenAPI Specification:
        $apiValidator = ValidatorBuilder::fromYamlFile(
            base_path('resources/api-docs/example-api.yaml')
        )->getValidator();
        $apiValidator->get($response->baseRequest, '/api/pet-name');
        $apiValidator->get($response->baseResponse, '/api/pet-name');

        // Perform any other validations we wish
        $response->assertStatus(200);
    }

    /**
     * Check that our API allows us to add a pet name to the list
     *
     * This is an example of a failing test - the /planet-name endpoint
     * does not exist in our example-api.yaml so the test will fail with the
     * error:
     *      Body does not match schema for content-type "application/json" for
     *      Request [put /api/pet-name]: Keyword validation failed: Required property
     *      'name' must be present in the object Field: name"
     */
    public function test_it_can_add_pet_name(): void
    {
        // Make a response but get the property name wrong.
        $response = $this->putJson('/api/pet-name', [
            'pet-name' => 'Charlie',
        ]);

        // Run it through the OpenAPI Specification validator.
        $apiValidator = ValidatorBuilder::fromYamlFile(
            base_path('resources/api-docs/example-api.yaml')
        )->getValidator();

        // These will throw a ValidationException to alert you to a problem
        // with your OAS file code before going any further
        $apiValidator->put($response->baseRequest, '/api/pet-name');
        $apiValidator->put($response->baseResponse, '/api/pet-name');

        // We never reach here, because the OAS Validator threw an exception first
        $response->assertStatus(201);
    }
}
