<?php

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
