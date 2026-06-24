<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutIdentityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_see_identity_gateway_when_not_logged_in()
    {
        $response = $this->get(route('checkout.index'));

        $response->assertStatus(200);
        $response->assertSee('Enter your email to continue');
        $response->assertSee('Continue without email');
    }

    public function test_existing_user_email_logs_in_and_redirects_to_checkout()
    {
        $user = User::factory()->customer()->create([
            'email' => 'john.doe@example.com',
            'name' => 'John Doe',
        ]);

        $response = $this->post(route('checkout.identify'), [
            'email' => 'john.doe@example.com',
        ]);

        $response->assertRedirect(route('checkout.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_new_email_creates_guest_user_and_logs_in()
    {
        $response = $this->post(route('checkout.identify'), [
            'email' => 'newbuyer@example.com',
        ]);

        $response->assertRedirect(route('checkout.index'));
        $this->assertAuthenticated();

        $user = User::where('email', 'newbuyer@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->is_guest);
    }

    public function test_continue_without_email_creates_guest_user_and_logs_in()
    {
        $response = $this->post(route('checkout.identify'), [
            'continue_without_email' => '1',
        ]);

        $response->assertRedirect(route('checkout.index'));
        $response->assertSessionHas('guest_message', 'Guest checkout account created. Continue with your shipping details.');
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertTrue($user->is_guest);
        $this->assertMatchesRegularExpression('/^guest_\d+_[a-z0-9]{8}@jeanzo\.in$/', $user->email);
    }
}
