<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('unauthenticated users are redirected to login page', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});

test('admin user is redirected to admin dashboard', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get('/');

    $response->assertRedirect(route('admin.dashboard'));
});

test('employee user is redirected to employee dashboard', function () {
    $employee = User::factory()->create([
        'role' => 'employee',
        'is_active' => true,
    ]);

    $response = $this->actingAs($employee)->get('/');

    $response->assertRedirect(route('employee.dashboard'));
});

test('employee cannot access admin dashboard', function () {
    $employee = User::factory()->create([
        'role' => 'employee',
        'is_active' => true,
    ]);

    $response = $this->actingAs($employee)->get(route('admin.dashboard'));

    $response->assertRedirect(route('employee.dashboard'));
});

test('admin cannot access employee dashboard', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get(route('employee.dashboard'));

    $response->assertRedirect(route('admin.dashboard'));
});

test('login page does not display autofill demo accounts buttons', function () {
    $response = $this->get(route('login'));

    $response->assertSuccessful();
    $response->assertDontSee('Quick Demo Accounts');
    $response->assertDontSee('admin@admin.com');
    $response->assertDontSee('employee@employee.com');
});

test('admin can log in with new password after changing it', function () {
    $admin = User::factory()->create([
        'email' => 'admin_test@example.com',
        'password' => Hash::make('original-password'),
        'role' => 'admin',
        'is_active' => true,
    ]);

    // Admin logs in and changes password
    $this->actingAs($admin);
    Livewire::test('admin::change-password')
        ->set('current_password', 'original-password')
        ->set('password', 'new-brand-new-password')
        ->set('password_confirmation', 'new-brand-new-password')
        ->call('updatePassword')
        ->assertHasNoErrors();

    // Log out
    auth()->logout();

    // Log in with new credentials via login component
    Livewire::test('auth::login')
        ->set('email', 'admin_test@example.com')
        ->set('password', 'new-brand-new-password')
        ->call('login')
        ->assertRedirect(route('admin.dashboard'));

    expect(auth()->check())->toBeTrue();
    expect(auth()->user()->id)->toBe($admin->id);
});
