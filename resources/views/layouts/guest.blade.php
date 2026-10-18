<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'JJVV Digital') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script>
            try {
                document.documentElement.classList.toggle('dark', localStorage.getItem('theme') === 'dark');
            } catch (_) {}
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 font-sans text-gray-700 antialiased dark:bg-gray-900 dark:text-gray-300">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
            <a href="/">
                <x-application-logo />
            </a>

            <div class="ui-panel mt-6 w-full p-6 shadow-sm sm:max-w-md sm:p-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>