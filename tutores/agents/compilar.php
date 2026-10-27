<?php

declare(strict_types=1);

/** Compilador local de borradores; no requiere Laravel ni llama servicios de IA. */
function compilarTutor(array $perfil, string $motor): string
{
    foreach (['id', 'materia', 'area', 'nivel', 'version', 'fuente', 'alcance'] as $campo) {
        if (! isset($perfil[$campo]) || ! is_string($perfil[$campo]) || trim($perfil[$campo]) === '') {
            throw new InvalidArgumentException("Falta el campo de texto: {$campo}");
        }
    }
    if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/D', $perfil['id'])) {
        throw new InvalidArgumentException('El id debe contener solo letras minúsculas, números y guiones.');
    }
    if (! in_array($perfil['nivel'], ['estricto', 'progresivo', 'reflexivo', 'redaccion'], true)) {
        throw new InvalidArgumentException('Nivel de rigor desconocido.');
    }
    if (trim($motor) === '') {
        throw new InvalidArgumentException('El motor base está vacío.');
    }
    foreach (['temas', 'prerrequisitos', 'errores', 'mini_evaluacion', 'pendientes'] as $campo) {
        if (! isset($perfil[$campo]) || ! is_array($perfil[$campo]) || ! array_is_list($perfil[$campo]) || $perfil[$campo] === []) {
            throw new InvalidArgumentException("Se requiere una lista no vacía: {$campo}");
        }
        foreach ($perfil[$campo] as $valor) {
            if (! is_string($valor) || trim($valor) === '') {
                throw new InvalidArgumentException("Hay un elemento vacío o inválido en {$campo}");
            }
        }
    }
    $tituloNivel = ['estricto' => 'ESTRICTO', 'progresivo' => 'PROGRESIVO', 'reflexivo' => 'REFLEXIVO', 'redaccion' => 'REDACCIÓN'];
    $texto = "# TUTOR SOCRÁTICO — {$perfil['materia']} | UNICAES\n\n";
    $texto .= "> BORRADOR PARA REVISIÓN — sin validación docente ni pruebas conversacionales registradas.\n\n";
    $texto .= rtrim($motor)."\n\n";
    $texto .= "- Materia: {$perfil['materia']}\n- Área: {$perfil['area']}\n";
    $texto .= "- Versión: {$perfil['version']}\n- Nivel seleccionado: {$tituloNivel[$perfil['nivel']]}\n";
    $texto .= "- Fuente: {$perfil['fuente']}\n- Alcance: {$perfil['alcance']}\n\n";
    foreach (['temas' => 'Temas incluidos', 'prerrequisitos' => 'Prerrequisitos', 'errores' => 'Dificultades y preguntas guía', 'mini_evaluacion' => 'Mini-evaluación propuesta', 'pendientes' => 'Pendientes de validación'] as $campo => $titulo) {
        $texto .= "### {$titulo}\n\n";
        foreach ($perfil[$campo] as $valor) {
            $texto .= "- {$valor}\n";
        }
        $texto .= "\n";
    }

    return $texto;
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    try {
        $motor = file_get_contents(__DIR__.'/motor-base.md');
        if ($motor === false) {
            throw new RuntimeException('No se pudo leer el motor base.');
        }
        $archivos = glob(__DIR__.'/perfiles/*.json');
        if (! $archivos) {
            throw new RuntimeException('No se encontraron perfiles.');
        }
        // Validar todos los perfiles antes de escribir; no usar rutas del perfil como entrada.
        $salidas = [];
        foreach ($archivos as $archivo) {
            $contenido = file_get_contents($archivo);
            if ($contenido === false) {
                throw new RuntimeException("No se pudo leer {$archivo}");
            }
            $perfil = json_decode($contenido, true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($perfil)) {
                throw new InvalidArgumentException("Perfil inválido: {$archivo}");
            }
            $texto = compilarTutor($perfil, $motor);
            if (isset($salidas[$perfil['id']])) {
                throw new InvalidArgumentException("ID duplicado: {$perfil['id']}");
            }
            $salidas[$perfil['id']] = $texto;
        }
        $destino = __DIR__.'/borradores';
        if (! is_dir($destino) && ! mkdir($destino, 0775, true)) {
            throw new RuntimeException('No se pudo crear el directorio de borradores.');
        }
        foreach ($salidas as $id => $texto) {
            if (file_put_contents($destino.'/'.$id.'.md', $texto) === false) {
                throw new RuntimeException("No se pudo escribir {$id}");
            }
            echo "Generado BORRADOR: {$id}.md\n";
        }
    } catch (Throwable $error) {
        fwrite(STDERR, $error->getMessage()."\n");
        exit(1);
    }
}
