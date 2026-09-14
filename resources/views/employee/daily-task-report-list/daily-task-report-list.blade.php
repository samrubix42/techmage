<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Daily Task Reports</h1>
            <p class="text-xs text-slate-500 mt-1">View and manage all your submitted daily work logs and project reports.</p>
        </div>

        <a 
            href="{{ route('employee.daily-task-report-create') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition-colors shadow-xs"
        >
            <i class="ri-add-line text-sm"></i>
            <span>Add Daily Task Report</span>
        </a>
    </div>

    <!-- Flash Success Message -->
    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between gap-3 text-xs font-medium text-emerald-800">
            <div class="flex items-center gap-2">
                <i class="ri-checkbox-circle-fill text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                <i class="ri-close-line text-base"></i>
            </button>
        </div>
    @endif

    <!-- Filters & Search Bar -->
    <div class="bg-white border border-slate-200 rounded-xl p-4 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-3">
        <!-- Search Input -->
        <div class="relative w-full sm:w-80">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="Search projects or topics..."
                class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 transition-all"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <i class="ri-search-line"></i>
            </div>
        </div>

        <!-- Date Filter -->
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-48">
                <input 
                    type="date" 
                    wire:model.live="dateFilter"
                    class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 transition-all"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="ri-filter-3-line"></i>
                </div>
            </div>

            @if($search !== '' || $dateFilter !== '')
                <button 
                    type="button" 
                    wire:click="$set('search', ''); $set('dateFilter', '')"
                    class="px-3 py-2 rounded-lg text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition-colors whitespace-nowrap"
                >
                    Clear Filters
                </button>
            @endif
        </div>
    </div>

    <!-- Reports Table / Cards List -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-xs overflow-hidden">
        @if($reports->isEmpty())
            <div class="p-12 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center mx-auto text-slate-400">
                    <i class="ri-file-paper-2-line text-2xl"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-900">No Daily Reports Found</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    @if($search !== '' || $dateFilter !== '')
                        No daily task reports matched your search filters. Try clearing filters or using different keywords.
                    @else
                        You haven't submitted any daily task reports yet. Click below to add your first report.
                    @endif
                </p>
                <div class="pt-2">
                    <a 
                        href="{{ route('employee.daily-task-report-create') }}"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition-colors shadow-xs"
                    >
                        <i class="ri-add-line"></i>
                        <span>Create Daily Task Report</span>
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <th class="py-3 px-5">Report Date</th>
                            <th class="py-3 px-5">Projects Submitted</th>
                            <th class="py-3 px-5">Projects Overview</th>
                            <th class="py-3 px-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach($reports as $report)
                            @php
                                $items = is_array($report->title_description) ? $report->title_description : [];
                                $projectCount = count($items);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- Date Column -->
                                <td class="py-4 px-5 font-bold text-slate-900 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i class="ri-calendar-check-line text-slate-400"></i>
                                        <span>{{ $report->date ? $report->date->format('M d, Y') : 'N/A' }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-normal block pl-6 space-y-0.5">
                                        <span>Submitted: {{ $report->created_at ? $report->created_at->format('g:i A') : '' }}</span>
                                        @if($report->updated_at && $report->updated_at->gt($report->created_at))
                                            <span class="inline-flex items-center gap-0.5 text-amber-700 font-semibold text-[10px] block">
                                                <i class="ri-history-line text-[10px]"></i>
                                                Edited {{ $report->updated_at->format('M d, g:i A') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Projects Count -->
                                <td class="py-4 px-5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                        <i class="ri-folder-3-line text-slate-500"></i>
                                        <span>{{ $projectCount }} {{ Str::plural('Project', $projectCount) }}</span>
                                    </span>
                                </td>

                                <!-- Projects Titles Preview -->
                                <td class="py-4 px-5">
                                    <div class="flex flex-wrap gap-1.5 max-w-md">
                                        @foreach($items as $item)
                                            <span class="px-2 py-0.5 rounded bg-slate-50 text-slate-700 border border-slate-200 font-medium text-[11px] truncate max-w-[200px]" title="{{ $item['title'] ?? 'Untitled' }}">
                                                {{ $item['title'] ?? 'Untitled' }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <button 
                                            wire:click="viewReport({{ $report->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors cursor-pointer"
                                        >
                                            <i class="ri-eye-line text-slate-500"></i>
                                            <span>View Details</span>
                                        </button>

                                        <a 
                                            href="{{ route('employee.daily-task-report-edit', $report) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors"
                                            title="Edit Report"
                                        >
                                            <i class="ri-edit-line text-slate-500"></i>
                                            <span>Edit</span>
                                        </a>

                                        @if($confirmingDeleteId === $report->id)
                                            <div class="inline-flex items-center gap-1 bg-red-50 p-1 rounded-lg border border-red-200">
                                                <button 
                                                    wire:click="deleteReport({{ $report->id }})"
                                                    class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-[11px] font-bold transition-colors"
                                                >
                                                    Confirm
                                                </button>
                                                <button 
                                                    wire:click="cancelDelete"
                                                    class="px-2 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded text-[11px] font-bold transition-colors"
                                                >
                                                    Cancel
                                                </button>
                                            </div>
                                        @else
                                            <button 
                                                wire:click="confirmDelete({{ $report->id }})"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer"
                                                title="Delete Report"
                                            >
                                                <i class="ri-delete-bin-line text-base"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination Container -->
            @if($reports->hasPages())
                <div class="p-4 border-t border-slate-200 bg-slate-50">
                    {{ $reports->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Report Detail Modal -->
    @if($showDetailModal && $selectedReport)
        <div 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs"
            x-data
            @keydown.escape.window="$wire.closeDetailModal()"
        >
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden animate-in fade-in zoom-in duration-150">
                <!-- Modal Header -->
                <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-slate-900 text-white uppercase tracking-wider">
                                Daily Report
                            </span>
                            <span class="text-xs font-bold text-slate-700">
                                {{ $selectedReport->date ? $selectedReport->date->format('F d, Y') : 'N/A' }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-1">
                            <span>Submitted: {{ $selectedReport->created_at ? $selectedReport->created_at->format('M d, Y at g:i A') : '' }}</span>
                            @if($selectedReport->updated_at && $selectedReport->updated_at->gt($selectedReport->created_at))
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                    <i class="ri-history-line"></i>
                                    Last Edited: {{ $selectedReport->updated_at->format('M d, Y at g:i A') }}
                                </span>
                            @endif
                        </div>
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
                    @php
                        $modalItems = is_array($selectedReport->title_description) ? $selectedReport->title_description : [];
                    @endphp

                    @foreach($modalItems as $index => $item)
                        <div class="border border-slate-200 rounded-xl p-5 bg-slate-50/50 space-y-3">
                            <div class="flex items-center gap-2 border-b border-slate-200 pb-2.5">
                                <span class="w-5 h-5 rounded-full bg-slate-800 text-white text-[11px] font-bold flex items-center justify-center shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <h3 class="text-sm font-bold text-slate-900 tracking-tight">
                                    {{ $item['title'] ?? 'Untitled Project' }}
                                </h3>
                            </div>

                            <div class="prose prose-slate prose-sm max-w-none text-xs leading-relaxed text-slate-700 bg-white p-4 rounded-lg border border-slate-200 shadow-2xs">
                                {!! $item['description'] ?? '<p class="text-slate-400 italic">No description provided.</p>' !!}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                    <button 
                        wire:click="deleteReport({{ $selectedReport->id }})"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors"
                    >
                        <i class="ri-delete-bin-line"></i>
                        <span>Delete Report</span>
                    </button>

                    <div class="flex items-center gap-2">
                        <a 
                            href="{{ route('employee.daily-task-report-edit', $selectedReport) }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-100 transition-colors shadow-xs"
                        >
                            <i class="ri-edit-line"></i>
                            <span>Edit Report</span>
                        </a>

                        <button 
                            wire:click="closeDetailModal"
                            class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition-colors"
                        >
                            Close
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>