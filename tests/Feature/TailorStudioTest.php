<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\TailorOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TailorStudioTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_homepage_opens_for_customers_without_showing_owner_setup(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Something lovely');

        $this->get('/workroom')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('Set up your studio');
        $this->get('/setup')->assertOk()->assertSee('Make it yours.');
    }

    public function test_owner_can_create_an_account_once_and_sign_in(): void
    {
        $this->get('/setup')->assertOk();

        $this->post('/setup', [
            'name' => 'Saim Ali',
            'email' => 'saim@example.com',
            'password' => 'a-very-safe-password',
            'password_confirmation' => 'a-very-safe-password',
        ])->assertRedirect('/workroom');

        $this->assertDatabaseHas('users', ['email' => 'saim@example.com']);
        $this->assertAuthenticated();
        $this->get('/setup')->assertRedirect('/login');

        $this->post('/logout')->assertRedirect('/login');
        $this->post('/login', [
            'email' => 'saim@example.com',
            'password' => 'a-very-safe-password',
        ])->assertRedirect('/workroom');
        $this->assertAuthenticated();
    }

    public function test_customer_can_register_and_place_an_order_with_measurements(): void
    {
        $this->post('/account/register', [
            'name' => 'Amina Khan',
            'gender' => 'female',
            'phone' => '+92 300 1234567',
            'email' => 'amina@example.com',
            'password' => 'a-very-safe-password',
            'password_confirmation' => 'a-very-safe-password',
        ])->assertRedirect('/account');

        $this->assertDatabaseHas('customers', ['name' => 'Amina Khan', 'phone' => '+92 300 1234567']);

        $response = $this->post('/account/orders', [
            'garment' => 'Shalwar kameez',
            'package' => 'everyday',
            'fabric' => 'Cotton',
            'standard_size' => 'custom',
            'measurements' => ['chest' => '38', 'shirt_length' => '42'],
            'style_notes' => 'A relaxed fit, please.',
            'due_date' => today()->addDays(14)->toDateString(),
        ]);

        $response->assertRedirect('/account');
        $this->assertDatabaseHas('tailor_orders', [
            'garment' => 'Shalwar kameez',
            'package' => 'everyday',
            'standard_size' => 'custom',
            'status' => 'new',
            'total' => 0,
            'advance' => 0,
        ]);
    }

    public function test_customer_order_requires_a_size_or_measurements(): void
    {
        $this->post('/account/register', [
            'name' => 'Amina Khan',
            'gender' => 'female',
            'phone' => '+92 300 1234567',
            'password' => 'a-very-safe-password',
            'password_confirmation' => 'a-very-safe-password',
        ]);

        $response = $this->from('/account/order/new')->post('/account/orders', [
            'garment' => 'Shalwar kameez',
            'package' => 'everyday',
            'fabric' => 'Cotton',
            'standard_size' => null,
            'measurements' => [],
            'due_date' => today()->addDays(14)->toDateString(),
        ]);

        $response->assertRedirect('/account/order/new')->assertSessionHasErrors('standard_size');
        $this->assertDatabaseCount('tailor_orders', 0);
    }

    public function test_owner_can_manage_orders_and_customers(): void
    {
        $owner = User::factory()->create(['name' => 'Saim']);
        $customer = Customer::create([
            'name' => 'Amina Khan',
            'phone' => '+92 300 1234567',
            'measurements' => ['chest' => 38],
            'measurement_unit' => 'cm',
        ]);
        $order = $customer->orders()->create([
            'garment' => 'Shalwar kameez',
            'package' => 'everyday',
            'fabric' => 'Cotton',
            'standard_size' => 'M',
            'measurement_unit' => 'in',
            'measurements' => [],
            'status' => 'new',
            'due_date' => today()->addDays(14),
            'total' => 8500,
            'advance' => 2000,
        ]);

        $this->get('/workroom')->assertRedirect('/login');

        $this->actingAs($owner)
            ->get('/workroom')
            ->assertOk()
            ->assertSee('Amina Khan')
            ->assertSee('PKR 6,500 remaining');

        $this->get('/workroom/customers')
            ->assertOk()
            ->assertSee('Amina Khan');

        $this->post('/orders', [
            'customer_id' => $customer->id,
            'garment' => 'Two-piece suit',
            'package' => 'premium',
            'fabric' => 'Cotton',
            'standard_size' => 'M',
            'measurement_unit' => 'cm',
            'total' => 12500,
            'advance' => 3500,
            'due_date' => today()->addDays(14)->toDateString(),
        ])->assertRedirect('/workroom');

        $this->assertDatabaseHas('tailor_orders', [
            'customer_id' => $customer->id,
            'total' => 12500,
            'advance' => 3500,
            'status' => 'confirmed',
        ]);

        $this->patch('/orders/'.$order->id.'/price', [
            'total' => 12000,
            'advance' => 2500,
        ])->assertRedirect();

        $this->assertDatabaseHas('tailor_orders', [
            'id' => $order->id,
            'total' => 12000,
            'advance' => 2500,
        ]);

        $this->patch('/orders/'.$order->id.'/status', ['status' => 'stitching'])
            ->assertRedirect();
        $this->assertDatabaseHas('tailor_orders', ['id' => $order->id, 'status' => 'stitching']);
    }

    public function test_owner_cannot_record_an_advance_greater_than_the_order_price(): void
    {
        $owner = User::factory()->create();

        $this->actingAs($owner)
            ->from('/workroom')
            ->post('/orders', [
                'customer_name' => 'Amina Khan',
                'phone' => '+92 300 1234567',
                'gender' => 'female',
                'garment' => 'Shalwar kameez',
                'package' => 'everyday',
                'fabric' => 'Cotton',
                'standard_size' => 'M',
                'measurement_unit' => 'in',
                'total' => 8500,
                'advance' => 9000,
                'due_date' => today()->addDays(14)->toDateString(),
            ])
            ->assertRedirect('/workroom')
            ->assertSessionHasErrors('advance');

        $this->assertDatabaseCount('tailor_orders', 0);
    }

    public function test_owner_can_quote_a_customer_request_and_update_its_balance(): void
    {
        $owner = User::factory()->create();
        $customer = Customer::create([
            'name' => 'Amina Khan',
            'phone' => '+92 300 1234567',
        ]);
        $order = $customer->orders()->create([
            'garment' => 'Shalwar kameez',
            'package' => 'premium',
            'fabric' => 'Cotton',
            'standard_size' => 'M',
            'measurement_unit' => 'in',
            'measurements' => [],
            'status' => 'new',
            'due_date' => today()->addDays(14),
            'total' => 0,
            'advance' => 0,
        ]);

        $this->actingAs($owner)
            ->get('/workroom')
            ->assertOk()
            ->assertSee('Price to confirm');

        $this->patch('/orders/'.$order->id.'/price', [
            'total' => 14500,
            'advance' => 4500,
        ])->assertRedirect();

        $this->assertDatabaseHas('tailor_orders', [
            'id' => $order->id,
            'total' => 14500,
            'advance' => 4500,
        ]);

        $this->from('/workroom')->patch('/orders/'.$order->id.'/price', [
            'total' => 14500,
            'advance' => 16000,
        ])->assertRedirect('/workroom')->assertSessionHasErrors('advance');

        $this->assertDatabaseHas('tailor_orders', [
            'id' => $order->id,
            'total' => 14500,
            'advance' => 4500,
        ]);
    }
}
