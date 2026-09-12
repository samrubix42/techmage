<div class="space-y-6" @keydown.escape.window="$wire.closeModals()">
    <!-- Header Section -->
    <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-light text-primary-dark border border-yellow-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                    Admin Control
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Department Management</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Manage company departments, organizational units, and operational divisions.</p>
        </div>

        <div>
            <button 
                wire:click="openCreateModal" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-md text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 active:scale-95 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 transition-all shadow-sm cursor-pointer"
            >
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Department
            </button>
        </div>
    </div>

    <!-- Flash Notifications -->
    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-md p-4 flex items-center justify-between shadow-xs transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold">{{ session('message') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 p-1 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-md p-4 flex items-center justify-between shadow-xs transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold">{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 p-1 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Toolbar Filters & Search -->
    <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search department by name or slug..." 
                class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-colors"
            />
            @if(!empty($search))
                <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @endif
        </div>

        <div class="flex items-center gap-3">
            <!-- Status Filter -->
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status:</label>
                <select 
                    wire:model.live="statusFilter" 
                    class="border border-slate-300 rounded-md text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white"
                >
                    <option value="">All Statuses</option>
                    <option value="active">Active Only</option>
                    <option value="inactive">Inactive Only</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Departments Table Container -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden relative">
        <div wire:loading.flex class="absolute inset-0 bg-white/70 backdrop-blur-xs z-10 items-center justify-center transition-all">
            <div class="flex items-center gap-2 text-slate-700 font-semibold text-sm">
                <svg class="animate-spin w-5 h-5 text-slate-900" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading departments...
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700">
                <thead class="text-xs uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold">Department Name</th>
                        <th class="px-6 py-3.5 font-semibold">Slug Identifier</th>
                        <th class="px-6 py-3.5 font-semibold">Status</th>
                        <th class="px-6 py-3.5 font-semibold">Created Date</th>
                        <th class="px-6 py-3.5 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($departments as $department)
                        <tr wire:key="dept-{{ $department->id }}" class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-md bg-slate-900 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                                        {{ strtoupper(substr($department->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $department->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-600">
                                {{ $department->slug }}
                            </td>
                            <td class="px-6 py-4">
                                @if($department->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $department->created_at ? $department->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- View Button -->
                                    <button 
                                        wire:click="openViewModal({{ $department->id }})"
                                        title="View Details"
                                        class="p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-md transition-colors cursor-pointer"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>

                                    <!-- Edit Button -->
                                    <button 
                                        wire:click="openEditModal({{ $department->id }})"
                                        title="Edit Department"
                                        class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md transition-colors cursor-pointer"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    <!-- Toggle Status Button -->
                                    <button 
                                        wire:click="toggleStatus({{ $department->id }})"
                                        title="{{ $department->is_active ? 'Deactivate Department' : 'Activate Department' }}"
                                        class="p-1.5 {{ $department->is_active ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} rounded-md transition-colors cursor-pointer"
                                    >
                                        @if($department->is_active)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </button>

                                    <!-- Delete Button -->
                                    <button 
                                        wire:click="openDeleteModal({{ $department->id }})"
                                        title="Delete Department"
                                        class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-md transition-colors cursor-pointer"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <div class="max-w-xs mx-auto text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M15 16h.01M15 12h.01M9 8h.01M15 8h.01"/>
                                    </svg>
                                    <p class="font-semibold text-slate-700 text-base">No departments found</p>
                                    <p class="text-xs text-slate-500 mt-1">Add a new department or adjust your search filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($departments->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $departments->links() }}
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: CREATE DEPARTMENT MODAL           -->
    <!-- ========================================== -->
    <div 
        x-show="$wire.showCreateModal"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
    >
        <!-- Backdrop -->
        <div 
            x-show="$wire.showCreateModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            wire:click="closeModals" 
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div 
                x-show="$wire.showCreateModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl sm:my-8 sm:w-full sm:max-w-lg border border-slate-200"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-md bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                            +
                        </div>
                        <h3 class="text-base font-bold text-slate-900" id="modal-title">Create New Department</h3>
                    </div>
                    <button wire:click="closeModals" class="text-slate-400 hover:text-slate-600 p-1 rounded-md cursor-pointer transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body / Form -->
                <form wire:submit.prevent="createDepartment">
                    <div class="p-6 space-y-4">
                        <!-- Department Name -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Department Name <span class="text-rose-500">*</span></label>
                            <input 
                                type="text" 
                                wire:model.live="name" 
                                placeholder="Human Resources, Engineering, Marketing..." 
                                class="w-full px-3.5 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 {{ $errors->has('name') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                            />
                            @error('name') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Slug Identifier <span class="text-rose-500">*</span></label>
                                <button type="button" wire:click="generateSlug" class="text-xs text-blue-600 hover:underline">Auto Generate</button>
                            </div>
                            <input 
                                type="text" 
                                wire:model="slug" 
                                placeholder="human-resources" 
                                class="w-full px-3.5 py-2 border rounded-md text-sm font-mono focus:outline-none focus:ring-2 focus:ring-slate-900 {{ $errors->has('slug') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                            />
                            @error('slug') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Department Status</label>
                            <select 
                                wire:model="is_active" 
                                class="w-full px-3.5 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white"
                            >
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('is_active') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
                        <button 
                            type="button" 
                            wire:click="closeModals" 
                            class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 active:scale-95 rounded-md border border-slate-300 transition-all cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 active:scale-95 rounded-md transition-all cursor-pointer shadow-sm"
                        >
                            <span wire:loading.remove wire:target="createDepartment">Save Department</span>
                            <span wire:loading wire:target="createDepartment" class="flex items-center gap-1.5">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: EDIT DEPARTMENT MODAL             -->
    <!-- ========================================== -->
    <div 
        x-show="$wire.showEditModal"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
    >
        <!-- Backdrop -->
        <div 
            x-show="$wire.showEditModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            wire:click="closeModals" 
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div 
                x-show="$wire.showEditModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl sm:my-8 sm:w-full sm:max-w-lg border border-slate-200"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-md bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                            ✎
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Edit Department Details</h3>
                    </div>
                    <button wire:click="closeModals" class="text-slate-400 hover:text-slate-600 p-1 rounded-md cursor-pointer transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body / Form -->
                <form wire:submit.prevent="updateDepartment">
                    <div class="p-6 space-y-4">
                        <!-- Department Name -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Department Name <span class="text-rose-500">*</span></label>
                            <input 
                                type="text" 
                                wire:model="name" 
                                class="w-full px-3.5 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 {{ $errors->has('name') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                            />
                            @error('name') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Slug Identifier <span class="text-rose-500">*</span></label>
                                <button type="button" wire:click="generateSlug" class="text-xs text-blue-600 hover:underline">Regenerate Slug</button>
                            </div>
                            <input 
                                type="text" 
                                wire:model="slug" 
                                class="w-full px-3.5 py-2 border rounded-md text-sm font-mono focus:outline-none focus:ring-2 focus:ring-slate-900 {{ $errors->has('slug') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                            />
                            @error('slug') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Department Status</label>
                            <select 
                                wire:model="is_active" 
                                class="w-full px-3.5 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white"
                            >
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('is_active') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
                        <button 
                            type="button" 
                            wire:click="closeModals" 
                            class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 active:scale-95 rounded-md border border-slate-300 transition-all cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 active:scale-95 rounded-md transition-all cursor-pointer shadow-sm"
                        >
                            <span wire:loading.remove wire:target="updateDepartment">Update Department</span>
                            <span wire:loading wire:target="updateDepartment" class="flex items-center gap-1.5">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Updating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 3: VIEW DEPARTMENT DETAILS MODAL     -->
    <!-- ========================================== -->
    <div 
        x-show="$wire.showViewModal"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
    >
        <!-- Backdrop -->
        <div 
            x-show="$wire.showViewModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            wire:click="closeModals" 
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div 
                x-show="$wire.showViewModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl sm:my-8 sm:w-full sm:max-w-md border border-slate-200"
            >
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Department Overview</h3>
                    <button wire:click="closeModals" class="text-slate-400 hover:text-slate-600 p-1 rounded-md cursor-pointer transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Profile Body -->
                @if ($selectedDepartment)
                    <div class="p-6 space-y-6">
                        <div class="flex items-center gap-4 pb-4 border-b border-slate-100">
                            <div class="w-14 h-14 rounded-md bg-slate-900 text-white flex items-center justify-center font-bold text-xl shadow-xs">
                                {{ strtoupper(substr($selectedDepartment->name, 0, 2)) }}
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-slate-900">{{ $selectedDepartment->name }}</h4>
                                <p class="font-mono text-xs text-slate-500">{{ $selectedDepartment->slug }}</p>
                                <div class="mt-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $selectedDepartment->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                        {{ $selectedDepartment->is_active ? '● Active Department' : '○ Inactive Department' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Data Fields -->
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-xs font-semibold text-slate-500 uppercase">Department ID</span>
                                <span class="font-mono text-slate-800">#{{ $selectedDepartment->id }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-xs font-semibold text-slate-500 uppercase">Created Date</span>
                                <span class="text-slate-800 font-medium">
                                    {{ $selectedDepartment->created_at ? $selectedDepartment->created_at->format('F d, Y H:i') : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-100">
                                <span class="text-xs font-semibold text-slate-500 uppercase">Last Updated</span>
                                <span class="text-slate-800 font-medium">
                                    {{ $selectedDepartment->updated_at ? $selectedDepartment->updated_at->format('F d, Y H:i') : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-between px-6 py-4 bg-slate-50 border-t border-slate-200">
                        <button 
                            wire:click="openEditModal({{ $selectedDepartment->id }})" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 active:scale-95 rounded-md transition-all cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Department
                        </button>
                        <button 
                            wire:click="closeModals" 
                            class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 active:scale-95 rounded-md border border-slate-300 transition-all cursor-pointer"
                        >
                            Close
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 4: DELETE CONFIRMATION MODAL         -->
    <!-- ========================================== -->
    <div 
        x-show="$wire.showDeleteModal"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
    >
        <!-- Backdrop -->
        <div 
            x-show="$wire.showDeleteModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            wire:click="closeModals" 
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div 
                x-show="$wire.showDeleteModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4 sm:translate-y-0"
                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl sm:my-8 sm:w-full sm:max-w-md border border-slate-200"
            >
                @if ($selectedDepartment)
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900">Delete Department</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Permanent organizational unit removal</p>
                            </div>
                        </div>

                        <div class="mt-4 bg-slate-50 p-3 rounded-md border border-slate-200">
                            <p class="text-sm text-slate-700">
                                Are you sure you want to delete department <strong class="text-slate-900">{{ $selectedDepartment->name }}</strong> (<span class="font-mono text-xs text-slate-600">{{ $selectedDepartment->slug }}</span>)?
                            </p>
                            <p class="text-xs text-rose-600 font-medium mt-2">
                                Warning: This action is permanent and cannot be reversed.
                            </p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex items-center justify-end gap-3 px-6 py-4 bg-slate-50 border-t border-slate-200">
                        <button 
                            type="button" 
                            wire:click="closeModals" 
                            class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 active:scale-95 rounded-md border border-slate-300 transition-all cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="button" 
                            wire:click="deleteDepartment" 
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-md transition-all cursor-pointer shadow-sm"
                        >
                            <span wire:loading.remove wire:target="deleteDepartment">Yes, Delete Department</span>
                            <span wire:loading wire:target="deleteDepartment" class="flex items-center gap-1.5">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Deleting...
                            </span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>