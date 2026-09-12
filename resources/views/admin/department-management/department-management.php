<?php

use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Department Management - TechMage')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showViewModal = false;

    public bool $showDeleteModal = false;

    public ?int $departmentId = null;

    public string $name = '';

    public string $slug = '';

    public bool $is_active = true;

    public ?Department $selectedDepartment = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedName(string $value): void
    {
        if (empty($this->slug) || $this->slug === Str::slug(substr($value, 0, -1))) {
            $this->slug = Str::slug($value);
        }
    }

    public function generateSlug(): void
    {
        $this->slug = Str::slug($this->name);
    }

    public function resetForm(): void
    {
        $this->departmentId = null;
        $this->name = '';
        $this->slug = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function closeModals(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showViewModal = false;
        $this->showDeleteModal = false;
        $this->selectedDepartment = null;
        $this->resetForm();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function createDepartment(): void
    {
        if (empty($this->slug) && ! empty($this->name)) {
            $this->slug = Str::slug($this->name);
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:departments,slug'],
            'is_active' => ['boolean'],
        ]);

        Department::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'is_active' => $validated['is_active'],
        ]);

        session()->flash('message', 'Department created successfully.');
        $this->closeModals();
    }

    public function openEditModal(int $id): void
    {
        $this->resetForm();
        $department = Department::findOrFail($id);

        $this->departmentId = $department->id;
        $this->name = (string) $department->name;
        $this->slug = (string) $department->slug;
        $this->is_active = (bool) $department->is_active;

        $this->showEditModal = true;
    }

    public function updateDepartment(): void
    {
        if (empty($this->slug) && ! empty($this->name)) {
            $this->slug = Str::slug($this->name);
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('departments', 'slug')->ignore($this->departmentId)],
            'is_active' => ['boolean'],
        ]);

        $department = Department::findOrFail($this->departmentId);

        $department->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'is_active' => $validated['is_active'],
        ]);

        session()->flash('message', 'Department updated successfully.');
        $this->closeModals();
    }

    public function openViewModal(int $id): void
    {
        $this->selectedDepartment = Department::findOrFail($id);
        $this->showViewModal = true;
    }

    public function openDeleteModal(int $id): void
    {
        $this->selectedDepartment = Department::findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function deleteDepartment(): void
    {
        if (! $this->selectedDepartment) {
            return;
        }

        $this->selectedDepartment->delete();

        session()->flash('message', 'Department deleted successfully.');
        $this->closeModals();
    }

    public function toggleStatus(int $id): void
    {
        $department = Department::findOrFail($id);

        $department->update([
            'is_active' => ! $department->is_active,
        ]);

        session()->flash('message', 'Department status updated successfully.');
    }

    public function render()
    {
        $query = Department::query();

        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        if (trim($this->search) !== '') {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('slug', 'like', '%'.$search.'%');
            });
        }

        $departments = $query->latest()->paginate(10);

        return view('admin.department-management.department-management', [
            'departments' => $departments,
        ]);
    }
};
