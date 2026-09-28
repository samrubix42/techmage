<?php

use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\DailySlotTracking;
use App\Models\DailyTaskReport;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.employee')] #[Title('Daily Task Report - TechMage')] class extends Component
{
    public ?int $reportId = null;

    public string $date = '';

    /**
     * Array of project entries
     * Each item: ['_key' => string, 'title' => string, 'description' => string]
     *
     * @var array<int, array{_key: string, title: string, description: string}>
     */
    public array $projects = [];

    public bool $isEdit = false;

    public function mount(?DailyTaskReport $report = null): void
    {
        if ($report && $report->exists) {
            if ($report->user_id !== Auth::id()) {
                abort(403);
            }

            $this->isEdit = true;
            $this->reportId = $report->id;
            $this->date = $report->date ? $report->date->format('Y-m-d') : now()->toDateString();

            $items = is_array($report->title_description) ? $report->title_description : [];
            $this->projects = array_map(function ($item) {
                return [
                    '_key' => uniqid('proj_'),
                    'title' => $item['title'] ?? '',
                    'description' => $item['description'] ?? '',
                ];
            }, $items);

            if (empty($this->projects)) {
                $this->addProject();
            }
        } else {
            $this->date = now()->toDateString();
            $this->addProject();
        }
    }

    public function addProject(): void
    {
        $this->projects[] = [
            '_key' => uniqid('proj_'),
            'title' => '',
            'description' => '',
        ];
    }

    public function removeProject(int $index): void
    {
        if (count($this->projects) > 1) {
            unset($this->projects[$index]);
            $this->projects = array_values($this->projects);
        }
    }

    public function save()
    {
        $this->validate([
            'date' => 'required|date',
            'projects' => 'required|array|min:1',
            'projects.*.title' => 'required|string|max:255',
            'projects.*.description' => 'required|string|min:3',
        ], [
            'date.required' => 'Please select a date for the report.',
            'projects.required' => 'At least one project report entry is required.',
            'projects.*.title.required' => 'Project title is required.',
            'projects.*.description.required' => 'Project description is required.',
            'projects.*.description.min' => 'Project description must be at least 3 characters.',
        ]);

        $titleDescription = array_map(function ($project) {
            return [
                'title' => trim($project['title']),
                'description' => trim($project['description']),
            ];
        }, $this->projects);

        if ($this->isEdit && $this->reportId) {
            $report = DailyTaskReport::where('user_id', Auth::id())->findOrFail($this->reportId);
            $report->update([
                'date' => $this->date,
                'title_description' => $titleDescription,
            ]);

            $message = 'Daily Task Report updated successfully!';
        } else {
            DailyTaskReport::create([
                'user_id' => Auth::id(),
                'date' => $this->date,
                'title_description' => $titleDescription,
            ]);

            $message = 'Daily Task Report submitted successfully!';
        }

        // If report is for today, sync 4th slot check-in & clock out shift
        if ($this->date === now()->toDateString()) {
            $user = Auth::user();
            if ($user) {
                $now = now();
                $tracking = DailySlotTracking::where('user_id', $user->id)
                    ->whereDate('tracking_date', $now->toDateString())
                    ->first();

                if ($tracking && $tracking->slot3_start_time && ! $tracking->slot4_checkin_time) {
                    $tracking->update([
                        'slot3_end_time' => $tracking->slot3_end_time ?? $now,
                        'slot4_checkin_time' => $now,
                        'slot4_timing_status' => 'normal',
                        'slot4_is_flagged' => false,
                    ]);

                    $attendance = Attendance::where('user_id', $user->id)
                        ->whereDate('attendance_date', $now->toDateString())
                        ->first();

                    if ($attendance) {
                        $attendance->update(['clock_out_time' => $now]);

                        $activeLog = AttendanceLog::where('attendance_id', $attendance->id)
                            ->whereNull('clock_out_time')
                            ->first();

                        if ($activeLog) {
                            $clockIn = $activeLog->clock_in_time;
                            $duration = $clockIn ? (int) $clockIn->diffInMinutes($now) : 0;
                            $activeLog->update([
                                'clock_out_time' => $now,
                                'duration_minutes' => $duration,
                            ]);
                        }
                    }
                }
            }
        }

        $this->dispatch('toast-show', [
            'message' => $message,
            'type' => 'success',
            'position' => 'top-right',
        ]);

        session()->flash('toast', [
            'message' => $message,
            'type' => 'success',
            'position' => 'top-right',
        ]);

        return redirect()->route('employee.daily-task-report-list');

    }

    public function render()
    {
        return view('employee.daily-task-report-create.daily-task-report-create');
    }
};
