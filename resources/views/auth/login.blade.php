@extends('app')
@section('title', 'Login | ' . app('company')->name)
@section('content')
    <div class="bg-[--secondary-bg-color] p-10 rounded-xl shadow-md max-w-md w-full fade-in mx-auto">
        <h4 class="text-xl font-semibold text-center text-[--primary-color]">Al Jobat</h4>
        <h1 class="text-3xl font-bold text-center mt-2 text-[--primary-color]">Login</h1>

        <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <!-- User Name -->
            <x-input 
                label="User Name" 
                name="username" 
                placeholder="Confirm your user name" 
                required 
            />

            <x-input 
                label="Password" 
                name="password" 
                type="password" 
                placeholder="Enter your password" 
                required 
            />

            <!-- login Button -->
            <button type="submit" class="bg-[--primary-color] px-5 py-2 rounded-lg hover:bg-blue-600 transition-all duration-300 ease-in-out font-medium">Login</button>
        </form>
    </div>
@endsection