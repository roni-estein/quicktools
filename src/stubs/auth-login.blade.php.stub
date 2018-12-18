@extends('layouts.app-without-nav')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="h-screen bg-purple-dark flex flex-col px-6">
            <div class="flex flex-col flex-grow justify-center mx-auto w-sm">
                <div class="text-white py-6 text-3xl">{{ config('app.name') }}</div>
                <div class="mb-4 flex flex-col">
                    <input type="text" class="login-input {{ $errors->has('email') ? 'border-red-light':'' }}"
                           name="email" id="email" placeholder="Email address" required autocomplete="username"
                           value="{{ old('email','') }}">
                    @if($errors->has('email'))
                        <div class="login-error">{{$errors->first('email')}}</div>
                    @endif
                </div>

                <div class="mb-4 flex flex-col">
                    <input type="password" class="login-input {{ $errors->has('password') ? 'border-red-light':'' }}"
                           name="password" id="password" placeholder="Password" required autocomplete="current-password"
                           value="{{ old('password') }}">
                    @if($errors->has('password'))
                        <div class="login-error">{{$errors->first('password')}}</div>
                    @endif
                </div>
                <div class="text-white text-sm ml-4">
                    <input class="" type="checkbox" name="remember"
                           id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="text-white-transparent-5 text-sm" for="remember">{{ __('Remember Me') }}</label>
                </div>
                <div class="mt-4">

                    <button class="w-sm bg-white-transparent-5 hover:bg-white-transparent-7 py-4 text-black-transparent-5 uppercase tracking-wide">
                        {{ __('Login') }}
                    </button>
                </div>
                <div class="mt-4 text-xs text-purple-dark font-bold text-center">
                    @if (Route::has('password.request'))
                        <a class="text-white-transparent-5" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                    @if (Route::has('register'))
                        <a class="ml-6 text-white-transparent-5" href="{{ route('register') }}">
                            {{ __('Don\'t have an account?') }}
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </form>
@endsection
