<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Catálogo') · {{ config('app.name') }}</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/favicon-32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('img/apple-touch-icon.png') }}">
    <meta name="theme-color" content="#6B1F2A">

    {{ Vite::fonts() }}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">

@php($roleName = auth()->check() ? (auth()->user()->isAdmin() ? 'Admin' : (auth()->user()->isTeacher() ? 'Docente' : 'Estudiante')) : null)
@php($roleBadge = ['Admin' => 'bg-unicaes-maroon/10 text-unicaes-maroon', 'Docente' => 'bg-unicaes-blue/10 text-unicaes-blue', 'Estudiante' => 'bg-unicaes-green/10 text-unicaes-green-dark'][$roleName] ?? '')

<header class="sticky top-0 z-40 border-b border-unicaes-line bg-unicaes-mist/90 backdrop-blur-lg">
    <nav class="mx-auto flex h-[58px] max-w-[75rem] items-center justify-between gap-4 px-4 sm:px-6">
        <a href="{{ route('catalog.index') }}" class="flex items-center gap-2.5 font-bold text-unicaes-maroon">
            <span class="flex size-[34px] items-center justify-center rounded-[10px] bg-unicaes-green text-white text-[17px] font-extrabold tracking-tight">T</span>
            <span class="hidden sm:block leading-tight text-[13px]">Tutores Socráticos<span class="block text-[9px] font-bold tracking-[2px] text-unicaes-gold uppercase">UNICAES</span></span>
        </a>

        @auth
            <div class="hidden md:flex items-center gap-0.5">
                <a href="{{ route('catalog.index') }}"
                   class="rounded-lg px-3.5 py-1.5 text-[13px] transition-colors {{ request()->routeIs('catalog.*') ? 'bg-unicaes-green/10 font-bold text-unicaes-green-dark' : 'font-medium text-unicaes-slate hover:bg-white hover:text-unicaes-ink' }}">
                    Catálogo
                </a>
                <a href="{{ route('guide') }}"
                   class="rounded-lg px-3.5 py-1.5 text-[13px] transition-colors {{ request()->routeIs('guide') ? 'bg-unicaes-green/10 font-bold text-unicaes-green-dark' : 'font-medium text-unicaes-slate hover:bg-white hover:text-unicaes-ink' }}">
                    Cómo usarlo
                </a>
                @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                    <a href="{{ route('teacher.subjects.index') }}"
                       class="rounded-lg px-3.5 py-1.5 text-[13px] transition-colors {{ request()->routeIs('teacher.*') ? 'bg-unicaes-green/10 font-bold text-unicaes-green-dark' : 'font-medium text-unicaes-slate hover:bg-white hover:text-unicaes-ink' }}">
                        Mis materias
                    </a>
                @endif
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                       class="rounded-lg px-3.5 py-1.5 text-[13px] transition-colors {{ request()->routeIs('admin.*') ? 'bg-unicaes-green/10 font-bold text-unicaes-green-dark' : 'font-medium text-unicaes-slate hover:bg-white hover:text-unicaes-ink' }}">
                        Administración
                    </a>
                @endif

                <span class="mx-2.5 h-5 w-px bg-unicaes-line" aria-hidden="true"></span>

                <div class="flex items-center gap-2.5">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatar }}" alt="" class="size-[30px] rounded-full border border-unicaes-line">
                    @else
                        <span class="flex size-[30px] items-center justify-center rounded-full bg-unicaes-maroon text-[11px] font-bold text-white">
                            {{ collect(explode(' ', auth()->user()->name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('') }}
                        </span>
                    @endif
                    <div class="leading-tight">
                        <p class="text-[13px] font-semibold">{{ auth()->user()->name }}</p>
                        <span class="badge mt-0.5 {{ $roleBadge }}">{{ $roleName }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="ml-2">
                    @csrf
                    <button type="submit" class="rounded-lg border border-unicaes-line px-3 py-1 text-xs font-medium text-unicaes-slate transition-colors hover:bg-white hover:text-unicaes-maroon">
                        Salir
                    </button>
                </form>
            </div>

            <button type="button" data-menu-toggle aria-label="Abrir menú" aria-expanded="false"
                    class="md:hidden rounded-lg p-2 text-unicaes-slate hover:bg-white">
                <svg class="size-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" d="M3 7h18M3 12h18M3 17h18"/></svg>
            </button>
        @endauth
    </nav>

    @auth
        <div data-mobile-menu class="menu-collapse md:hidden">
            <div>
                <div class="border-t border-unicaes-line bg-unicaes-mist/95 px-4 pt-3 pb-5 space-y-0.5">
                    <a href="{{ route('catalog.index') }}" class="block rounded-[10px] px-3.5 py-2.5 text-sm {{ request()->routeIs('catalog.*') ? 'bg-unicaes-green/10 font-bold text-unicaes-green-dark' : 'font-medium text-unicaes-slate' }}">Catálogo</a>
                    <a href="{{ route('guide') }}" class="block rounded-[10px] px-3.5 py-2.5 text-sm {{ request()->routeIs('guide') ? 'bg-unicaes-green/10 font-bold text-unicaes-green-dark' : 'font-medium text-unicaes-slate' }}">Cómo usarlo</a>
                    @if(auth()->user()->isTeacher() || auth()->user()->isAdmin())
                        <a href="{{ route('teacher.subjects.index') }}" class="block rounded-[10px] px-3.5 py-2.5 text-sm {{ request()->routeIs('teacher.*') ? 'bg-unicaes-green/10 font-bold text-unicaes-green-dark' : 'font-medium text-unicaes-slate' }}">Mis materias</a>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block rounded-[10px] px-3.5 py-2.5 text-sm {{ request()->routeIs('admin.*') ? 'bg-unicaes-green/10 font-bold text-unicaes-green-dark' : 'font-medium text-unicaes-slate' }}">Administración</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="pt-2">
                        @csrf
                        <button type="submit" class="w-full rounded-[10px] border border-unicaes-line px-3.5 py-2.5 text-left text-sm font-medium text-unicaes-slate">
                            Salir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endauth
</header>

<main class="mx-auto w-full max-w-[75rem] flex-1 px-4 py-8 sm:px-6 sm:py-11">
    @if(session('status'))
        <div data-flash role="status"
             class="animate-pop-in mb-6 flex items-start justify-between gap-3 rounded-xl border border-unicaes-green/30 bg-unicaes-green/10 px-4 py-3 text-sm font-medium text-unicaes-green-dark transition-[opacity,transform] duration-300 ease-[var(--ease-out-soft)]">
            <span>{{ session('status') }}</span>
            <button type="button" data-flash-close aria-label="Cerrar" class="text-unicaes-green-dark/60 transition-colors hover:text-unicaes-green-dark">✕</button>
        </div>
    @endif

    @if($errors->any())
        <div role="alert" class="animate-pop-in mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold mb-1">Revisa lo siguiente:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<footer>
    {{-- Banda institucional: el lockup es arte granate, necesita fondo claro --}}
    <div class="border-t border-unicaes-line bg-white">
        <div class="mx-auto max-w-[75rem] px-4 py-7 text-center sm:px-6">
            <img src="{{ asset('img/unicaes-lockup.png') }}"
                 alt="Universidad Católica de El Salvador — UNICAES"
                 width="800" height="200" loading="lazy" decoding="async"
                 class="mx-auto h-auto w-full max-w-[230px]">
        </div>
    </div>
    <div class="bg-unicaes-maroon">
        <p class="mx-auto max-w-[75rem] px-4 py-6 text-center text-xs tracking-wide text-white/60 sm:px-6">
            Universidad Católica de El Salvador — UNICAES · Sistema de Tutores Socráticos · {{ now()->year }}
        </p>
    </div>
</footer>

</body>
</html>
