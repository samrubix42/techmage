<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('guest cannot access admin change password page', function () {
    $response = $this->get(route('admin.change-password'));

    $response->assertRedirect(route('login'));
});

test('employee cannot access admin change password page', function () {
    $employee = User::factory()->create([
        'role' => 'employee',
        'is_active' => true,
    ]);

    $response = $this->actingAs($employee)->get(route('admin.change-password'));

    $response->assertRedirect(route('employee.dashboard'));
});

test('admin can view change password page', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.change-password'));

    $response->assertSuccessful();
    $response->assertSee('Change Password');
    $response->assertSee('Account Security');
});

test('admin cannot change password with incorrect current password', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
        'password' => Hash::make('old-password-123'),
    ]);

    $this->actingAs($admin);

    Livewire::test('admin::change-password')
        ->set('current_password', 'wrong-current-password')
        ->set('password', 'new-secure-password-456')
        ->set('password_confirmation', 'new-secure-password-456')
        ->call('updatePassword')
        ->assertHasErrors(['current_password']);

    expect(Hash::check('old-password-123', $admin->fresh()->password))->toBeTrue();
});

test('admin cannot change password when new password is less than 8 characters', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
        'password' => Hash::make('old-password-123'),
    ]);

    $this->actingAs($admin);

    Livewire::test('admin::change-password')
        ->set('current_password', 'old-password-123')
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('updatePassword')
        ->assertHasErrors(['password']);
});

test('admin cannot change password when confirmation does not match', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
        'password' => Hash::make('old-password-123'),
    ]);

    $this->actingAs($admin);

    Livewire::test('admin::change-password')
        ->set('current_password', 'old-password-123')
        ->set('password', 'new-secure-password-456')
        ->set('password_confirmation', 'mismatched-password')
        ->call('updatePassword')
        ->assertHasErrors(['password']);
});

test('admin cannot change password when new password is same as current password', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
        'password' => Hash::make('old-password-123'),
    ]);

    $this->actingAs($admin);

    Livewire::test('admin::change-password')
        ->set('current_password', 'old-password-123')
        ->set('password', 'old-password-123')
        ->set('password_confirmation', 'old-password-123')
        ->call('updatePassword')
        ->assertHasErrors(['password']);
});

test('admin can successfully change password', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_active' => true,
        'password' => Hash::make('old-password-123'),
    ]);

    $this->actingAs($admin);

    Livewire::test('admin::change-password')
        ->set('current_password', 'old-password-123')
        ->set('password', 'new-super-secret-password')
        ->set('password_confirmation', 'new-super-secret-password')
        ->call('updatePassword')
        ->assertHasNoErrors()
        ->assertDispatched('toast-show')
        ->assertSet('current_password', '')
        ->assertSet('password', '')
        ->assertSet('password_confirmation', '');

    expect(Hash::check('new-super-secret-password', $admin->fresh()->password))->toBeTrue();
});
