@extends('layouts.app-with-nav-single-screen')

@section('content')
    <div class="bg-primary flex flex-col flex-grow">
        <div class="mt-6 px-6 text-white-transparent-4 text-lg font-bold">{{ __('Dashboard') }}</div>
        <div class="px-6 max-w-sm w-full mx-auto mt-6">
            <div class="text-white shadow-lg px-6 py-6 bg-white-transparent-1 text-sm rounded">

                <div class="mb-4">{{ __('Verify Your Email Address') }}</div>

                @if (session('status'))
                    <div class="bg-white-transparent-2 p-4 mb-4 rounded text-xs" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                {{ __('You are logged in!') }}

            </div>
        </div>
    </div>
@endsection
