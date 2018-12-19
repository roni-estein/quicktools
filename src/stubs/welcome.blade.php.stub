<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    </head>
    <body>
        <div class="h-screen flex flex-col">
            @if (Route::has('login'))
                <div class="flex items-center py-4 text-primary justify-end uppercase">
                    @auth
                        <a href="{{ url('/home') }}" class="no-underline text-primary text-sm uppercase">Home</a>
                    @else
                        <a href="{{ route('login') }}" class="no-underline text-primary text-sm uppercase mr-8">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="no-underline text-primary text-sm uppercase mr-8">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="flex flex-col flex-grow items-center justify-center text-primary">
                <div class="text-5xl mb-4">
                    {{ config('app.name') }}
                </div>

                <div class="uppercase">
                    <a href="https://laravel.com/docs" class="no-underline mr-6 text-primary">Documentation</a>
                    <a href="https://laracasts.com" class="no-underline mr-6 text-primary">Laracasts</a>
                    <a href="https://laravel-news.com" class="no-underline mr-6 text-primary">News</a>
                    <a href="https://nova.laravel.com" class="no-underline mr-6 text-primary">Nova</a>
                    <a href="https://forge.laravel.com" class="no-underline mr-6 text-primary">Forge</a>
                    <a href="https://github.com/laravel/laravel" class="no-underline text-primary">GitHub</a>
                </div>
            </div>
        </div>
    </body>
</html>
