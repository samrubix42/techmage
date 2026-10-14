<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Employee Portal - ' . config('app.name', 'TechMage') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />

        <script src="{{ asset('tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="h-full antialiased font-sans bg-slate-50 text-slate-900" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen flex flex-col md:flex-row">
            <!-- Employee Sidebar -->
            <livewire:employee.sidebar />

            <!-- Main Content Container -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Employee Header -->
                <livewire:employee.header />

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <x-mobile-restriction-popup />
        @include('components.toaster')

        @livewireScripts
        <script>
            function registerTinymceAlpine() {
                if (typeof Alpine !== 'undefined' && !Alpine.data('tinymceEditor')) {
                    Alpine.data('tinymceEditor', (wireValue, editorId) => ({
                        value: wireValue,
                        editor: null,
                        useTinyMce: true,
                        init() {
                            this.$nextTick(() => {
                                if (this.useTinyMce) {
                                    this.initEditor();
                                }
                            });

                            this.$watch('value', (newVal) => {
                                if (this.editor && this.useTinyMce && this.editor.getContent() !== (newVal || '')) {
                                    this.editor.setContent(newVal || '');
                                }
                            });
                        },
                        initEditor() {
                            this.$nextTick(() => {
                                if (typeof tinymce === 'undefined') return;

                                if (tinymce.get(editorId)) {
                                    tinymce.get(editorId).remove();
                                }

                                tinymce.init({
                                    selector: '#' + editorId,
                                    height: 220,
                                    menubar: false,
                                    plugins: 'advlist autolink lists link charmap preview searchreplace visualblocks code fullscreen table wordcount',
                                    toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | alignleft aligncenter alignright | link table | removeformat | code',
                                    content_style: 'body { font-family: "Instrument Sans", sans-serif; font-size: 13px; color: #1e293b; line-height: 1.5; margin: 8px; }',
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
                        enableTinyMce() {
                            this.useTinyMce = true;
                            this.initEditor();
                        },
                        disableTinyMce() {
                            if (this.editor) {
                                this.value = this.editor.getContent();
                            }
                            this.destroyEditor();
                            this.useTinyMce = false;
                        },
                        destroyEditor() {
                            if (typeof tinymce !== 'undefined' && tinymce.get(editorId)) {
                                tinymce.get(editorId).remove();
                                this.editor = null;
                            }
                        },
                        destroy() {
                            this.destroyEditor();
                        }
                    }));
                }
            }

            if (typeof Alpine !== 'undefined') {
                registerTinymceAlpine();
            } else {
                document.addEventListener('alpine:init', registerTinymceAlpine);
            }
        </script>
    </body>
</html>


