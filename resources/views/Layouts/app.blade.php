<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('tailwind.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>@yield('title', 'Track Point')</title>
    <style>
        /* color theme */
        :root {
            --bg-color: #0f172a;
            --h-bg-color: #1e293b;
            --secondary-bg-color: #1e293b;
            --h-secondary-bg-color: #273449;
            --text-color: #e2e8f0;
            --secondary-text: #94a3b8;
            --primary-color: #3b82f6;
            --h-primary-color: #2563eb;
            --bg-warning: hsl(45, 85%, 50%);
            --bg-success: hsl(142, 70%, 30%);
            --bg-error: hsl(360, 70%, 40%);
            --border-warning: hsl(45, 100%, 50%);
            --border-success: hsl(142, 100%, 35%);
            --border-error: hsl(360, 100%, 45%);
            --text-warning: hsl(45, 85%, 20%);
            --text-success: hsl(142, 85%, 20%);
            --text-error: hsl(360, 85%, 20%);
            --danger-color: hsl(0, 70%, 50%);
            --h-danger-color: hsl(0, 70%, 40%);
            --success-color: hsl(142, 70%, 35%);
            --h-success-color: hsl(142, 70%, 25%);
        }

        /* Light Mode */
        [data-theme='light'] {
            --bg-color: #f9fafb;
            --h-bg-color: #e5e7eb;
            --secondary-bg-color: #ffffff;
            --h-secondary-bg-color: #f3f4f6;
            --text-color: #374151;
            --secondary-text: #6b7280;
            --bg-warning: hsl(45, 100%, 90%);
            --bg-success: hsl(142, 100%, 90%);
            --bg-error: hsl(360, 100%, 90%);
            --border-warning: hsl(45, 100%, 55%);
            --border-success: hsl(142, 100%, 55%);
            --border-error: hsl(360, 100%, 55%);
            --text-warning: hsl(45, 75%, 35%);
            --text-success: hsl(142, 75%, 35%);
            --text-error: hsl(360, 75%, 35%);
        }

        [data-theme="dark"] input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }

        .bg-\[--primary-color\] {
            color: #e2e8f0 !important;
        }
        
        .my-scroller-2::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body class="bg-[--bg-color] text-[--text-color] text-sm min-h-screen flex items-center justify-center" cz-shortcut-listen="true">
    {{-- side bar --}}
    @if (Auth::check())
        @component('layouts.components.sidebar')
        @endcomponent
    @endif

    <div class="wrapper flex-1 flex flex-col h-screen relative">
        {{-- main content --}}
        <main class="flex-1 p-8 overflow-y-auto my-scroller-2 flex items-center justify-center">
            <div class="main-child grow">
                @yield('content')
            </div>
        </main>
    </div>
</body>

</html>
