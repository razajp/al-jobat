@extends('app')
@section('title', 'Home | ' . app('company')->name)

@section('content')
        <h1 class="text-3xl font-bold text-[var(--primary-color)] mb-4">Welcome to {{app('company')->name}}!</h1>
        <p class="text-[var(--secondary-text)] mb-6">Track your progress and manage your tasks efficiently.</p>
@endsection
