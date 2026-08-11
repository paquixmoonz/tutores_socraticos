<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · {{ config('app.name') }}</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
    <meta name="theme-color" content="#6B1F2A">

    {{ Vite::fonts() }}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-unicaes-mist">

<main class="relative flex min-h-screen items-center justify-center px-4 py-12">
    {{-- Cuadrícula sutil de fondo (Figma) --}}
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 opacity-50"
         style="background-image: linear-gradient(#E4E7EB 1px, transparent 1px), linear-gradient(90deg, #E4E7EB 1px, transparent 1px); background-size: 48px 48px;"></div>

    <div class="relative w-full max-w-[400px]">
        <div class="mb-10 text-center">
            <span class="mx-auto mb-5 flex size-16 items-center justify-center rounded-2xl bg-unicaes-green text-[32px] font-extrabold tracking-tight text-white">T</span>
            <h1 class="text-[22px] font-extrabold tracking-tight text-unicaes-maroon">Tutores Socráticos</h1>
            <p class="mt-1 text-[11px] font-bold tracking-[3px] text-unicaes-gold uppercase">UNICAES</p>
        </div>

        <div class="rounded-[20px] border border-unicaes-line bg-white px-7 py-8 shadow-[0_4px_32px_rgba(0,0,0,0.05)]">
            <h2 class="text-base font-bold tracking-tight">Accede a la plataforma</h2>
            <p class="mt-1.5 text-[13px] leading-relaxed text-unicaes-slate">
                Usa tu cuenta institucional de Google para ingresar.
            </p>

            @if($errors->any())
                <div role="alert" class="mt-4 flex items-center gap-2 rounded-[10px] border border-red-300 bg-red-50 px-3.5 py-2.5 text-xs font-medium text-red-600">
                    <svg class="size-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <a href="{{ route('auth.google') }}"
               class="mt-6 flex w-full items-center justify-center gap-2.5 rounded-[10px] border border-unicaes-line bg-white px-4 py-[11px] text-sm font-semibold text-unicaes-ink shadow-[0_1px_4px_rgba(0,0,0,0.04)] transition-shadow hover:shadow-[0_4px_16px_rgba(0,0,0,0.09)] active:scale-[0.99]">
                <svg class="size-[18px]" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.27-4.74 3.27-8.1Z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84A11 11 0 0 0 12 23Z"/><path fill="#FBBC05" d="M5.84 14.1a6.6 6.6 0 0 1 0-4.2V7.06H2.18a11 11 0 0 0 0 9.88l3.66-2.84Z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15A11 11 0 0 0 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52Z"/></svg>
                Continuar con Google
            </a>

            <p class="mt-[18px] flex items-center justify-center gap-1.5 text-xs text-unicaes-slate">
                <svg class="size-[13px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Solo cuentas <strong class="font-semibold text-unicaes-ink">&#64;{{ config('tutores.allowed_domain') }}</strong>
            </p>

            @if(app()->environment('local'))
                @php($devUsers = \App\Models\User::with('roles')->orderBy('name')->limit(10)->get())
                @if($devUsers->isNotEmpty())
                    <details class="mt-6 rounded-xl border border-dashed border-unicaes-gold/50 bg-unicaes-gold/5 p-4">
                        <summary class="cursor-pointer text-xs font-semibold text-unicaes-gold uppercase tracking-wide">Acceso de desarrollo (solo local)</summary>
                        <ul class="mt-3 space-y-2">
                            @foreach($devUsers as $devUser)
                                <li>
                                    <form method="POST" action="{{ route('dev.login', $devUser) }}">
                                        @csrf
                                        <button type="submit" class="flex w-full items-center justify-between rounded-lg border border-unicaes-line bg-white px-3 py-2 text-left text-sm hover:bg-unicaes-mist">
                                            <span>
                                                <span class="font-medium">{{ $devUser->name }}</span>
                                                <span class="block text-xs text-unicaes-slate">{{ $devUser->email }}</span>
                                            </span>
                                            <span class="text-xs font-semibold text-unicaes-green capitalize">{{ $devUser->roles->pluck('name')->implode(', ') ?: 'estudiante' }}</span>
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </details>
                @endif
            @endif
        </div>

        <p class="mt-5 text-center text-xs text-unicaes-slate">
            ¿Primer ingreso? Tu cuenta se crea automáticamente al entrar con Google.
        </p>

        {{-- Respaldo institucional --}}
        <div class="mt-9 border-t border-unicaes-line pt-7">
            <img src="{{ asset('img/unicaes-lockup.png') }}"
                 alt="Universidad Católica de El Salvador — UNICAES"
                 width="800" height="200"
                 class="mx-auto h-auto w-full max-w-[260px] opacity-90">
        </div>
    </div>
</main>

</body>
</html>
