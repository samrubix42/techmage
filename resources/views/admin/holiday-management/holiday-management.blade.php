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

        <div class="flex flex-wrap items-center gap-3">
            <!-- View Switcher -->
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-md border border-slate-200">
                <button 
                    wire:click="setViewMode('calendar')" 
                    class="px-3.5 py-1.5 rounded-md text-xs font-semibold transition-all cursor-pointer flex items-center gap-1.5 {{ $viewMode === 'calendar' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    <i class="ri-calendar-line text-sm"></i>
                    <span>Calendar View</span>
                </button>
                <button 
                    wire:click="setViewMode('list')" 
                    class="px-3.5 py-1.5 rounded-md text-xs font-semibold transition-all cursor-pointer flex items-center gap-1.5 {{ $viewMode === 'list' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                >
                    <i class="ri-list-check-2 text-sm"></i>
                    <span>List View</span>
                </button>
            </div>

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

    @if ($viewMode === 'calendar')
        <!-- Calendar Navigation & Controls Bar -->
        <div class="bg-white rounded-md border border-slate-200/80 shadow-xs p-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <!-- Month Navigator -->
            <div class="flex items-center gap-3 w-full md:w-auto justify-between md:justify-start">
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-md">
                    <button 
                        wire:click="previousMonth" 
                        class="p-2 rounded-md hover:bg-white text-slate-600 hover:text-slate-900 transition-all cursor-pointer"
                        title="Previous Month"
                    >
                        <i class="ri-arrow-left-s-line text-lg"></i>
                    </button>
                    <button 
                        wire:click="goToToday" 
                        class="px-3 py-1.5 rounded-md text-xs font-semibold text-slate-700 hover:bg-white transition-all cursor-pointer"
                    >
                        Today
                    </button>
                    <button 
                        wire:click="nextMonth" 
                        class="p-2 rounded-md hover:bg-white text-slate-600 hover:text-slate-900 transition-all cursor-pointer"
                        title="Next Month"
                    >
                        <i class="ri-arrow-right-s-line text-lg"></i>
                    </button>
                </div>
                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <i class="ri-calendar-event-line text-purple-600"></i>
                    <span>{{ $currentMonthLabel }}</span>
                </h3>
            </div>

            <!-- Month & Year Jump Selectors + Badge -->
            <div class="flex flex-wrap items-center gap-3 w-full md:w-auto justify-end">
                <div class="flex items-center gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Month:</label>
                    <select 
                        wire:model.live="calendarMonth" 
                        class="border border-slate-300 rounded-md text-xs font-medium px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white"
                    >
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-1.5">
                    <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Year:</label>
                    <select 
                        wire:model.live="calendarYear" 
                        class="border border-slate-300 rounded-md text-xs font-medium px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white"
                    >
                        @foreach (range((int) now()->format('Y') - 2, (int) now()->format('Y') + 3) as $y)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-semibold bg-purple-50 text-purple-800 border border-purple-200">
                    <i class="ri-calendar-event-fill text-purple-600"></i>
                    <span>{{ $monthHolidaysCount }} {{ Str::plural('Holiday', $monthHolidaysCount) }} this month</span>
                </div>
            </div>
        </div>

        <!-- Holiday Legend & Quick Tip -->
        <div class="bg-white rounded-md border border-slate-200/80 shadow-xs px-4 py-3 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-slate-500 font-semibold uppercase tracking-wider text-[11px]">Holiday Legend:</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                    <i class="ri-calendar-event-fill text-purple-600"></i> Public Holiday
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    <i class="ri-calendar-event-fill text-blue-600"></i> Company Holiday
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    <i class="ri-calendar-event-fill text-amber-600"></i> Optional Holiday
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-800 border border-amber-300">
                    <i class="ri-user-star-line text-amber-600"></i> Staff Worked
                </span>
            </div>
            <div class="text-[11px] text-slate-500 flex items-center gap-1">
                <i class="ri-information-line text-slate-400"></i>
                <span>Click any empty day to add a holiday, or click a holiday card to view worked staff.</span>
            </div>
        </div>

        <!-- Calendar Grid Card -->
        <div class="bg-white rounded-md border border-slate-200/80 shadow-xs overflow-hidden">
            <!-- Day Names Header -->
            <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50/80 text-center text-xs font-bold text-slate-600 uppercase tracking-wider py-3">
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div class="text-amber-700">Sat</div>
                <div class="text-rose-700">Sun</div>
            </div>

            <!-- Calendar Days Grid -->
            <div class="grid grid-cols-7 auto-rows-fr divide-x divide-y divide-slate-100 bg-slate-50/30">
                @foreach ($calendarDays as $day)
                    <div 
                        class="min-h-[110px] sm:min-h-[125px] p-2 transition-all relative group flex flex-col justify-between 
                        {{ ! $day['isCurrentMonth'] ? 'bg-slate-50/50 opacity-40' : 'bg-white hover:bg-slate-50/60' }}
                        {{ $day['isToday'] ? 'ring-2 ring-amber-400 ring-inset bg-amber-50/20' : '' }}"
                    >
                        <!-- Cell Header: Date Number & Add Button -->
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold {{ $day['isToday'] ? 'w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center font-black' : ($day['isWeekend'] ? 'text-amber-800' : 'text-slate-700') }}">
                                {{ $day['dayNumber'] }}
                            </span>

                            @if($day['isToday'])
                                <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider hidden sm:inline">Today</span>
                            @endif

                            @if(!$day['holiday'])
                                <button 
                                    wire:click="openCreateModalForDate('{{ $day['date'] }}')" 
                                    title="Add holiday on {{ $day['date'] }}"
                                    class="opacity-0 group-hover:opacity-100 transition-opacity p-1 text-slate-400 hover:text-purple-700 hover:bg-purple-50 rounded cursor-pointer"
                                >
                                    <i class="ri-add-line text-xs font-bold"></i>
                                </button>
                            @endif
                        </div>

                        <!-- Cell Body: Holiday Display -->
                        <div class="flex-1">
                            @if ($day['holiday'])
                                @php
                                    $h = $day['holiday'];
                                    $themeClass = match($h->type) {
                                        'company' => 'bg-blue-50/90 border-blue-200 text-blue-950 hover:border-blue-300',
                                        'optional' => 'bg-amber-50/90 border-amber-200 text-amber-950 hover:border-amber-300',
                                        default => 'bg-purple-50/90 border-purple-200 text-purple-950 hover:border-purple-300',
                                    };
                                    $badgeClass = match($h->type) {
                                        'company' => 'bg-blue-100/80 text-blue-800 border-blue-200',
                                        'optional' => 'bg-amber-100/80 text-amber-800 border-amber-200',
                                        default => 'bg-purple-100/80 text-purple-800 border-purple-200',
                                    };
                                    $iconColor = match($h->type) {
                                        'company' => 'text-blue-600',
                                        'optional' => 'text-amber-600',
                                        default => 'text-purple-600',
                                    };
                                @endphp
                                <div 
                                    wire:click="openViewModal({{ $h->id }})" 
                                    class="p-2 rounded-md border {{ $themeClass }} shadow-xs transition-all cursor-pointer space-y-1.5"
                                >
                                    <div class="flex items-start justify-between gap-1">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <i class="ri-calendar-event-line text-sm {{ $iconColor }} shrink-0"></i>
                                            <span class="text-xs font-bold truncate leading-tight">{{ $h->name }}</span>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-1">
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-semibold border {{ $badgeClass }}">
                                            {{ ucfirst($h->type) }}
                                        </span>

                                        @if ($day['workerCount'] > 0)
                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                <i class="ri-user-star-line text-[10px]"></i>
                                                {{ $day['workerCount'] }} Worked
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Quick Actions on Card Hover -->
                                    <div class="flex items-center justify-end gap-1 pt-0.5 border-t border-slate-200/40" @click.stop>
                                        <button 
                                            wire:click.stop="openViewModal({{ $h->id }})" 
                                            class="p-1 text-slate-500 hover:text-purple-700 hover:bg-purple-100/60 rounded transition-colors cursor-pointer"
                                            title="View Details & Worked Staff"
                                        >
                                            <i class="ri-eye-line text-xs"></i>
                                        </button>
                                        <button 
                                            wire:click.stop="openEditModal({{ $h->id }})" 
                                            class="p-1 text-slate-500 hover:text-blue-700 hover:bg-blue-100/60 rounded transition-colors cursor-pointer"
                                            title="Edit Holiday"
                                        >
                                            <i class="ri-edit-line text-xs"></i>
                                        </button>
                                        <button 
                                            wire:click.stop="openDeleteModal({{ $h->id }})" 
                                            class="p-1 text-slate-500 hover:text-rose-700 hover:bg-rose-100/60 rounded transition-colors cursor-pointer"
                                            title="Delete Holiday"
                                        >
                                            <i class="ri-delete-bin-line text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div 
                                    wire:click="openCreateModalForDate('{{ $day['date'] }}')" 
                                    class="h-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer text-slate-300 hover:text-slate-500 py-4"
                                >
                                    <span class="text-[11px] font-medium">+ Add Holiday</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Yearly Holidays Quick Overview Section -->
        <div class="bg-white border border-slate-200/80 rounded-md shadow-xs p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-200 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="ri-calendar-event-line text-purple-600 text-lg"></i>
                        <span>All Holidays Scheduled for {{ $calendarYear }}</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Quick overview of every company holiday scheduled in {{ $calendarYear }}.</p>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-purple-50 text-purple-700 border border-purple-200">
                        {{ $yearlyHolidays->count() }} Total in {{ $calendarYear }}
                    </span>
                </div>
            </div>

            @if($yearlyHolidays->isEmpty())
                <div class="text-center py-8 text-slate-400">
                    <i class="ri-calendar-event-line text-3xl mb-1 text-slate-300"></i>
                    <p class="text-xs font-medium">No holidays scheduled for year {{ $calendarYear }}. Click "Add New Holiday" to create one.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($yearlyHolidays as $yHoliday)
                        @php
                            $yDateStr = $yHoliday->holiday_date->toDateString();
                            $yWorkerCount = $yearlyWorkedCounts[$yDateStr] ?? 0;
                            $yIsPast = $yHoliday->holiday_date->isPast() && $yDateStr !== now()->toDateString();
                            $yIsToday = $yDateStr === now()->toDateString();
                        @endphp
                        <div class="p-3.5 rounded-md border border-slate-200/80 hover:border-purple-300 bg-white hover:bg-purple-50/20 transition-all flex flex-col justify-between gap-3 shadow-2xs">
                            <div class="space-y-1.5">
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                        <i class="ri-calendar-event-line text-purple-600"></i>
                                        <span>{{ $yHoliday->name }}</span>
                                    </h4>
                                    @if ($yIsToday)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 shrink-0">Today</span>
                                    @elseif ($yIsPast)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600 border border-slate-200 shrink-0">Past</span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">Upcoming</span>
                                    @endif
                                </div>

                                <div class="text-[11px] text-slate-600 flex items-center gap-2">
                                    <span class="font-semibold text-slate-900">
                                        <i class="ri-calendar-line text-slate-400 mr-0.5"></i>
                                        {{ $yHoliday->holiday_date->format('d M Y') }}
                                    </span>
                                    <span class="text-slate-400">•</span>
                                    <span>{{ $yHoliday->holiday_date->format('l') }}</span>
                                </div>

                                @if($yHoliday->description)
                                    <p class="text-[11px] text-slate-500 line-clamp-1">{{ $yHoliday->description }}</p>
                                @endif
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                                    {{ ucfirst($yHoliday->type) }} Holiday
                                </span>

                                <div class="flex items-center gap-1">
                                    @if($yWorkerCount > 0)
                                        <button 
                                            wire:click="openViewModal({{ $yHoliday->id }})" 
                                            class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 hover:bg-amber-200 cursor-pointer"
                                            title="View worked employees"
                                        >
                                            <i class="ri-user-star-line text-[10px]"></i>
                                            {{ $yWorkerCount }} Worked
                                        </button>
                                    @endif

                                    <button 
                                        wire:click="openViewModal({{ $yHoliday->id }})" 
                                        class="p-1 text-slate-500 hover:text-purple-700 hover:bg-purple-100/60 rounded transition-colors cursor-pointer"
                                        title="View Details"
                                    >
                                        <i class="ri-eye-line text-xs"></i>
                                    </button>
                                    <button 
                                        wire:click="openEditModal({{ $yHoliday->id }})" 
                                        class="p-1 text-slate-500 hover:text-blue-700 hover:bg-blue-100/60 rounded transition-colors cursor-pointer"
                                        title="Edit Holiday"
                                    >
                                        <i class="ri-edit-line text-xs"></i>
                                    </button>
                                    <button 
                                        wire:click="openDeleteModal({{ $yHoliday->id }})" 
                                        class="p-1 text-slate-500 hover:text-rose-700 hover:bg-rose-100/60 rounded transition-colors cursor-pointer"
                                        title="Delete Holiday"
                                    >
                                        <i class="ri-delete-bin-line text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
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
    @endif

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

            <div class="relative inline-block w-full max-w-lg p-0 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl border border-slate-200">
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
                                placeholder="e.g. Republic Day, Diwali Vacation, Christmas Holidays..." 
                                class="w-full px-3.5 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 {{ $errors->has('name') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                            />
                            @error('name') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Date Mode Switcher & Inputs -->
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Holiday Date Selection <span class="text-rose-500">*</span></label>
                            
                            <!-- Mode Switcher Tabs -->
                            <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 rounded-md border border-slate-200">
                                <button 
                                    type="button" 
                                    wire:click="$set('date_mode', 'single')" 
                                    class="px-2.5 py-1.5 rounded text-xs font-semibold flex items-center justify-center gap-1.5 transition-all cursor-pointer {{ $date_mode === 'single' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                                >
                                    <i class="ri-calendar-event-line text-sm"></i>
                                    <span>Single Day</span>
                                </button>

                                <button 
                                    type="button" 
                                    wire:click="$set('date_mode', 'range')" 
                                    class="px-2.5 py-1.5 rounded text-xs font-semibold flex items-center justify-center gap-1.5 transition-all cursor-pointer {{ $date_mode === 'range' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                                >
                                    <i class="ri-calendar-2-line text-sm"></i>
                                    <span>Date Range</span>
                                </button>

                                <button 
                                    type="button" 
                                    wire:click="$set('date_mode', 'multiple')" 
                                    class="px-2.5 py-1.5 rounded text-xs font-semibold flex items-center justify-center gap-1.5 transition-all cursor-pointer {{ $date_mode === 'multiple' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}"
                                >
                                    <i class="ri-calendar-check-line text-sm"></i>
                                    <span>Multiple Dates</span>
                                </button>
                            </div>

                            <!-- Option 1: Single Date -->
                            @if ($date_mode === 'single')
                                <div class="pt-1">
                                    <input 
                                        type="date" 
                                        wire:model="holiday_date" 
                                        class="w-full px-3.5 py-2 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white {{ $errors->has('holiday_date') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                                    />
                                    @error('holiday_date') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                                </div>
                            @endif

                            <!-- Option 2: Date Range -->
                            @if ($date_mode === 'range')
                                <div class="pt-1 space-y-3 p-3 bg-slate-50 border border-slate-200 rounded-md">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Start Date <span class="text-rose-500">*</span></label>
                                            <input 
                                                type="date" 
                                                wire:model.live="start_date" 
                                                class="w-full px-3 py-1.5 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white {{ $errors->has('start_date') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                                            />
                                            @error('start_date') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">End Date <span class="text-rose-500">*</span></label>
                                            <input 
                                                type="date" 
                                                wire:model.live="end_date" 
                                                class="w-full px-3 py-1.5 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white {{ $errors->has('end_date') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }}"
                                            />
                                            @error('end_date') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between pt-1 text-xs">
                                        <label class="inline-flex items-center gap-2 cursor-pointer text-slate-700">
                                            <input 
                                                type="checkbox" 
                                                wire:model.live="range_skip_weekends" 
                                                class="rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                                            />
                                            <span class="text-[11px] font-medium">Skip weekends (Sat & Sun)</span>
                                        </label>

                                        @if(!empty($start_date) && !empty($end_date) && $start_date <= $end_date)
                                            @php
                                                $s = \Illuminate\Support\Carbon::parse($start_date);
                                                $e = \Illuminate\Support\Carbon::parse($end_date);
                                                $dayCount = 0;
                                                $c = $s->copy();
                                                while($c->lte($e)) {
                                                    if (!($range_skip_weekends && $c->isWeekend())) {
                                                        $dayCount++;
                                                    }
                                                    $c->addDay();
                                                }
                                            @endphp
                                            <span class="text-[11px] font-semibold text-purple-700 bg-purple-100/70 px-2 py-0.5 rounded border border-purple-200">
                                                {{ $dayCount }} {{ Str::plural('Holiday', $dayCount) }} will be added
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Option 3: Multiple Custom Dates -->
                            @if ($date_mode === 'multiple')
                                <div class="pt-1 space-y-3 p-3 bg-slate-50 border border-slate-200 rounded-md">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider mb-1">Pick a Date to Add</label>
                                        <div class="flex items-center gap-2">
                                            <input 
                                                type="date" 
                                                wire:model="new_multiple_date" 
                                                wire:keydown.enter.prevent="addMultipleDate"
                                                class="flex-1 px-3 py-1.5 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white"
                                            />
                                            <button 
                                                type="button" 
                                                wire:click="addMultipleDate" 
                                                class="px-3 py-1.5 bg-slate-900 text-white rounded-md text-xs font-semibold hover:bg-slate-800 transition-colors flex items-center gap-1 cursor-pointer shrink-0"
                                            >
                                                <i class="ri-add-line"></i>
                                                <span>Add Date</span>
                                            </button>
                                        </div>
                                        @error('multiple_dates') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Added Dates ({{ count($multiple_dates) }}):</span>
                                            @if(count($multiple_dates) > 0)
                                                <button 
                                                    type="button" 
                                                    wire:click="$set('multiple_dates', [])" 
                                                    class="text-[11px] text-rose-600 hover:underline cursor-pointer"
                                                >
                                                    Clear All
                                                </button>
                                            @endif
                                        </div>

                                        @if(empty($multiple_dates))
                                            <p class="text-xs text-slate-400 italic">No dates added yet. Pick a date above and click "Add Date".</p>
                                        @else
                                            <div class="flex flex-wrap gap-1.5 max-h-36 overflow-y-auto p-1.5 bg-white border border-slate-200 rounded-md">
                                                @foreach ($multiple_dates as $d)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-semibold bg-purple-50 text-purple-800 border border-purple-200">
                                                        <i class="ri-calendar-event-line text-purple-600"></i>
                                                        <span>{{ \Illuminate\Support\Carbon::parse($d)->format('d M Y (D)') }}</span>
                                                        <button 
                                                            type="button" 
                                                            wire:click="removeMultipleDate('{{ $d }}')" 
                                                            class="text-purple-500 hover:text-purple-900 p-0.5 cursor-pointer"
                                                            title="Remove"
                                                        >
                                                            <i class="ri-close-line text-xs font-bold"></i>
                                                        </button>
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
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
