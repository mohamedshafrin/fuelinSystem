@extends('layouts.auth')

@section('title')
    Login
@endsection

@section('content')
<div class="form-container outer" style="background-image: url('https://www.alusons.com/wp-content/uploads/2021/06/x1-copy.jpg');"  >
    <div class="form-form" >
        <div class="form-form-wrap">
            <div class="form-container">
                <div class="form-content">

                    <h1 class="text-uppercase">Admin-Sign In</h1>
                    <p class="">Log in to your account  to continue.</p>

                    <form method="POST" class="text-left" action="{{ route('login') }}">
                        @csrf
                        <div class="form">

                            <div id="username-field" class="field-wrapper input">
                                <label for="username" class="text-uppercase">Enter Your Email Address</label>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                <input id="username" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus type="email" class="form-control" placeholder="e.g example@gmail.com">
                                @error('email')
                                    <small class="text-danger"><strong>{{ $message }}</strong></small>
                                @enderror
                            </div>

                            <div id="password-field" class="field-wrapper input mb-2">
                                <div class="d-flex justify-content-between">
                                    <label for="password">Enter your PASSWORD</label>
                                    <a href="auth_pass_recovery_boxed.html" class="forgot-pass-link">Forgot Password?</a>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                                <input id="password" name="password" required autocomplete="current-password" type="password" class="form-control" placeholder="Password">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="toggle-password" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                @error('password')
                                    <small class="text-danger"><strong>{{ $message }}</strong></small>
                                @enderror
                            </div>
                            <div class="d-sm-flex justify-content-between">
                                <div class="field-wrapper">
                                    <button type="submit" class="btn btn-dark text-uppercase" value="">Log In</button>
                                </div>
                            </div>


                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
