<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header / Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                <a href="{{ route('employee.daily-task-report-list') }}" class="hover:text-slate-900 transition-colors">Daily Task Reports</a>
                <i class="ri-arrow-right-s-line text-slate-400"></i>
                <span class="text-slate-900">{{ $isEdit ? 'Edit Report' : 'New Report' }}</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $isEdit ? 'Edit Daily Task Report' : 'Create Daily Task Report' }}</h1>
            <p class="text-xs text-slate-500 mt-1">
                {{ $isEdit ? 'Update your submitted task logs and project progress.' : 'Submit detailed task logs and project progress for your working day.' }}
            </p>
        </div>

        <a 
            href="{{ route('employee.daily-task-report-list') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-xs"
        >
            <i class="ri-arrow-left-line"></i>
            <span>Back to Reports List</span>
        </a>

    </div>

    <!-- Main Form Card -->
    <form wire:submit="save" class="space-y-6">
        <!-- Date Selection Card -->
        <div class="bg-white border border-slate-200 rounded-md p-5 shadow-xs">
            <div class="max-w-xs">
                <label for="report-date" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                    Report Date <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input 
                        type="date" 
                        id="report-date"
                        wire:model="date"
                        class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-md text-xs font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 transition-all"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i class="ri-calendar-event-line text-base"></i>
                    </div>
                </div>
                @error('date')
                    <p class="text-xs text-red-600 font-medium mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Project Entries List -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Project Reports ({{ count($projects) }})</h2>
                <span class="text-xs text-slate-500">You can add multiple project logs in a single report.</span>
            </div>

            @error('projects')
                <div class="p-3 bg-red-50 border border-red-200 rounded-md text-xs font-medium text-red-700">
                    {{ $message }}
                </div>
            @enderror

            @foreach($projects as $index => $project)
                <div 
                    wire:key="project-card-{{ $project['_key'] }}"
                    class="bg-white border border-slate-200 rounded-md p-5 shadow-xs space-y-4 relative group"
                >
                    <!-- Entry Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-slate-900 text-white font-bold text-xs flex items-center justify-center">
                                {{ $index + 1 }}
                            </span>
                            <h3 class="text-xs font-bold text-slate-800">Project Report Entry #{{ $index + 1 }}</h3>
                        </div>

                        @if(count($projects) > 1)
                            <button 
                                type="button"
                                wire:click="removeProject({{ $index }})"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 transition-colors cursor-pointer"
                                title="Remove Project"
                            >
                                <i class="ri-delete-bin-line"></i>
                                <span>Remove Entry</span>
                            </button>
                        @endif
                    </div>

                    <!-- Project Title -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Project Title / Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="projects.{{ $index }}.title"
                            placeholder="e.g. E-Commerce API Integration, UI Redesign, Bug Fixes"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-md text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-900 transition-all"
                        />
                        @error("projects.{$index}.title")
                            <p class="text-xs text-red-600 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Project Description with TinyMCE -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            Project Description & Accomplishments <span class="text-red-500">*</span>
                        </label>
                        
                        <div 
                            x-data="tinymceEditor(@entangle('projects.' . $index . '.description'), 'tinymce-{{ $project['_key'] }}')"
                            wire:ignore
                            class="border border-slate-200 rounded-md overflow-hidden"
                        >
                            <textarea id="tinymce-{{ $project['_key'] }}"></textarea>
                        </div>

                        @error("projects.{$index}.description")
                            <p class="text-xs text-red-600 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Dynamic Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
            <button 
                type="button"
                wire:click="addProject"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-md text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 transition-colors shadow-xs cursor-pointer"
            >
                <i class="ri-add-line text-sm"></i>
                <span>Add Another Project Report</span>
            </button>

            <div class="w-full sm:w-auto flex items-center gap-3">
                <a 
                    href="{{ route('employee.daily-task-report-list') }}"
                    class="w-1/2 sm:w-auto inline-flex items-center justify-center px-4 py-2.5 rounded-md text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors text-center"
                >
                    Cancel
                </a>

                <button 
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-md text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 transition-colors shadow-xs cursor-pointer disabled:opacity-50"
                >
                    <i wire:loading.remove wire:target="save" class="ri-save-line text-sm"></i>
                    <i wire:loading wire:target="save" class="ri-loader-4-line animate-spin text-sm"></i>
                    <span>{{ $isEdit ? 'Update Daily Report' : 'Submit Daily Report' }}</span>
                </button>

            </div>
        </div>
    </form>
</div>

<!-- TinyMCE Script & Alpine Component Script -->
<script src="{{ asset('tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('tinymceEditor', (wireValue, editorId) => ({
            value: wireValue,
            editor: null,
            init() {
                this.$nextTick(() => {
                    if (typeof tinymce === 'undefined') return;

                    if (tinymce.get(editorId)) {
                        tinymce.get(editorId).remove();
                    }

                    tinymce.init({
                        selector: '#' + editorId,
                        height: 260,
                        menubar: false,
                        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table wordcount',
                        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code',
                        content_style: 'body { font-family: "Instrument Sans", sans-serif; font-size: 13px; color: #1e293b; line-height: 1.5; }',
                        setup: (editor) => {
                            this.editor = editor;
                            editor.on('init', () => {
                                if (this.value) {
                                    editor.setContent(this.value);
                                }
                            });
                            editor.on('change KeyUp Input Blur', () => {
                                this.value = editor.getContent();
                            });
                        }
                    });
                });
            },
            destroy() {
                if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                    tinymce.get(editorId).remove();
                }
            }
        }));
    });
</script>