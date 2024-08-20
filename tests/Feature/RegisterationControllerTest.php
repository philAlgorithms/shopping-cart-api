<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\Users\Admin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function it_registers_a_buyer()
    {
        $admin = Admin::factory()->create();

        // $this->actingAs($admin, 'admin');

        $response = $this->postJson(
            '/auth/register/buyer', 
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@doe.com',
                'phone_number' => '09033292224',
                'password' => 'pHYSC1234',
                'password_confirmation' => 'pHYSC1234'
            ]
        );

        $response->dump();
        $response->assertCreated();
                //  ->assertJsonPath('data.name', 'unisex clothes');
    }

    /**
     * @test
     */
    public function it_registers_a_vendor()
    {
        $admin = Admin::factory()->create();

        // $this->actingAs($admin, 'admin');

        $response = $this->postJson(
            '/auth/register/vendor', 
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@doe.com',
                'phone_number' => '09033292224',
                'password' => 'pHYSC1234',
                'password_confirmation' => 'pHYSC1234'
            ]
        );

        $response->dump();
        $response->assertCreated();
                //  ->assertJsonPath('data.name', 'unisex clothes');
    }

    /**
     * @test
     */
    public function it_registers_an_admin()
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin');

        $response = $this->postJson(
            '/auth/register/admin', 
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@doe.com',
                'phone_number' => '09033292224',
                'password' => 'pHYSC1234',
                'password_confirmation' => 'pHYSC1234'
            ]
        );

        $response->dump();
        $response->assertCreated();
                //  ->assertJsonPath('data.name', 'unisex clothes');
    }
}
