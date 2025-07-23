@extends('welcome')
@section('content')
<style>
    body { background: #f0f4f8; }
    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .auth-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        padding: 2.5rem 2rem 2rem 2rem;
        width: 100%;
        max-width: 370px;
        text-align: center;
    }
    .auth-card svg {
        margin-bottom: 1rem;
    }
    .auth-title {
        font-size: 1.7rem;
        font-weight: 700;
        color: #388e3c;
        margin-bottom: 1.5rem;
    }
    .auth-form label {
        display: block;
        text-align: left;
        margin-bottom: 0.3rem;
        font-weight: 600;
        color: #333;
    }
    .auth-form input {
        width: 100%;
        padding: 0.7rem 1rem;
        border: 1px solid #cfd8dc;
        border-radius: 8px;
        margin-bottom: 1.1rem;
        font-size: 1rem;
        background: #f7fafc;
        transition: border 0.2s;
    }
    .auth-form input:focus {
        border: 1.5px solid #388e3c;
        outline: none;
        background: #fff;
    }
    .auth-form button {
        width: 100%;
        background: #388e3c;
        color: #fff;
        font-weight: 700;
        border: none;
        border-radius: 8px;
        padding: 0.8rem;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    .auth-form button:hover {
        background: #1b5e20;
    }
    .auth-link {
        margin-top: 1.2rem;
        color: #607d8b;
        font-size: 1rem;
    }
    .auth-link a {
        color: #388e3c;
        font-weight: 600;
        text-decoration: none;
        margin-left: 0.2rem;
    }
    .auth-link a:hover {
        text-decoration: underline;
    }
    .auth-error {
        color: #d32f2f;
        font-size: 0.95rem;
        margin-bottom: 0.7rem;
        text-align: left;
    }
</style>
<div class="auth-container">
    <div class="auth-card">
        <svg width="40" height="40" fill="none" stroke="#388e3c" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        <div class="auth-title">Create Your Account</div>
        <form method="POST" action="{{ route('register') }}" class="auth-form">
            @csrf
            <label for="name">Name</label>
            <input type="text" name="name" id="name" required autofocus>
            @error('name')
                <div class="auth-error">{{ $message }}</div>
            @enderror
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required>
            @error('email')
                <div class="auth-error">{{ $message }}</div>
            @enderror
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
            @error('password')
                <div class="auth-error">{{ $message }}</div>
            @enderror
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
            <button type="submit">Register</button>
        </form>
        <div class="auth-link">
            Already have an account?
            <a href="{{ route('login') }}">Login</a>
        </div>
    </div>
</div>
@endsection 