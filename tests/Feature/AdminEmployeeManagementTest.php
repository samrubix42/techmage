<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('admin can access employee management page', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $this->actingAs($admin)
        ->get(route('admin.employees'))
        ->assertStatus(200)
        ->assertSee('Employee Directory');
});

test('non-admin employee cannot access employee management page', function () {
    $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);

    $this->actingAs($employee)
        ->get(route('admin.employees'))
        ->assertRedirect(route('employee.dashboard'));
});

test('admin can open create modal and create an employee', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    $department = Department::create(['name' => 'Engineering', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test('admin::employee-management')
        ->assertSet('showCreateModal', false)
        ->call('openCreateModal')
        ->assertSet('showCreateModal', true)
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('password', 'password123')
        ->set('role', 'employee')
        ->set('department_id', $department->id)
        ->set('is_active', true)
        ->call('createEmployee')
        ->assertSet('showCreateModal', false)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => 'employee',
        'department_id' => $department->id,
        'is_active' => true,
    ]);
});

test('admin can open edit modal and update employee details', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $employee = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
        'role' => 'employee',
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test('admin::employee-management')
        ->call('openEditModal', $employee->id)
        ->assertSet('showEditModal', true)
        ->assertSet('name', 'Original Name')
        ->assertSet('email', 'original@example.com')
        ->set('name', 'Updated Name')
        ->set('email', 'updated@example.com')
        ->call('updateEmployee')
        ->assertSet('showEditModal', false)
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'id' => $employee->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);
});

test('admin can toggle employee active status', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $employee = User::factory()->create([
        'role' => 'employee',
        'is_active' => true,
    ]);

    Livewire::actingAs($admin)
        ->test('admin::employee-management')
        ->call('toggleStatus', $employee->id);

    expect($employee->fresh()->is_active)->toBeFalse();
});

test('admin can open delete modal and delete an employee', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
    $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);

    Livewire::actingAs($admin)
        ->test('admin::employee-management')
        ->call('openDeleteModal', $employee->id)
        ->assertSet('showDeleteModal', true)
        ->call('deleteEmployee')
        ->assertSet('showDeleteModal', false);

    $this->assertDatabaseMissing('users', [
        'id' => $employee->id,
    ]);
});
