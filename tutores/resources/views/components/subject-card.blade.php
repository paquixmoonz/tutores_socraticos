{{-- Tarjeta de materia (Figma): nombre + badge de categoría, código mono, indicadores ● Agente / ● Plan --}}
@props(['subject'])

<a href="{{ route('catalog.subject', $subject) }}"
   class="card card-interactive group flex h-full flex-col gap-2.5 px-5 pt-[18px] pb-4">
    <div class="flex items-start justify-between gap-2.5">
        <h3 class="text-sm font-bold leading-snug group-hover:text-unicaes-green-dark">{{ $subject->name }}</h3>
        @if($subject->category)
            <span class="badge shrink-0"
                  style="background-color: {{ $subject->category->color ?? '#52606D' }}14; color: {{ $subject->category->color ?? '#52606D' }}">
                {{ $subject->category->name }}
            </span>
        @endif
    </div>
    @if($subject->code)
        <p class="font-mono text-[11px] tracking-wider text-unicaes-slate">{{ $subject->code }}</p>
    @endif
    <div class="mt-auto flex items-center gap-3.5 border-t border-unicaes-line pt-2.5 text-xs font-semibold">
        <span class="flex items-center gap-1.5 {{ $subject->activeAgent ? 'text-unicaes-green-dark' : 'text-[#CBD2D9]' }}">
            <svg class="size-2" viewBox="0 0 8 8" aria-hidden="true"><circle cx="4" cy="4" r="3.25" @if($subject->activeAgent) fill="#3AAA35" stroke="#3AAA35" @else fill="none" stroke="#CBD2D9" @endif stroke-width="1.5"/></svg>
            Agente
        </span>
        <span class="flex items-center gap-1.5 {{ $subject->activeStudyPlan ? 'text-unicaes-blue' : 'text-[#CBD2D9]' }}">
            <svg class="size-2" viewBox="0 0 8 8" aria-hidden="true"><circle cx="4" cy="4" r="3.25" @if($subject->activeStudyPlan) fill="#0258FC" stroke="#0258FC" @else fill="none" stroke="#CBD2D9" @endif stroke-width="1.5"/></svg>
            Plan
        </span>
    </div>
</a>
