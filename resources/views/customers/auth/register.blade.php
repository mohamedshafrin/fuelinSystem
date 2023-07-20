@extends('customers.auth.layout')

@section('title')
Sign Up
@endsection

@section('content')
<div class="form-container outer">
    <div class="form-form">
        <div class="form-form-wrap">
            <div class="form-container">
                <div class="form-content">

                    <h1 class="text-uppercase">Sign Up</h1>
                    <p class="">Sign Up to your account to continue.</p>

                    <form method="POST" class="text-left" action="{{ route('fuelin.customer.register') }}">
                        @csrf
                        <div class="form">

                            <div id="username-field" class="field-wrapper input">
                                <label for="username" class="text-uppercase">First Name</label>
                                <input id="username" name="first_name" maxlength="50" value="{{ old('first_name') }}"  autofocus type="text" class="form-control">
                                @error('first_name')
                                    <small class="text-danger"><strong>{{ $message }}</strong></small>
                                @enderror
                            </div>

                            <div id="username-field" class="field-wrapper input">
                                <label for="username" class="text-uppercase">Last Name</label>
                                <input id="username" name="last_name" maxlength="50" value="{{ old('last_name') }}"  autofocus type="text" class="form-control">
                                @error('last_name')
                                    <small class="text-danger"><strong>{{ $message }}</strong></small>
                                @enderror
                            </div>

                            <div id="username-field" class="field-wrapper input">
                                <label for="username" class="text-uppercase">Contact Number</label>
                                <input id="username" name="contact" value="{{ old('contact') }}" maxlength="10"  autofocus type="text" class="form-control">
                                @error('contact')
                                    <small class="text-danger"><strong>{{ $message }}</strong></small>
                                @enderror
                            </div>


                            <div id="username-field" class="field-wrapper input">
                                <label for="username" class="text-uppercase">Email Address</label>
                                <input id="username" name="email" maxlength="250" value="{{ old('email') }}"  autocomplete="email" type="email" class="form-control">
                                @error('email')
                                    <small class="text-danger"><strong>{{ $message }}</strong></small>
                                @enderror
                            </div>

                            <div id="username-field" class="field-wrapper input">
                                <label for="username" class="text-uppercase">NIC Number</label>
                                <input id="username" name="nic" value="{{ old('nic') }}" maxlength="12" minlength="10"  autofocus type="text" class="form-control">
                                @error('nic')
                                    <small class="text-danger"><strong>{{ $message }}</strong></small>
                                @enderror
                            </div>

                            <div id="username-field" class="field-wrapper input">
                                <label for="username" class="text-uppercase">Password</label>
                                <input id="username" name="password" value="{{ old('password') }}"  autofocus type="password" class="form-control">
                                @error('password')
                                    <small class="text-danger"><strong>{{ $message }}</strong></small>
                                @enderror
                            </div>

                            <div id="username-field" class="field-wrapper input">
                                <label for="username" class="text-uppercase">Password Confirmation</label>
                                <input id="username" name="password_confirmation" value="{{ old('password_confirmation') }}"  autofocus type="password" class="form-control">
                                @error('password_confirmation')
                                    <small class="text-danger"><strong>{{ $message }}</strong></small>
                                @enderror
                            </div>

                            <div class="d-sm-flex justify-content-between">
                                <div class="field-wrapper">
                                    <button type="submit" class="btn btn-secondary text-uppercase" value="">Sign Up</button>
                                </div>
                            </div>

                            <p class="signup-link">Already registered ? <a href="{{url('/')}}">Log In</a></p>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
