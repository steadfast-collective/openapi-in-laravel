<?php

namespace Tests\Feature;

use Tests\Feature\Concerns\HasValidatingJsonMethods;
use Tests\TestCase;

/**
 * This is a version of the ManualValidationTest which uses the HasValidatingJsonMethods
 * trait to automatically test every request against our OpenAPI Spec.
 *
 * This removes the need to remember to test it every time, and greatly
 * improves the quality if your tests.
 */
class AutomaticValidationTest extends TestCase
{
    use HasValidatingJsonMethods;

    /**
     * Check that our API response returns the name of a pet.
     */
    public function test_it_can_read_pet_name(): void
    {
        // Make our request as usual - because of the HasValidatingJsonMethods
        // both the Request and Response will be automatically validated
        // against the OpenAPI Specification as part of of the getJson
        // call.
        $response = $this->getJson('/api/pet-name');

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

        // We never reach here, because the OAS Validator threw an exception first
        $response->assertStatus(201);
    }

    /**
     * This is a version of test_it_can_add_pet_name which demonstrates how to
     * bypass validation.
     *
     * This is required when you want to test your APIs responses to invalid data.
     */
    public function test_it_can_add_pet_name_without_validation(): void
    {
        // Make the response without checking against the OpenAPI file.
        $response = $this->withoutApiValidation(function () {
            return $this->putJson('/api/pet-name', [
                'pet-name' => 'Charlie',
            ]);
        });

        $this->assertSame([
            'message' => 'The name field is required.',
            'errors' => [
                'name' => ['The name field is required.'],
            ],
        ], $response->json());

        $response->assertStatus(422);
    }
}
