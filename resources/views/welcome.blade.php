<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

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
        <header class="mx-auto flex max-w-5xl items-center justify-between px-4 py-5 sm:px-6">
            <a href="/"><x-application-logo /></a>

            @if (Route::has('login'))
                <nav class="flex items-center gap-2">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="ui-button ui-button-primary">Ir al panel</a>
                    @else
                        <a href="{{ route('login') }}" class="ui-button ui-button-secondary">Iniciar sesión</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ui-button ui-button-primary">Registrarse</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <main class="mx-auto max-w-5xl space-y-6 px-4 pb-16 sm:px-6">
            <section class="ui-panel p-6 sm:p-10">
                <h1 class="text-3xl font-semibold text-gray-900 dark:text-white sm:text-4xl">Junta de vecinos</h1>
                <p class="mt-3 max-w-2xl text-gray-500 dark:text-gray-400">
                    Consulta los horarios de atención para realizar tus trámites en ventanilla.
                </p>
                <a href="{{ route('verificar.formulario') }}" class="ui-button ui-button-primary mt-6">Verificar un certificado</a>
            </section>

            <section class="ui-panel p-6 sm:p-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Horarios de atención al público</h2>

                @if ($horariosAtencion->isEmpty())
                    <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Aún no hay horarios de atención publicados.</p>
                @else
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Estos horarios se repiten cada semana.</p>
                    <dl class="mt-4 divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach (\App\Models\HorarioAtencion::DIAS as $dia => $nombre)
                            <div class="grid gap-1 py-3 sm:grid-cols-3">
                                <dt class="font-medium text-gray-900 dark:text-white">{{ $nombre }}</dt>
                                <dd class="text-sm sm:col-span-2">
                                    @forelse ($horariosAtencion->get($dia, collect()) as $horario)
                                        <p>{{ substr($horario->hora_inicio, 0, 5) }} a {{ substr($horario->hora_fin, 0, 5) }}</p>
                                    @empty
                                        <span class="text-gray-400 dark:text-gray-500">Sin atención programada</span>
                                    @endforelse
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                @endif

                <p class="mt-6 rounded-lg bg-brand-50 p-4 text-sm text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                    Para firmar el libro de socios, reserva una cita desde tu cuenta una vez aprobada tu documentación.
                </p>
            </section>
        </main>
    </body>
</html>