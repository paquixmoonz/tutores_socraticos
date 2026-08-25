@extends('layouts.app')

@section('title', 'Cómo usar tu tutor socrático')

@section('content')

<header class="mb-9 max-w-2xl">
    <span class="badge bg-unicaes-green/10 text-unicaes-green-dark">Guía para estudiantes</span>
    <h1 class="mt-3 text-[28px] font-extrabold leading-tight tracking-tight sm:text-[32px]">
        Cómo usar tu tutor socrático
    </h1>
    <p class="mt-3 text-[15px] leading-relaxed text-unicaes-slate">
        Cada materia del catálogo tiene un agente: un texto que convierte la IA que ya usas en un
        tutor especializado en esa asignatura. Lo descargas, lo pegas y empiezas a estudiar.
        Esta guía explica cómo hacerlo y —más importante— qué esperar de él.
    </p>
</header>

{{-- ── Expectativas: lo que separa un buen uso de la frustración ───────────── --}}
<section class="mb-10" aria-labelledby="expectativas">
    <h2 id="expectativas" class="mb-1.5 text-lg font-bold tracking-tight">Antes de empezar: no es un resolvedor de tareas</h2>
    <p class="mb-5 max-w-2xl text-sm leading-relaxed text-unicaes-slate">
        Un tutor socrático está diseñado para <strong class="font-semibold text-unicaes-ink">no darte la
        respuesta</strong>. Te devuelve preguntas hasta que la encuentras tú. Puede resultar incómodo al
        principio; esa incomodidad es exactamente el momento en que estás aprendiendo.
    </p>

    <div class="grid gap-3.5 sm:grid-cols-2">
        <div class="card border-unicaes-green/25 bg-unicaes-green/[0.04] p-5">
            <h3 class="mb-3 flex items-center gap-2 text-[13px] font-bold tracking-wide uppercase text-unicaes-green-dark">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                Lo que sí hace
            </h3>
            <ul class="space-y-2 text-[13px] leading-relaxed text-unicaes-slate">
                @foreach([
                    'Te pregunta qué has intentado antes de guiarte.',
                    'Descompone un problema difícil en pasos que sí puedes resolver.',
                    'Te da pistas graduales cuando te atascas de verdad.',
                    'Responde directo dudas de definición, notación o datos concretos.',
                    'Te toma una mini-evaluación al cerrar un tema para comprobar si lo dominas.',
                ] as $item)
                    <li class="flex gap-2.5"><span class="mt-[7px] size-1 shrink-0 rounded-full bg-unicaes-green" aria-hidden="true"></span>{{ $item }}</li>
                @endforeach
            </ul>
        </div>

        <div class="card border-unicaes-maroon/20 bg-unicaes-maroon/[0.03] p-5">
            <h3 class="mb-3 flex items-center gap-2 text-[13px] font-bold tracking-wide uppercase text-unicaes-maroon">
                <svg class="size-4" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12"/></svg>
                Lo que no hace
            </h3>
            <ul class="space-y-2 text-[13px] leading-relaxed text-unicaes-slate">
                @foreach([
                    'No te entrega la solución de un ejercicio a la primera.',
                    'No resuelve tareas evaluadas ni exámenes: te propone ejercicios equivalentes.',
                    'No reescribe tus textos; te señala con preguntas qué revisar.',
                    'No se sale del temario de su materia.',
                    'No sustituye a tu docente ni a la clase.',
                ] as $item)
                    <li class="flex gap-2.5"><span class="mt-[7px] size-1 shrink-0 rounded-full bg-unicaes-maroon/50" aria-hidden="true"></span>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

{{-- ── Los cuatro pasos ────────────────────────────────────────────────────── --}}
<section class="mb-10" aria-labelledby="pasos">
    <h2 id="pasos" class="mb-5 text-lg font-bold tracking-tight">Ponerlo a funcionar en cuatro pasos</h2>

    <ol class="grid gap-3.5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['Encuentra tu materia', 'Entra al catálogo y navega Facultad → Carrera → Ciclo → Materia, o búscala por nombre o código.'],
            ['Copia el agente', 'En la página de la materia usa «Copiar al portapapeles». Si prefieres guardarlo, «Descargar agente».'],
            ['Pégalo en tu IA', 'Abre ChatGPT, Claude o Gemini, inicia una conversación nueva y pega todo el texto como primer mensaje.'],
            ['Empieza a trabajar', 'El tutor se presenta y te pregunta qué tema quieres estudiar. A partir de ahí, conversa.'],
        ] as $i => [$title, $body])
            <li class="card stagger-item p-5" style="--i: {{ $i }}">
                <span class="flex size-7 items-center justify-center rounded-full bg-unicaes-green text-xs font-extrabold text-white">{{ $i + 1 }}</span>
                <h3 class="mt-3.5 text-sm font-bold">{{ $title }}</h3>
                <p class="mt-1.5 text-[13px] leading-relaxed text-unicaes-slate">{{ $body }}</p>
            </li>
        @endforeach
    </ol>
</section>

{{-- ── Instalación por plataforma ──────────────────────────────────────────── --}}
<section class="mb-10 max-w-2xl" aria-labelledby="plataformas">
    <h2 id="plataformas" class="mb-1.5 text-lg font-bold tracking-tight">Dónde pegarlo, según la IA que uses</h2>
    <p class="mb-4 text-sm leading-relaxed text-unicaes-slate">
        Todas las opciones funcionan con cuenta gratuita. Las versiones de pago solo te ahorran
        volver a pegar el agente cada vez.
    </p>
    <x-tutor-install />
</section>

{{-- ── Cómo sacarle provecho ───────────────────────────────────────────────── --}}
<section class="mb-10" aria-labelledby="provecho">
    <h2 id="provecho" class="mb-5 text-lg font-bold tracking-tight">Cómo sacarle provecho de verdad</h2>

    <div class="grid gap-3.5 md:grid-cols-2">
        @foreach([
            ['Muestra tu intento, aunque esté mal', 'El tutor arranca preguntando qué has probado. Si le escribes tu razonamiento —aunque sea equivocado— puede detectar exactamente dónde se te rompió la idea. Un «no sé» a secas solo hace que te repita la pregunta más simple.'],
            ['Responde tú antes de pedir más', 'Cada pregunta que te hace tiene un propósito. Si contestas «no sé» a todo, el tutor baja la dificultad pero no avanza. Arriésgate a responder mal: es información útil para él.'],
            ['Pide definiciones sin culpa', 'Para conceptos, notación, fórmulas o datos históricos el tutor sí responde directo. Lo que no te va a regalar es el desarrollo de un ejercicio.'],
            ['Acepta la mini-evaluación', 'Cuando cierres un tema, te hará dos a cuatro preguntas cortas. Es el único momento en que sabrás si de verdad entendiste o solo seguiste el hilo.'],
            ['Una conversación nueva por sesión', 'Los chats largos hacen que la IA pierda el hilo de sus instrucciones. Empieza limpio cada vez que te sientes a estudiar.'],
            ['Si «olvida» que es tu tutor', 'Vuelve a pegar el agente en la conversación. Recupera su rol de inmediato.'],
        ] as $i => [$title, $body])
            <div class="card p-5">
                <h3 class="text-sm font-bold">{{ $title }}</h3>
                <p class="mt-1.5 text-[13px] leading-relaxed text-unicaes-slate">{{ $body }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ── Honestidad académica ────────────────────────────────────────────────── --}}
<section class="max-w-2xl" aria-labelledby="limites">
    <div class="card border-unicaes-gold/35 bg-unicaes-gold/[0.06] p-6">
        <h2 id="limites" class="mb-2.5 text-base font-bold tracking-tight">Dos advertencias honestas</h2>
        <p class="mb-3 text-sm leading-relaxed text-unicaes-slate">
            <strong class="font-semibold text-unicaes-ink">La IA se equivoca.</strong> El agente está
            construido sobre el programa oficial de la materia y validado por tu docente, pero la IA
            donde lo pegas puede aun así afirmar algo incorrecto con total seguridad. Contrasta con el
            plan de grado, con tu libro y con tu docente.
        </p>
        <p class="text-sm leading-relaxed text-unicaes-slate">
            <strong class="font-semibold text-unicaes-ink">Usarlo para copiar es hacer trampa.</strong>
            El tutor está para que aprendas, no para entregar en tu nombre. Consulta con tu docente
            qué uso de IA acepta en cada entrega.
        </p>
    </div>
</section>

<div class="mt-9">
    <a href="{{ route('catalog.index') }}" class="btn-primary">
        Ir al catálogo de materias
    </a>
</div>

@endsection
