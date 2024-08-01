@extends('layouts.master')

@section('content')

    <div class="card">
        <div class="card-body">
            <div class="app-brand justify-content-center mb-4">
                <a href="{{url('/')}}" class="app-brand-link">
                    <x-table.table_image url="{{asset('assets/img/crudstarter_logo.png')}}" name="logo" height="50px" width="200px" />
                </a>
            </div>
            <h4 class="mb-3">Login Page</h4>
            <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Sign in with your email</label>
                    <input
                            type="text"
                            class="form-control @error('email') is-invalid @enderror" name="email"
                            value="{{ old('email') }}"
                            id="email"
                            placeholder="Enter your email"
                            autofocus
                    />
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mb-3 form-password-toggle">
                    <div class="d-flex justify-content-between">
                        <label class="form-label" for="password">Password</label>
                    </div>
                    <div class="input-group input-group-merge">
                        <input
                                type="password"
                                id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password"
                                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                aria-describedby="password"
                        />
                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }} " class="mt-1 d-inline-block">
                            <small>Forgot Password?</small>
                        </a>
                    @endif
                </div>
                <div class="mb-3">
                    <button class="btn btn-primary d-grid w-100" type="submit">LOGIN</button>
                </div>
            </form>

            <div>
                Do not have an account ?
                <a href="{{ route('register') }} " class="mt-1 d-inline-block">
                    <small>Sign Up</small>
                </a>
            </div>
        </div>
    </div>
@endsection
