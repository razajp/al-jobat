@extends('app')
@section('title', 'Subscription Expired')

@section('content')
<div class="flex items-center justify-center">
    <div class="bg-[var(--secondary-bg-color)] p-10 rounded-xl shadow-md max-w-md w-full fade-in mx-auto text-center">

        {{-- Icon Section --}}
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-[var(--bg-error)] mb-4">
            <svg class="h-6 w-6 text-[var(--text-error)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        {{-- Content Section --}}
        <div class="space-y-4">
            <h1 class="text-3xl font-medium tracking-wide">Subscription Expired</h1>
            <p class="text-lg">
                It looks like your access has ended. Renew now to unlock full features.
            </p>

            {{-- Login Button --}}
            <a href="{{ route('login') }}"
               class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                Login
            </a>
        </div>

    </div>
</div>
@endsection
