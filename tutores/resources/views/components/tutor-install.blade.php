@props(['subject' => null])

{{-- Instrucciones de instalación por plataforma (CREADOR_TUTORES_SOCRATICOS.md §8).
     <details> nativo: accesible y sin JavaScript. --}}
@php($platforms = [
    [
        'name' => 'ChatGPT',
        'simple' => 'Abre un chat nuevo y pega el contenido del agente como primer mensaje.',
        'pro' => 'Con Plus: ve a «Explorar GPTs → Crear», pega el contenido en «Instrucciones» y guarda el GPT. Así lo tienes disponible siempre sin volver a pegarlo.',
    ],
    [
        'name' => 'Claude',
        'simple' => 'Abre una conversación nueva en claude.ai y pega el contenido como primer mensaje.',
        'pro' => 'Con Pro: crea un Proyecto, pega el contenido en «Instrucciones del proyecto» y conversa dentro de ese proyecto.',
    ],
    [
        'name' => 'Gemini',
        'simple' => 'Abre un chat nuevo y pega el contenido como primer mensaje.',
        'pro' => 'También puedes crear un Gem personalizado en «Gems → Nuevo Gem» y pegar el contenido en sus instrucciones.',
    ],
])

<div {{ $attributes->merge(['class' => 'divide-y divide-unicaes-line overflow-hidden rounded-xl border border-unicaes-line bg-white']) }}>
    @foreach($platforms as $platform)
        <details class="group">
            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 transition-colors hover:bg-unicaes-mist">
                <span class="text-sm font-semibold">{{ $platform['name'] }}</span>
                <svg class="size-4 shrink-0 text-unicaes-slate transition-transform duration-200 ease-[var(--ease-out-soft)] group-open:rotate-180"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/>
                </svg>
            </summary>
            <div class="space-y-2.5 border-t border-unicaes-line bg-unicaes-mist/40 px-4 pt-3 pb-4">
                <p class="text-[13px] leading-relaxed text-unicaes-slate">
                    <strong class="font-semibold text-unicaes-ink">Forma rápida —</strong>
                    {{ $platform['simple'] }}
                </p>
                <p class="text-[13px] leading-relaxed text-unicaes-slate">
                    <strong class="font-semibold text-unicaes-ink">Si tienes cuenta de pago —</strong>
                    {{ $platform['pro'] }}
                </p>
            </div>
        </details>
    @endforeach
</div>

<p class="mt-3 text-xs leading-relaxed text-unicaes-slate">
    Funciona igual en cualquier otra IA que acepte instrucciones escritas. El agente
    @if($subject)de <strong class="font-semibold text-unicaes-ink">{{ $subject->name }}</strong> @endif
    es un archivo de texto: no depende de ninguna plataforma en particular.
</p>
