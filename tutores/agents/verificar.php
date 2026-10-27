<?php

declare(strict_types=1);

require __DIR__.'/compilar.php';

$comprobaciones = 0;
function comprobar(bool $condicion, string $mensaje): void
{
    global $comprobaciones;
    if (! $condicion) {
        throw new RuntimeException($mensaje);
    }
    $comprobaciones++;
}

$motor = file_get_contents(__DIR__.'/motor-base.md');
$perfil = json_decode(file_get_contents(__DIR__.'/perfiles/matematica-i.json'), true, 512, JSON_THROW_ON_ERROR);
foreach (glob(__DIR__.'/perfiles/*.json') as $archivo) {
    $p = json_decode(file_get_contents($archivo), true, 512, JSON_THROW_ON_ERROR);
    $texto = compilarTutor($p, $motor);
    comprobar(str_contains($texto, 'BORRADOR PARA REVISIÓN'), 'No puede salir sin advertencia de borrador.');
    comprobar(str_contains($texto, 'Para tareas evaluadas o exámenes se aplica siempre ESTRICTO.'), 'Falta protección de evaluaciones.');
    comprobar(str_contains($texto, $p['fuente']), 'Se perdió la procedencia.');
    comprobar(str_contains($texto, $p['mini_evaluacion'][0]), 'Se perdió la evaluación de la materia.');
    comprobar(str_contains($texto, $p['pendientes'][0]), 'Se perdieron los pendientes.');
}
foreach (['estricto' => 'ESTRICTO', 'progresivo' => 'PROGRESIVO', 'reflexivo' => 'REFLEXIVO', 'redaccion' => 'REDACCIÓN'] as $nivel => $titulo) {
    $p = array_replace($perfil, ['nivel' => $nivel]);
    comprobar(str_contains(compilarTutor($p, $motor), 'Nivel seleccionado: '.$titulo), 'Nivel incorrecto.');
}
foreach ([['nivel' => 'desconocido'], ['id' => '../escape'], ['fuente' => ''], ['mini_evaluacion' => []], ['pendientes' => [null]], ['temas' => 'texto']] as $cambio) {
    $rechazado = false;
    try {
        compilarTutor(array_replace($perfil, $cambio), $motor);
    } catch (InvalidArgumentException) {
        $rechazado = true;
    }
    comprobar($rechazado, 'Se aceptó un perfil inválido: '.json_encode($cambio));
}
echo "{$comprobaciones} comprobaciones automáticas correctas. No evalúan respuestas de una IA.\n";
