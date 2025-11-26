@extends('auth.layout')

@section('title', 'Login')

@section('content')
<div class="auth-header">
    <h1>Selamat Datang</h1>
    <p>Login ke akun Anda</p>
</div>

<div class="auth-body">
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Login gagal!</strong>
            <ul class="error-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn">
            Login
        </button>
    </form>

    <div class="auth-footer">
        <p>Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></p>
    </div>
</div>
@endsection
