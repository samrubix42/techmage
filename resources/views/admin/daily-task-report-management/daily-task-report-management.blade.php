<div>
    <div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white border border-slate-200 rounded-lg p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-primary-light text-primary-dark border border-yellow-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                    Admin Control
                </span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Employee Daily Task Reports</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Review, inspect, and approve daily work logs submitted by company employees.</p>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white border border-slate-200 rounded-lg p-4 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Search Input -->
        <div class="relative flex-1 max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i class="ri-search-line"></i>
            </div>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search employee, email, or project title..." 
                class="w-full pl-9 pr-4 py-2 border border-slate-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition-colors"
            />
            @if(!empty($search))
                <button wire:click="$set('search', '')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                    <i class="ri-close-line"></i>
                </button>
            @endif
        </div>

        <!-- Filter Dropdowns -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Review Status Filter -->
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status:</label>
                <select 
                    wire:model.live="statusFilter" 
                    class="border border-slate-300 rounded-md text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white"
                >
                    <option value="all">All Statuses</option>
                    <option value="pending">Pending Review</option>
                    <option value="checked">Reviewed & Done</option>
                </select>
            </div>

            <!-- Employee Filter -->
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Employee:</label>
                <select 
                    wire:model.live="employeeFilter" 
                    class="border border-slate-300 rounded-md text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white max-w-[160px]"
                >
                    <option value="">All Employees</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Department Filter -->
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Department:</label>
                <select 
                    wire:model.live="departmentFilter" 
                    class="border border-slate-300 rounded-md text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white max-w-[160px]"
                >
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Filter -->
            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Date:</label>
                <input 
                    type="date"
                    wire:model.live="dateFilter"
                    class="border border-slate-300 rounded-md text-sm px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-slate-900 bg-white"
                />
            </div>

            @if($search !== '' || $employeeFilter !== '' || $departmentFilter !== '' || $statusFilter !== 'all' || $dateFilter !== '')
                <button 
                    type="button" 
                    wire:click="resetFilters"
                    class="px-3 py-1.5 rounded-md text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors"
                >
                    Reset
                </button>
            @endif
        </div>
    </div>

    <!-- Reports Table Container -->
    <div class="bg-white border border-slate-200 rounded-lg shadow-xs overflow-hidden relative">
        <div wire:loading.flex class="absolute inset-0 bg-white/70 backdrop-blur-xs z-10 items-center justify-center transition-all">
            <div class="flex items-center gap-2 text-slate-700 font-semibold text-sm">
                <i class="ri-loader-4-line animate-spin text-slate-900 text-lg"></i>
                <span>Loading daily reports...</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-700 border-collapse">
                <thead class="text-xs uppercase bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5 font-semibold">Employee</th>
                        <th class="px-6 py-3.5 font-semibold">Department</th>
                        <th class="px-6 py-3.5 font-semibold">Report Date</th>
                        <th class="px-6 py-3.5 font-semibold">Projects</th>
                        <th class="px-6 py-3.5 font-semibold">Review Status</th>
                        <th class="px-6 py-3.5 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reports as $report)
                        @php
                            $items = is_array($report->title_description) ? $report->title_description : [];
                            $projectCount = count($items);
                        @endphp
                        <tr wire:key="admin-report-{{ $report->id }}" class="hover:bg-slate-50/80 transition-colors">
                            <!-- Employee Column -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($report->user->name ?? 'E', 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $report->user->name ?? 'Unknown Employee' }}</p>
                                        <p class="text-xs text-slate-500">{{ $report->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Department Column -->
                            <td class="px-6 py-4">
                                @if($report->user?->department)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        <i class="ri-building-line text-slate-400"></i>
                                        {{ $report->user->department->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Unassigned</span>
                                @endif
                            </td>

                            <!-- Date Column -->
                            <td class="px-6 py-4 font-semibold text-slate-900 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <i class="ri-calendar-line text-slate-400"></i>
                                    <span>{{ $report->date ? $report->date->format('M d, Y') : 'N/A' }}</span>
                                </div>
                                <span class="text-[11px] text-slate-400 font-normal block pl-5">
                                    Submitted {{ $report->created_at ? $report->created_at->format('g:i A') : '' }}
                                </span>
                            </td>

                            <!-- Projects Column -->
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 mb-1">
                                    {{ $projectCount }} {{ Str::plural('Project', $projectCount) }}
                                </span>
                                <div class="flex flex-wrap gap-1 max-w-xs">
                                    @foreach($items as $item)
                                        <span class="px-1.5 py-0.5 rounded bg-slate-50 text-slate-600 border border-slate-200 text-[10px] truncate max-w-[140px]" title="{{ $item['title'] ?? '' }}">
                                            {{ $item['title'] ?? '' }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Review Status Column -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($report->is_checked)
                                    <div class="space-y-0.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="ri-checkbox-circle-fill text-emerald-600"></i>
                                            <span>Reviewed & Done</span>
                                        </span>
                                        <span class="text-[10px] text-slate-400 block">
                                            by {{ $report->checkedBy?->name ?? 'Admin' }} at {{ $report->checked_at ? $report->checked_at->format('M d, g:i A') : '' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="ri-time-line text-amber-500"></i>
                                        <span>Pending Review</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <button 
                                        wire:click="viewReport({{ $report->id }})"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer"
                                    >
                                        <i class="ri-eye-line text-slate-500"></i>
                                        <span>Read & Review</span>
                                    </button>

                                    @if(!$report->is_checked)
                                        <button 
                                            wire:click="markAsChecked({{ $report->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-md text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors cursor-pointer"
                                            title="Mark Done"
                                        >
                                            <i class="ri-check-double-line"></i>
                                            <span>Mark Done</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <i class="ri-file-search-line text-3xl text-slate-300 block mb-2"></i>
                                <p class="font-semibold text-slate-700">No daily reports found.</p>
                                <p class="text-xs text-slate-400 mt-1">Try adjusting your filters or date selection.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($reports->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

    <!-- Review & Details Modal -->
    @if($showDetailModal && $this->selectedReport)
        @php
            $modalReport = $this->selectedReport;
        @endphp
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            x-data
            @keydown.escape.window="$wire.closeDetailModal()"
        >
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-150">
                <!-- Modal Header -->
                <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-slate-900 text-white uppercase tracking-wider">
                                Daily Report Review
                            </span>
                            <span class="text-xs font-bold text-slate-800">
                                {{ $modalReport->date ? $modalReport->date->format('F d, Y') : 'N/A' }}
                            </span>
                        </div>
                        <p class="text-xs font-medium text-slate-600 mt-1">
                            Submitted by <strong class="text-slate-900">{{ $modalReport->user->name ?? 'Employee' }}</strong> 
                            ({{ $modalReport->user->department?->name ?? 'Unassigned Department' }}) 
                            on {{ $modalReport->created_at ? $modalReport->created_at->format('M d, Y at g:i A') : '' }}
                        </p>
                    </div>

                    <button 
                        wire:click="closeDetailModal"
                        class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 flex items-center justify-center transition-colors"
                    >
                        <i class="ri-close-line text-xl"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="p-6 overflow-y-auto space-y-6 flex-1">
                    <!-- Status Banner -->
                    <div class="p-4 rounded-xl border {{ $modalReport->is_checked ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-amber-50 border-amber-200 text-amber-900' }} flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <i class="{{ $modalReport->is_checked ? 'ri-checkbox-circle-fill text-emerald-600' : 'ri-time-line text-amber-600' }} text-xl"></i>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider">
                                    {{ $modalReport->is_checked ? 'Status: Reviewed & Done' : 'Status: Pending Admin Review' }}
                                </p>
                                @if($modalReport->is_checked)
                                    <p class="text-[11px] opacity-80">
                                        Reviewed by {{ $modalReport->checkedBy?->name ?? 'Admin' }} on {{ $modalReport->checked_at ? $modalReport->checked_at->format('M d, Y at g:i A') : '' }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        @if($modalReport->is_checked)
                            <button 
                                wire:click="unmarkAsChecked({{ $modalReport->id }})"
                                class="px-3 py-1 bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 rounded text-xs font-semibold transition-colors cursor-pointer"
                            >
                                Mark as Pending
                            </button>
                        @endif
                    </div>

                    <!-- Project Items List -->
                    @php
                        $modalItems = is_array($modalReport->title_description) ? $modalReport->title_description : [];
                    @endphp

                    @foreach($modalItems as $index => $item)
                        <div class="border border-slate-200 rounded-xl p-5 bg-slate-50/50 space-y-3">
                            <div class="flex items-center gap-2 border-b border-slate-200 pb-2.5">
                                <span class="w-5 h-5 rounded-full bg-slate-800 text-white text-[11px] font-bold flex items-center justify-center shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <h4 class="text-sm font-bold text-slate-900 tracking-tight">
                                    {{ $item['title'] ?? 'Untitled Project' }}
                                </h4>
                            </div>

                            <div class="prose prose-slate prose-sm max-w-none text-xs leading-relaxed text-slate-700 bg-white p-4 rounded-lg border border-slate-200 shadow-2xs">
                                {!! $item['description'] ?? '<p class="text-slate-400 italic">No description provided.</p>' !!}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div></div>
                    <div class="flex items-center gap-3">
                        <button 
                            wire:click="closeDetailModal"
                            class="px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 rounded-lg border border-slate-300 transition-colors cursor-pointer"
                        >
                            Close
                        </button>

                        @if(!$modalReport->is_checked)
                            <button 
                                wire:click="markAsChecked({{ $modalReport->id }})"
                                class="inline-flex items-center gap-2 px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow-xs transition-colors cursor-pointer"
                            >
                                <i class="ri-check-double-line text-sm"></i>
                                <span>Mark as Reviewed & Done</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>
</div>