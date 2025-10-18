@extends('app')
@section('title', 'Home | ' . app('company')->name)

@section('content')
    <div class="flex flex-col justify-center items-center tracking-wide">
        <!-- Logo -->
        <div class="mb-4 p-2 shadow-sm border border-[var(--glass-border-color)]/20 rounded-xl">
            <div class="logo w-38 rounded-lg overflow-hidden">
                {{-- <img src="{{ asset('images/logo-transparent.png') }}" alt="GarmentsOS Logo" class="w-full h-full object-cover"> --}}
                <svg class="fill-[var(--primary-color)]" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                    viewBox="0 0 369.63 232.24" style="enable-background:new 0 0 369.63 232.24;" xml:space="preserve">
                <g>
                    <path d="M196.04,182.88H62.96c-0.82,0-1.32-0.97-0.87-1.7l22.09-35.85c0.19-0.31,0.52-0.5,0.87-0.5h40.64
                        c0.82,0,1.32-0.97,0.87-1.7l-20.51-33.29l-5.31-8.62c-0.41-0.66-1.32-0.66-1.73,0l-26.87,43.62l-23.14,37.55
                        c-0.19,0.31-0.52,0.5-0.87,0.5H3.69c-0.82,0-1.32-0.97-0.87-1.7l22.39-36.35l51.19-83.09l1.84-2.99
                        c10.19-16.54,33.04-16.54,43.23,0l1.84,2.99l6.18,10.03l29.99,48.67l9.95,16.16l5.07,8.23l1.9,3.08l20.49,33.27
                        C197.35,181.91,196.86,182.88,196.04,182.88z"/>
                    <path d="M369.63,103.77v81.1c0,0.61-0.46,1.1-1.04,1.1H242.14l0,0c-5.47,26.99-23.07,46.27-49.1,46.27H1.04
                        c-0.57,0-1.04-0.49-1.04-1.1v-35.85c0-0.61,0.46-1.1,1.04-1.1h183.87c9.92,0,17.97-8.53,17.97-19.06V49.78
                        c0-0.61,0.46-1.1,1.04-1.1h140.43c0.82,0,1.32,0.97,0.87,1.7l-14.84,24.09l-7.25,11.76c-0.19,0.31-0.52,0.5-0.87,0.5h-79.08
                        c-0.57,0-1.04,0.49-1.04,1.1v58.99c0,0.61,0.46,1.1,1.04,1.1h86.16c0.57,0,1.04-0.49,1.04-1.1v-9.11c0-0.61-0.46-1.1-1.04-1.1
                        h-42.34c-0.35,0-0.68-0.19-0.87-0.5l-19.56-31.74c-0.45-0.73,0.04-1.7,0.87-1.7H368.6C369.17,102.67,369.63,103.16,369.63,103.77z"
                        />
                    <ellipse cx="222.51" cy="20.3" rx="19.14" ry="20.3"/>
                </g>
                </svg>
            </div>
        </div>

        <!-- Title & Subtitle -->
        <h1 class="text-4xl font-bold text-[var(--primary-color)] mb-2 text-center">Welcome to {{ app('company')->name }}!</h1>
        <p class="text-[var(--secondary-text)] text-center mb-4">
            GarmentsOS | Track your progress and manage your tasks efficiently.
        </p>

        <!-- Powered by Tag -->
        <div class="text-xs text-gray-500 italic">
            Powered by <span class="font-semibold text-[var(--primary-color)]">SparkPair</span>
        </div>
    </div>


    @if ($notification)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    showNotification('{{ $notification["title"] }}', '{{ $notification["message"] }}');
                }, 1000);
            })
        </script>
    @endif
@endsection
