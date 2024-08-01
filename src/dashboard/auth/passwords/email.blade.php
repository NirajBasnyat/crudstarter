@extends('layouts.master')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="app-brand justify-content-center mb-4">
                <a href="{{url('/')}}" class="app-brand-link">
                    <x-table.table_image url="{{asset('assets/img/crudstarter_logo.png')}}" name="logo" height="50px" width="200px" />
                </a>
            </div>
            <h4 class="mb-2">Forgot Password?</h4>
            <p class="mb-3">Please enter your email to reset your password</p>
            <form id="formAuthentication" class="mb-3" method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                            type="text"
                            id="email"
                            class="form-control @error('email') is-invalid @enderror" name="email"
                            value="{{ $email ?? old('email') }}"
                            placeholder="Enter your email"
                            autofocus
                    />
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <button class="btn btn-primary d-grid w-100" type="submit">Send Reset Link</button>
            </form>
            <div class="text-center">
                <a href="{{route('login')}}" class="d-flex align-items-center justify-content-center">
                    <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                    Back to Login
                </a>
            </div>
        </div>
    </div>
@endsection
