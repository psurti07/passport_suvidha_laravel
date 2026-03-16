<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;
use App\Models\Ticket;
use Laravel\Sanctum\Sanctum;

class SupportTicketApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test authenticated customer can retrieve their own support tickets.
     *
     * @return void
     */
    public function test_authenticated_customer_can_list_their_tickets()
    {
        // Arrange: Create a customer and some tickets for them
        $customer = Customer::factory()->create();
        $tickets = Ticket::factory()->count(3)->create(['customer_id' => $customer->id]);

        // Create tickets for another customer (should not be included in the response)
        $otherCustomer = Customer::factory()->create();
        Ticket::factory()->count(2)->create(['customer_id' => $otherCustomer->id]);

        // Act: Authenticate as the first customer and make the API request
        Sanctum::actingAs($customer, ['*'], 'sanctum'); // Ensure using the correct guard if needed

        $response = $this->getJson('/api/support-tickets'); // Adjust route if necessary

        // Assert: Check the response
        $response->assertOk(); // Check for 200 status code
        $response->assertJsonStructure([ // Check the overall JSON structure
            'data' => [
                '*' => [
                    'id',
                    'subject',
                    'message',
                    'status',
                    'name', // Added based on store method logic
                    'email', // Added based on store method logic
                    // 'customer_id', // Usually not exposed directly in resource
                    'created_at',
                    'updated_at',
                ]
            ],
            'links',
            'meta',
        ]);
        $response->assertJsonCount(3, 'data'); // Ensure only the customer's tickets are returned

        // Optionally, assert specific ticket data is present
        $response->assertJsonFragment(['id' => $tickets->first()->id]);
        $response->assertJsonFragment(['subject' => $tickets->first()->subject]);

        // Optionally, assert that the other customer's tickets are NOT present
        // This might require fetching all ticket IDs from the response and comparing
        $responseTicketIds = collect($response->json('data'))->pluck('id');
        $this->assertNotContains($otherCustomer->tickets->first()->id, $responseTicketIds);
    }

    /**
     * Test unauthenticated user cannot retrieve tickets.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_list_tickets()
    {
        // Act: Make the API request without authentication
        $response = $this->getJson('/api/support-tickets');

        // Assert: Check for 401 Unauthorized status
        $response->assertUnauthorized();
    }

    // TODO: Add tests for store, show (when implemented), etc.
} 