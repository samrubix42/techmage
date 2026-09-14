<?php

use App\Models\DailyTaskReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('employee can view daily task reports page', function () {
    $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);

    $this->actingAs($employee)
        ->get(route('employee.daily-task-report-list'))
        ->assertStatus(200)
        ->assertSee('Daily Task Reports');
});

test('employee can view add daily task report page', function () {
    $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);

    $this->actingAs($employee)
        ->get(route('employee.daily-task-report-create'))
        ->assertStatus(200)
        ->assertSee('Create Daily Task Report');
});

test('employee can create daily task report with multiple projects', function () {
    $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);

    Livewire::actingAs($employee)
        ->test('employee::daily-task-report-create')
        ->set('date', '2026-09-14')
        ->set('projects', [
            ['_key' => 'proj_1', 'title' => 'Project Alpha API', 'description' => '<p>Completed auth routes.</p>'],
            ['_key' => 'proj_2', 'title' => 'Project Beta Dashboard', 'description' => '<p>Updated statistics cards.</p>'],
        ])
        ->call('save')
        ->assertRedirect(route('employee.daily-task-report-list'));

    $this->assertDatabaseHas('daily_task_reports', [
        'user_id' => $employee->id,
    ]);

    $report = DailyTaskReport::where('user_id', $employee->id)->first();
    expect($report->title_description)->toHaveCount(2);
    expect($report->title_description[0]['title'])->toBe('Project Alpha API');
    expect($report->title_description[1]['title'])->toBe('Project Beta Dashboard');
});

test('employee can edit daily task report', function () {
    $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
    $report = DailyTaskReport::create([
        'user_id' => $employee->id,
        'date' => '2026-09-14',
        'title_description' => [
            ['title' => 'Initial Title', 'description' => '<p>Initial Description</p>'],
        ],
    ]);

    $this->actingAs($employee)
        ->get(route('employee.daily-task-report-edit', $report))
        ->assertStatus(200)
        ->assertSee('Edit Daily Task Report');

    Livewire::actingAs($employee)
        ->test('employee::daily-task-report-create', ['report' => $report])
        ->assertSet('isEdit', true)
        ->assertSet('date', '2026-09-14')
        ->set('projects.0.title', 'Updated Title')
        ->set('projects.0.description', '<p>Updated Description</p>')
        ->call('save')
        ->assertRedirect(route('employee.daily-task-report-list'));

    $report->refresh();
    expect($report->title_description[0]['title'])->toBe('Updated Title');
    expect($report->title_description[0]['description'])->toBe('<p>Updated Description</p>');
});

test('employee can delete daily task report', function () {
    $employee = User::factory()->create(['role' => 'employee', 'is_active' => true]);
    $report = DailyTaskReport::create([
        'user_id' => $employee->id,
        'date' => '2026-09-14',
        'title_description' => [
            ['title' => 'Test Project', 'description' => '<p>Description</p>'],
        ],
    ]);

    Livewire::actingAs($employee)
        ->test('employee::daily-task-report-list')
        ->call('deleteReport', $report->id);

    $this->assertDatabaseMissing('daily_task_reports', [
        'id' => $report->id,
    ]);
});
