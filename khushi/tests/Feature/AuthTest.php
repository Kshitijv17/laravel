<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_user_can_register()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', [
            'email' => $userData['email']
        ]);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password'
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_admin_can_login()
    {
        $admin = Admin::factory()->create([
            'password' => bcrypt('password123')
        ]);

        $response = $this->post(route('admin.login'), [
            'email' => $admin->email,
            'password' => 'password123'
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_two_factor_authentication_setup()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('two-factor.enable'));

        $response->assertStatus(200);
        $this->assertNotNull($user->fresh()->two_factor_secret);
    }

    public function test_rate_limiting_blocks_excessive_login_attempts()
    {
        $user = User::factory()->create();

        // Make 6 failed login attempts (exceeding the limit)
        for ($i = 0; $i < 6; $i++) {
            $this->post(route('login'), [
                'email' => $user->email,
                'password' => 'wrong-password'
            ]);
        }

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password'
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('logout'));

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_admin_dashboard_requires_admin_auth()
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_dashboard_requires_user_auth()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }
}
