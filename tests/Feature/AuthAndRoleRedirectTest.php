<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
