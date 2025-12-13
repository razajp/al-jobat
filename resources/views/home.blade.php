@extends('app')
@section('title', 'Home | ' . app('client_company')->name)

@section('content')
    <div class="flex flex-col justify-center items-center tracking-wide">
        <!-- Logo -->
        <div class="mb-5 p-4 shadow-sm border border-[var(--glass-border-color)]/20 rounded-3xl">
            <div class="logo w-45 rounded-xl overflow-hidden">
                {!! app('client_company')->logo_svg !!}
            </div>
        </div>

        <!-- Title & Subtitle -->
        <h1 class="text-4xl font-bold text-[var(--primary-color)] mb-2 text-center">Welcome to {{ app('client_company')->name }}!</h1>
        <p class="text-[var(--secondary-text)] text-center mb-4">
            GarmentsOS | Track your progress and manage your tasks efficiently.
        </p>

        <!-- Powered by Tag -->
        <div class="text-xs text-gray-500 italic">
            Powered by <span class="font-semibold text-[var(--primary-color)]">SparkPair</span>
        </div>
    </div>


    @if ($pusherEnabled && $notification)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    showNotification('{{ $notification["title"] }}', '{{ $notification["message"] }}');
                }, 1000);
            })
        </script>
    @endif
@endsection
