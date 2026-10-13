<div class="space-y-6" @keydown.escape.window="$wire.closeModals()">
    <!-- Header Section -->
    <div class="bg-white border border-slate-200/80 rounded-md p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    Admin Control
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Holiday Management</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Manage official company holidays, calendar visibility, and track employees working on holiday dates.</p>
        </div>

        <div>
            <button 
                wire:click="openCreateModal" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-md text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 active:scale-95 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 transition-all shadow-sm cursor-pointer"
            >
                <i class="ri-add-line text-lg"></i>
                Add New Holiday
            </button>
        </div>
    </div>

    <!-- Flash Notifications -->
    @if (session()->has('message'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-md p-4 flex items-center justify-between shadow-xs transition-all">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="ri-checkbox-circle-line text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold">{{ session('message') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 p-1 cursor-pointer">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>
    @endif

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Total Holidays in Year -->
        <div class="bg-white border border-slate-200/80 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Holidays in {{ $activeYear }}</span>
                <div class="w-8 h-8 rounded-md bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <i class="ri-calendar-event-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $totalHolidaysCount }}</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">Scheduled for {{ $activeYear }}</p>
        </div>

        <!-- Metric 2: Upcoming Holidays -->
        <div class="bg-white border border-slate-200/80 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Upcoming Holidays</span>
                <div class="w-8 h-8 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i class="ri-time-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $upcomingHolidaysCount }}</div>
            <p class="text-xs text-emerald-600 mt-1 font-medium">Future holidays remaining</p>
        </div>

        <!-- Metric 3: Past Holidays -->
        <div class="bg-white border border-slate-200/80 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Past Holidays</span>
                <div class="w-8 h-8 rounded-md bg-slate-100 text-slate-600 flex items-center justify-center font-bold">
                    <i class="ri-history-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $pastHolidaysCount }}</div>
            <p class="text-xs text-slate-500 mt-1 font-medium">Elapsed holiday dates</p>
        </div>

        <!-- Metric 4: Staff Worked On Holidays -->
        <div class="bg-white border border-slate-200/80 rounded-md p-5 hover:border-slate-300 transition-colors shadow-xs">
            <div class="flex items-center justify-between text-slate-500 mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider">Worked On Holiday</span>
                <div class="w-8 h-8 rounded-md bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <i class="ri-user-star-line text-lg"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900">{{ $workedOnHolidaysThisYearCount }}</div>
            <p class="text-xs text-amber-700 mt-1 font-medium">Employees clocked in on holidays</p>
        </div>
    </div>

    <!-- Toolbar Filters & Search -->
    <div class="bg-white border border-slate-200/80 rounded-md p-4 shadow-xs flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <!-- Search Input -->
        <div class="relative flex-1 max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="ri-search-line text-slate-400"></i>
            </div>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search holiday by title or description..." 
                class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-colors"
            />
            @if(!empty($search))
                <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                    <i class="ri-close-line text-base"></i>
                </button>
            @endif
        </div>

        <!-- Dropdown Filters -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Year Filter -->
            <div class="flex items-center gap-1.5">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Year:</label>
                <select 
                    wire:model.live="yearFilter" 
                    class="border border-slate-300 rounded-md text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white"
                >
                    <option value="">All Years</option>
                    @foreach (range((int) now()->format('Y') - 1, (int) now()->format('Y') + 2) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Month Filter -->
            <div class="flex items-center gap-1.5">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Month:</label>
                <select 
                    wire:model.live="monthFilter" 
                    class="border border-slate-300 rounded-md text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white"
                >
                    <option value="">All Months</option>
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter -->
            <div class="flex items-center gap-1.5">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Type:</label>
                <select 
                    wire:model.live="typeFilter" 
                    class="border border-slate-300 rounded-md text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white"
                >
                    <option value="">All Types</option>
                    <option value="public">Public Holiday</option>
                    <option value="company">Company Holiday</option>
                    <option value="optional">Optional Holiday</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Holidays Table -->
    <div class="bg-white border border-slate-200/80 rounded-md shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Holiday Name & Type</th>
                        <th class="px-6 py-3.5">Date</th>
                        <th class="px-6 py-3.5">Day of Week</th>
                        <th class="px-6 py-3.5">Timeline Status</th>
                        <th class="px-6 py-3.5">Staff Worked</th>
                        <th class="px-6 py-3.5">Description</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse ($holidays as $holiday)
                        @php
                            $dateStr = $holiday->holiday_date->toDateString();
                            $isToday = $dateStr === now()->toDateString();
                            $isPast = $holiday->holiday_date->isPast() && !$isToday;
                            $workerCount = $workedCountsByDate[$dateStr] ?? 0;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Name & Type -->
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                    <span>{{ $holiday->name }}</span>
                                </div>
                                <div class="mt-1">
                                    @if ($holiday->type === 'public')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                            Public Holiday
                                        </span>
                                    @elseif ($holiday->type === 'company')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                                            Company Holiday
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Optional Holiday
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4 font-semibold text-slate-900 whitespace-nowrap">
                                <i class="ri-calendar-line text-slate-400 mr-1"></i>
                                {{ $holiday->holiday_date->format('d M Y') }}
                            </td>

                            <!-- Day of Week -->
                            <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                <span class="font-medium {{ $holiday->holiday_date->isWeekend() ? 'text-amber-700 font-semibold' : '' }}">
                                    {{ $holiday->holiday_date->format('l') }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($isToday)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        Today
                                    </span>
                                @elseif ($isPast)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                        Past Holiday
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Upcoming
                                    </span>
                                @endif
                            </td>

                            <!-- Staff Worked -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($workerCount > 0)
                                    <button 
                                        wire:click="openViewModal({{ $holiday->id }})" 
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 transition-colors cursor-pointer"
                                        title="Click to view employees who worked on this holiday"
                                    >
                                        <i class="ri-user-star-line text-amber-600"></i>
                                        <span>{{ $workerCount }} Worked</span>
                                    </button>
                                @else
                                    <span class="text-slate-400 text-xs">
                                        0 Worked
                                    </span>
                                @endif
                            </td>

                            <!-- Description -->
                            <td class="px-6 py-4 text-slate-500 max-w-xs truncate" title="{{ $holiday->description }}">
                                {{ $holiday->description ?: '—' }}
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <button 
                                        wire:click="openViewModal({{ $holiday->id }})" 
                                        class="p-1.5 text-slate-500 hover:text-slate-900 hover:bg-slate-100 rounded-md transition-colors cursor-pointer" 
                                        title="View Details & Worked Staff"
                                    >
                                        <i class="ri-eye-line text-base"></i>
                                    </button>

                                    <button 
                                        wire:click="openEditModal({{ $holiday->id }})" 
                                        class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-md transition-colors cursor-pointer" 
                                        title="Edit Holiday"
                                    >
                                        <i class="ri-edit-line text-base"></i>
                                    </button>

                                    <button 
                                        wire:click="openDeleteModal({{ $holiday->id }})" 
                                        class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-md transition-colors cursor-pointer" 
                                        title="Delete Holiday"
                                    >
                                        <i class="ri-delete-bin-line text-base"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                        <i class="ri-calendar-event-line text-2xl"></i>
                                    </div>
                                    <p class="font-semibold text-slate-700">No holidays found</p>
                                    <p class="text-xs text-slate-400 max-w-sm">No holidays match the selected criteria. Click "Add New Holiday" above to add one.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($holidays->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $holidays->links() }}
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- MODAL 1: ADD NEW HOLIDAY MODAL             -->
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
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeModals"></div>

            <div class="relative inline-block w-full max-w-md p-0 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl border border-slate-200">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-md bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                            <i class="ri-calendar-event-line text-lg"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Add New Holiday</h3>
                    </div>
                    <button type="button" wire:click="closeModals" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form wire:submit="createHoliday">
                    <div class="p-6 space-y-4">
                        <!-- Holiday Name -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Holiday Name <span class="text-rose-500">*</span></label>
                            <input 
                                type="text" 
                                wire:model="name" 
                                placeholder="e.g. Republic Day, Diwali, Christmas Day..." 
                                class="w-full px-3.5 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 {{ $errors->has('name') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                            />
                            @error('name') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Holiday Date <span class="text-rose-500">*</span></label>
                            <input 
                                type="date" 
                                wire:model="holiday_date" 
                                class="w-full px-3.5 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 {{ $errors->has('holiday_date') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                            />
                            @error('holiday_date') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Holiday Type <span class="text-rose-500">*</span></label>
                            <select 
                                wire:model="type" 
                                class="w-full px-3.5 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white"
                            >
                                <option value="public">Public Holiday (Official General Holiday)</option>
                                <option value="company">Company Holiday (Company Discretionary)</option>
                                <option value="optional">Optional / Restricted Holiday</option>
                            </select>
                            @error('type') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Description / Note</label>
                            <textarea 
                                wire:model="description" 
                                rows="3" 
                                placeholder="Optional description or note regarding this holiday..."
                                class="w-full px-3.5 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900"
                            ></textarea>
                            @error('description') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Note about employee working -->
                        <div class="p-3 bg-purple-50/70 rounded-md border border-purple-200/60 text-xs text-purple-800 space-y-1">
                            <p class="font-semibold flex items-center gap-1.5">
                                <i class="ri-information-line"></i>
                                Calendar & Employee Work Policy:
                            </p>
                            <p class="text-[11px] leading-relaxed text-purple-700">
                                This holiday will appear on the calendar for all employees. Employees can also choose to clock in and work on this day; their working hours will be tracked and displayed accordingly on the calendar.
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
                            type="submit" 
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 active:scale-95 rounded-md transition-all cursor-pointer shadow-sm"
                        >
                            <span wire:loading.remove wire:target="createHoliday">Save Holiday</span>
                            <span wire:loading wire:target="createHoliday" class="flex items-center gap-1.5">
                                <i class="ri-loader-4-line animate-spin text-base"></i>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 2: EDIT HOLIDAY MODAL                -->
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
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeModals"></div>

            <div class="relative inline-block w-full max-w-md p-0 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl border border-slate-200">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                            <i class="ri-edit-line text-lg"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Edit Holiday</h3>
                    </div>
                    <button type="button" wire:click="closeModals" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form wire:submit="updateHoliday">
                    <div class="p-6 space-y-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Holiday Name <span class="text-rose-500">*</span></label>
                            <input 
                                type="text" 
                                wire:model="name" 
                                class="w-full px-3.5 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 {{ $errors->has('name') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                            />
                            @error('name') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Date -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Holiday Date <span class="text-rose-500">*</span></label>
                            <input 
                                type="date" 
                                wire:model="holiday_date" 
                                class="w-full px-3.5 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 {{ $errors->has('holiday_date') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                            />
                            @error('holiday_date') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Holiday Type <span class="text-rose-500">*</span></label>
                            <select 
                                wire:model="type" 
                                class="w-full px-3.5 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white"
                            >
                                <option value="public">Public Holiday</option>
                                <option value="company">Company Holiday</option>
                                <option value="optional">Optional Holiday</option>
                            </select>
                            @error('type') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Description / Note</label>
                            <textarea 
                                wire:model="description" 
                                rows="3" 
                                class="w-full px-3.5 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900"
                            ></textarea>
                            @error('description') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
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
                            <span wire:loading.remove wire:target="updateHoliday">Update Holiday</span>
                            <span wire:loading wire:target="updateHoliday" class="flex items-center gap-1.5">
                                <i class="ri-loader-4-line animate-spin text-base"></i>
                                Updating...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 3: VIEW DETAILS & WORKED EMPLOYEES   -->
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
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeModals"></div>

            <div class="relative inline-block w-full max-w-2xl p-0 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl border border-slate-200">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50/50">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-md bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                            <i class="ri-calendar-check-line text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Holiday Details & Employee Work Activity</h3>
                            <p class="text-xs text-slate-500">{{ $selectedHoliday?->name }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModals" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-6">
                    @if ($selectedHoliday)
                        <!-- Holiday Overview Cards -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="p-3 bg-slate-50 rounded-md border border-slate-200">
                                <span class="text-[10px] font-bold uppercase text-slate-400 block">Date</span>
                                <span class="text-sm font-bold text-slate-900">{{ $selectedHoliday->holiday_date->format('d M Y (l)') }}</span>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-md border border-slate-200">
                                <span class="text-[10px] font-bold uppercase text-slate-400 block">Type</span>
                                <span class="text-sm font-bold text-slate-900 capitalize">{{ $selectedHoliday->type }} Holiday</span>
                            </div>
                            <div class="p-3 bg-amber-50 rounded-md border border-amber-200">
                                <span class="text-[10px] font-bold uppercase text-amber-600 block">Employees Worked</span>
                                <span class="text-sm font-bold text-amber-900">{{ $workedEmployees->count() }} Employees</span>
                            </div>
                        </div>

                        @if ($selectedHoliday->description)
                            <div class="p-3.5 bg-slate-50 rounded-md border border-slate-200 text-xs text-slate-700">
                                <span class="font-bold text-slate-800 block mb-1">Description:</span>
                                <p>{{ $selectedHoliday->description }}</p>
                            </div>
                        @endif

                        <!-- Worked Employees Section -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <i class="ri-user-star-line text-amber-600"></i>
                                    <span>Employees Who Worked On This Day</span>
                                </h4>
                                <span class="text-xs text-slate-500">{{ $workedEmployees->count() }} Records Found</span>
                            </div>

                            @if ($workedEmployees->isNotEmpty())
                                <div class="border border-slate-200 rounded-md overflow-hidden">
                                    <table class="w-full text-left text-xs border-collapse">
                                        <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase border-b border-slate-200">
                                            <tr>
                                                <th class="px-4 py-2.5">Employee</th>
                                                <th class="px-4 py-2.5">Department</th>
                                                <th class="px-4 py-2.5">Clock In</th>
                                                <th class="px-4 py-2.5">Clock Out</th>
                                                <th class="px-4 py-2.5">Total Hours</th>
                                                <th class="px-4 py-2.5 text-right">Calendar</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach ($workedEmployees as $item)
                                                <tr class="hover:bg-slate-50/50">
                                                    <td class="px-4 py-3 font-semibold text-slate-900">
                                                        {{ $item['user']->name }}
                                                    </td>
                                                    <td class="px-4 py-3 text-slate-500">
                                                        {{ $item['user']->department?->name ?? '—' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-emerald-700 font-medium">
                                                        {{ $item['clock_in_time'] ? \Carbon\Carbon::parse($item['clock_in_time'])->format('g:i A') : '—' }}
                                                    </td>
                                                    <td class="px-4 py-3 text-slate-700 font-medium">
                                                        {{ $item['clock_out_time'] ? \Carbon\Carbon::parse($item['clock_out_time'])->format('g:i A') : 'Active' }}
                                                    </td>
                                                    <td class="px-4 py-3 font-bold text-slate-900">
                                                        {{ $item['total_hours'] }}
                                                    </td>
                                                    <td class="px-4 py-3 text-right">
                                                        <a 
                                                            href="{{ route('admin.employee-calendar', $item['user']->id) }}" 
                                                            wire:navigate 
                                                            class="text-blue-600 hover:underline font-semibold"
                                                        >
                                                            View Calendar
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-6 text-center bg-slate-50 rounded-md border border-slate-200 text-xs text-slate-500 space-y-1">
                                    <p class="font-semibold text-slate-700">No employees worked on this holiday.</p>
                                    <p>All employees observed this date as an official holiday off.</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end px-6 py-4 bg-slate-50 border-t border-slate-200">
                    <button 
                        type="button" 
                        wire:click="closeModals" 
                        class="px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-200 rounded-md border border-slate-300 transition-all cursor-pointer"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL 4: DELETE HOLIDAY MODAL              -->
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
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" wire:click="closeModals"></div>

            <div class="relative inline-block w-full max-w-md p-0 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl border border-slate-200">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-rose-50/50">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-md bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                            <i class="ri-error-warning-line text-lg"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Confirm Holiday Deletion</h3>
                    </div>
                    <button type="button" wire:click="closeModals" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>

                <div class="p-6 space-y-3">
                    <p class="text-sm text-slate-700">
                        Are you sure you want to delete the holiday <strong class="text-slate-900">"{{ $selectedHoliday?->name }}"</strong> on <span class="font-semibold text-slate-900">{{ $selectedHoliday?->holiday_date?->format('d M Y') }}</span>?
                    </p>
                    <p class="text-xs text-rose-600 bg-rose-50 p-3 rounded-md border border-rose-200">
                        This action will remove the holiday from the organization calendar. Any existing employee attendance logs for this day will remain intact.
                    </p>
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
                        wire:click="deleteHoliday" 
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 active:scale-95 rounded-md transition-all cursor-pointer shadow-sm"
                    >
                        <span wire:loading.remove wire:target="deleteHoliday">Delete Holiday</span>
                        <span wire:loading wire:target="deleteHoliday" class="flex items-center gap-1.5">
                            <i class="ri-loader-4-line animate-spin text-base"></i>
                            Deleting...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
