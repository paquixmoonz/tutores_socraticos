@extends('layouts.app')

@section('title', $subject->name)

@section('content')
<nav aria-label="Ruta de navegación" class="mb-7 text-xs font-medium text-unicaes-slate">
    <a href="{{ route('catalog.index') }}" class="transition-colors hover:text-unicaes-ink">Catálogo</a>
    <span class="mx-1.5 text-[#CBD2D9]">/</span>
    <span class="font-semibold text-unicaes-ink">{{ $subject->name }}</span>
</nav>

<div class="grid items-start gap-7 lg:grid-cols-[1fr_320px]">
    {{-- Columna principal --}}
    <div class="space-y-5">
        <div>
            <div class="mb-3.5 flex flex-wrap gap-2">
                @if($subject->category)
                    <span class="badge"
                          style="background-color: {{ $subject->category->color ?? '#52606D' }}14; color: {{ $subject->category->color ?? '#52606D' }}">
                        {{ $subject->category->name }}
                    </span>
                @endif
                @if($subject->is_active ?? false)
                    <span class="badge bg-unicaes-green/10 text-unicaes-green-dark">Activa</span>
                @endif
            </div>
            <h1 class="text-2xl font-extrabold leading-tight tracking-tight sm:text-[26px]">{{ $subject->name }}</h1>
            <p class="mt-2.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-[13px] text-unicaes-slate">
                @if($subject->code)
                    <span class="font-mono tracking-wide">{{ $subject->code }}</span>
                    <span aria-hidden="true">·</span>
                @endif
                <span>Docente: <strong class="font-semibold text-unicaes-ink">{{ $subject->teacher?->name ?? 'Por asignar' }}</strong></span>
            </p>
        </div>

        @if($subject->description)
            <div class="card px-[22px] py-5">
                <h2 class="mb-3 text-[13px] font-bold tracking-wider uppercase">Sobre la materia</h2>
                <p class="text-sm leading-[1.75] text-unicaes-slate whitespace-pre-line">{{ $subject->description }}</p>
            </div>
        @endif

        @if($subject->careers->isNotEmpty())
            <div class="card px-[22px] py-5">
                <h2 class="mb-3.5 text-[13px] font-bold tracking-wider uppercase">Se imparte en</h2>
                <ul class="flex flex-wrap gap-2">
                    @foreach($subject->careers as $career)
                        <li class="rounded-full border border-unicaes-line bg-unicaes-mist px-[11px] py-1 text-xs text-unicaes-slate">
                            {{ $career->name }}@if($cycle = $cycles->get($career->pivot->cycle_id)) — {{ $cycle->label() }}@endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card px-[22px] py-5">
            <h2 class="mb-[18px] text-[13px] font-bold tracking-wider uppercase">Cómo usar tu tutor socrático</h2>
            <ol class="space-y-4">
                @foreach([
                    'Descarga o copia el agente socrático desde el panel lateral.',
                    'Abre tu asistente de IA favorito: ChatGPT, Claude o Gemini.',
                    'Pega el agente como tu primer mensaje en la conversación.',
                    'Empieza a hacer preguntas sobre la materia. El tutor te guiará.',
                ] as $i => $step)
                    <li class="flex items-start gap-3.5">
                        <span class="mt-px flex size-6 shrink-0 items-center justify-center rounded-full bg-unicaes-green text-[11px] font-extrabold text-white">{{ $i + 1 }}</span>
                        <p class="text-sm leading-relaxed text-unicaes-slate">{{ $step }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>

    {{-- Panel lateral: agente + plan (sticky en escritorio) --}}
    <div class="space-y-3.5 lg:sticky lg:top-[78px]">
        <div class="card overflow-hidden">
            <div class="flex items-center gap-2 border-b border-unicaes-green/15 bg-unicaes-green/5 px-[18px] py-3">
                <svg class="size-[15px] text-unicaes-green" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="15" x2="8" y2="15.01" stroke-linecap="round"/><line x1="16" y1="15" x2="16" y2="15.01" stroke-linecap="round"/>
                </svg>
                <h2 class="text-xs font-bold tracking-wide uppercase text-unicaes-green-dark">Agente socrático</h2>
            </div>
            <div class="px-[18px] pt-[18px] pb-5">
                @if($subject->activeAgent)
                    <p class="text-sm font-bold">{{ $subject->activeAgent->name }}</p>
                    @if($subject->activeAgent->description)
                        <p class="mt-1 text-xs leading-relaxed text-unicaes-slate">{{ $subject->activeAgent->description }}</p>
                    @endif
                    <dl class="mt-4 mb-[18px] grid grid-cols-3 gap-2.5 border-b border-unicaes-line pb-[18px]">
                        <div>
                            <dt class="text-[10px] font-bold tracking-wide uppercase text-unicaes-slate">Versión</dt>
                            <dd class="mt-0.5 text-[13px] font-bold">v{{ $subject->activeAgent->version }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold tracking-wide uppercase text-unicaes-slate">Tamaño</dt>
                            <dd class="mt-0.5 text-[13px] font-bold">{{ Number::fileSize($subject->activeAgent->file_size) }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold tracking-wide uppercase text-unicaes-slate">Descargas</dt>
                            <dd class="mt-0.5 text-[13px] font-bold">{{ number_format($subject->activeAgent->download_count) }}</dd>
                        </div>
                    </dl>
                    <div class="flex flex-col gap-2">
                        <a href="{{ route('agents.download', $subject->activeAgent) }}" class="btn-primary w-full">
                            <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path stroke-linecap="round" stroke-linejoin="round" d="m7 10 5 5 5-5"/><line stroke-linecap="round" x1="12" y1="15" x2="12" y2="3"/></svg>
                            Descargar agente
                        </a>
                        @if($subject->activeAgent->isCopyable())
                            <button type="button" data-copy-agent="{{ route('agents.content', $subject->activeAgent) }}" class="btn-secondary w-full">
                                <span data-copy-icon class="grid size-4 shrink-0 place-items-center">
                                    <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                                </span>
                                <span data-copy-label>Copiar al portapapeles</span>
                            </button>
                        @endif
                    </div>
                @else
                    <p class="text-sm leading-relaxed text-unicaes-slate">
                        El agente aún no está disponible. Vuelve pronto.
                    </p>
                @endif
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="flex items-center gap-2 border-b border-unicaes-blue/15 bg-unicaes-blue/5 px-[18px] py-3">
                <svg class="size-[15px] text-unicaes-blue" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                <h2 class="text-xs font-bold tracking-wide uppercase text-unicaes-blue">Plan de grado</h2>
            </div>
            <div class="px-[18px] pt-[18px] pb-5">
                @if($subject->activeStudyPlan)
                    <p class="text-sm font-bold">{{ $subject->activeStudyPlan->title }}</p>
                    <dl class="mt-4 mb-4 grid grid-cols-2 gap-2.5 border-b border-unicaes-line pb-4">
                        <div>
                            <dt class="text-[10px] font-bold tracking-wide uppercase text-unicaes-slate">Versión</dt>
                            <dd class="mt-0.5 text-[13px] font-bold">v{{ $subject->activeStudyPlan->version }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold tracking-wide uppercase text-unicaes-slate">Tamaño</dt>
                            <dd class="mt-0.5 text-[13px] font-bold">{{ Number::fileSize($subject->activeStudyPlan->file_size) }}</dd>
                        </div>
                    </dl>
                    <a href="{{ route('study-plans.download', $subject->activeStudyPlan) }}" class="btn-secondary w-full">
                        <svg class="size-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path stroke-linecap="round" stroke-linejoin="round" d="m7 10 5 5 5-5"/><line stroke-linecap="round" x1="12" y1="15" x2="12" y2="3"/></svg>
                        Descargar plan
                    </a>
                @else
                    <p class="text-sm leading-relaxed text-unicaes-slate">
                        El plan de grado aún no ha sido publicado por el docente.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
