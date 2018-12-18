@extends('layouts.app-with-nav-single-screen')

@section('content')
    <div class="bg-purple-dark flex flex-col flex-grow justify-center">
        <div class="px-6 max-w-sm w-full mx-auto">
            <div class="text-white py-6 text-xl">{{  __('Reset Password')  }}</div>
            <div class="text-white shadow-lg px-6 py-6 bg-white-transparent-1 text-sm rounded">

                @if (session('status'))
                    <div class="bg-white-transparent-2 p-4 mb-4 rounded text-xs" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <div class="mb-4 flex flex-col">
                        <input type="text" class="login-input {{ $errors->has('email') ? 'border-red-light':'' }}"
                               name="email" id="email" placeholder="Verify your email address" required autocomplete="username"
                               value="{{ old('email','') }}">
                        @if($errors->has('email'))
                            <div class="login-error">{{$errors->first('email')}}</div>
                        @endif
                    </div>

                    <div class="mb-4">
                        {{--<label for="password" class="label">Password</label>--}}
                        <input type="password" class="login-input {{ $errors->has('password') ? 'border-red-light':'' }}" name="password" id="password" placeholder="New Password" required autocomplete="new-password" value="{{ old('password') }}">
                        @if($errors->has('password'))
                            <div class="login-error">{{$errors->first('password')}}</div>
                        @endif
                    </div>

                    <div class="mb-4">
                        {{--<label for="password_confirmation" class="label">Password confirmationation</label>--}}
                        <input type="password" class="login-input {{ $errors->has('password_confirmation') ? 'border-red-light':'' }}" name="password_confirmation" id="password_confirmation" placeholder="Password Confirmation" required value="{{ old('password_confirmation') }}">
                        @if($errors->has('password_confirmation'))
                            <div class="login-error">{{$errors->first('password_confirmation')}}</div>
                        @endif
                    </div>

                    <button type="submit" class="w-full bg-white-transparent-5 hover:bg-white-transparent-7 py-4 text-black-transparent-5 uppercase tracking-wide">
                        {{ __('Reset Password') }}
                    </button>

                </form>
            </div>
        </div>
    </div>
@endsection
