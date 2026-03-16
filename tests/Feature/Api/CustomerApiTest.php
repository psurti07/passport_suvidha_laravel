<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_customers()
    {
        Customer::factory()->count(3)->create();
        $response = $this->getJson('/api/customers');
        $response->assertOk();
        $response->assertJsonStructure(['data', 'links', 'meta']);
    }

    /** @test */
    public function it_can_create_a_customer_with_minimum_fields()
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'mobile_number' => '1234567890',
            'email' => 'john@example.com',
        ];
        $response = $this->postJson('/api/customers', $data);
        $response->assertCreated();
        $this->assertDatabaseHas('customers', ['email' => 'john@example.com']);
    }

    /** @test */
    public function it_can_show_a_customer()
    {
        $customer = Customer::factory()->create();
        $response = $this->getJson('/api/customers/' . $customer->id);
        $response->assertOk();
        $response->assertJson(['id' => $customer->id]);
    }

    /** @test */
    public function it_can_update_a_customer()
    {
        $customer = Customer::factory()->create();
        $data = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'mobile_number' => $customer->mobile_number,
            'email' => 'jane@example.com',
        ];
        $response = $this->putJson('/api/customers/' . $customer->id, $data);
        $response->assertOk();
        $this->assertDatabaseHas('customers', ['email' => 'jane@example.com']);
    }

    /** @test */
    public function it_can_delete_a_customer()
    {
        $customer = Customer::factory()->create();
        $response = $this->deleteJson('/api/customers/' . $customer->id);
        $response->assertNoContent();
        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }

    /** @test */
    public function it_validates_required_fields_on_create()
    {
        $response = $this->postJson('/api/customers', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['first_name', 'last_name', 'mobile_number', 'email']);
    }

    /** @test */
    public function it_prevents_duplicate_email_on_create()
    {
        $customer = Customer::factory()->create(['email' => 'john@example.com']);
        $data = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'mobile_number' => '1234567890',
            'email' => 'john@example.com',
        ];
        $response = $this->postJson('/api/customers', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function it_can_register_customer_stepwise()
    {
        // Step 1: Create customer (basic info)
        $data = [
            'first_name' => 'Step',
            'last_name' => 'User',
            'mobile_number' => '9998887777',
            'email' => 'stepuser@example.com',
        ];
        $response = $this->postJson('/api/customers/create', $data);
        $response->assertCreated();
        $customerId = $response->json('customer.id');
        $this->assertDatabaseHas('customers', ['id' => $customerId, 'registration_step' => 1]);

        // Simulate OTP verification (set registration_step to 2)
        \App\Models\Customer::where('id', $customerId)->update(['registration_step' => 2]);
        $customer = \App\Models\Customer::find($customerId);

        // Step 2: Add additional info (requires auth, so acting as customer)
        $this->actingAs($customer, 'sanctum');
        $addInfo = [
            'address' => '123 Main St',
            'pin_code' => '123456',
            'city' => 'Testville',
            'state' => 'Teststate',
            'gender' => 'male',
            'date_of_birth' => '1990-01-01',
            'place_of_birth' => 'Testcity',
        ];
        $response = $this->postJson('/api/customers/add-additional-info', $addInfo);
        $response->assertOk();
        $this->assertDatabaseHas('customers', ['id' => $customerId, 'registration_step' => 3]);

        // Step 3: Select service
        $selectService = [
            'service_code' => 'SERVICE123',
        ];
        $response = $this->postJson('/api/customers/select-service', $selectService);
        $response->assertOk();
        $this->assertDatabaseHas('customers', ['id' => $customerId, 'registration_step' => 4, 'service_code' => 'SERVICE123']);

        // Step 4: Login (should succeed if registration_step is 4)
        $loginData = [
            'mobile_number' => '9998887777',
        ];
        $response = $this->postJson('/api/customers/login', $loginData);
        $response->assertOk();
        $response->assertJsonFragment(['next_step' => 'otp_verification']);
    }

    // Registration flow: create, add info, select service, login
    // Add more tests as needed for each step of the registration process
} 