<?php

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin')] #[Title('Employee Management - TechMage')] class extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $roleFilter = 'all';

    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public bool $showViewModal = false;

    public bool $showDeleteModal = false;

    public ?int $employeeId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public bool $is_active = true;

    public string $role = 'employee';

    public ?int $department_id = null;

    public ?User $selectedEmployee = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function resetForm(): void
    {
        $this->employeeId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->is_active = true;
        $this->role = 'employee';
        $this->department_id = null;
        $this->resetErrorBag();
    }

    public function closeModals(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showViewModal = false;
        $this->showDeleteModal = false;
        $this->selectedEmployee = null;
        $this->resetForm();
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function createEmployee(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'is_active' => ['boolean'],
            'role' => ['required', 'in:employee,admin'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_active' => $validated['is_active'],
            'role' => $validated['role'],
            'department_id' => $validated['department_id'] ?: null,
            'email_verified_at' => now(),
        ]);

        $this->dispatch('toast-show', [
            'message' => 'Employee created successfully.',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        session()->flash('message', 'Employee created successfully.');
        $this->closeModals();
    }

    public function openEditModal(int $id): void
    {
        $this->resetForm();
        $employee = User::findOrFail($id);

        $this->employeeId = $employee->id;
        $this->name = $employee->name;
        $this->email = $employee->email;
        $this->is_active = (bool) $employee->is_active;
        $this->role = $employee->role;
        $this->department_id = $employee->department_id;
        $this->password = '';

        $this->showEditModal = true;
    }

    public function updateEmployee(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->employeeId)],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['boolean'],
            'role' => ['required', 'in:employee,admin'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        $employee = User::findOrFail($this->employeeId);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $validated['is_active'],
            'role' => $validated['role'],
            'department_id' => $validated['department_id'] ?: null,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $employee->update($updateData);

        $this->dispatch('toast-show', [
            'message' => 'Employee updated successfully.',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        session()->flash('message', 'Employee updated successfully.');
        $this->closeModals();
    }

    public function openViewModal(int $id): void
    {
        $this->selectedEmployee = User::with('department')->findOrFail($id);
        $this->showViewModal = true;
    }

    public function openDeleteModal(int $id): void
    {
        $this->selectedEmployee = User::with('department')->findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function deleteEmployee(): void
    {
        if (! $this->selectedEmployee) {
            return;
        }

        if ($this->selectedEmployee->id === auth()->id()) {
            $this->dispatch('toast-show', [
                'message' => 'You cannot delete your own logged-in admin account.',
                'type' => 'danger',
                'position' => 'top-right',
            ]);
            session()->flash('error', 'You cannot delete your own logged-in admin account.');
            $this->closeModals();

            return;
        }

        $this->selectedEmployee->delete();

        $this->dispatch('toast-show', [
            'message' => 'Employee deleted successfully.',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        session()->flash('message', 'Employee deleted successfully.');
        $this->closeModals();
    }

    public function toggleStatus(int $id): void
    {
        $employee = User::findOrFail($id);

        if ($employee->id === auth()->id()) {
            $this->dispatch('toast-show', [
                'message' => 'You cannot deactivate your own logged-in admin account.',
                'type' => 'danger',
                'position' => 'top-right',
            ]);
            session()->flash('error', 'You cannot deactivate your own logged-in admin account.');

            return;
        }

        $employee->update([
            'is_active' => ! $employee->is_active,
        ]);

        $this->dispatch('toast-show', [
            'message' => 'Employee status updated successfully.',
            'type' => 'success',
            'position' => 'top-right',
        ]);

        session()->flash('message', 'Employee status updated successfully.');
    }

    public function render()
    {
        $query = User::with('department');

        if ($this->roleFilter !== 'all') {
            $query->where('role', $this->roleFilter);
        }

        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        if (trim($this->search) !== '') {
            $search = trim($this->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }

        $employees = $query->latest()->paginate(10);
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('admin.employee-management.employee-management', [
            'employees' => $employees,
            'departments' => $departments,
        ]);
    }
};
