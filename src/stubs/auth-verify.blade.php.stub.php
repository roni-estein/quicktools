@extends('layouts.app-with-nav-single-screen')

@section('content')
    <div class="bg-purple-dark flex flex-col flex-grow justify-center">
        <div class="px-6 max-w-sm mx-auto">
            <div class="text-white shadow-lg px-6 py-6 bg-white-transparent-1 text-sm rounded">

                <div class="mb-4">{{ __('Verify Your Email Address') }}</div>

                @if (session('resent'))
                    <div class="bg-white-transparent-2 p-4 mb-4 rounded text-xs" role="alert">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </div>
                @endif

                {{ __('Before proceeding, please check your email for a verification link.') }}
                {{ __('If you did not receive the email') }},
                <a class="text-white-transparent-8"
                   href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>.
            </div>
        </div>
    </div>
@endsection
