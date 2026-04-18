<div class="resultado-contenedor">

    <div class="resultado-cabecera <?= $aprobado ? 'aprobado' : 'suspenso' ?>">
        <div class="resultado-icono"><?= $aprobado ? '🎉' : '😔' ?></div>
        <h1><?= $aprobado ? '¡Cuestionario superado!' : 'Sigue practicando' ?></h1>
        <div class="resultado-puntuacion"><?= $puntuacion ?> puntos</div>
        <p><?= $correctas ?> de <?= count($resultados) ?> preguntas correctas (<?= $porcentaje ?>%)</p>
        <p class="resultado-tiempo">Tiempo empleado: <?= $tiempoEmpleado ?>s</p>
    </div>

    <section class="resultados-detalle">
        <h2>Revisión de respuestas</h2>

        <?php foreach ($resultados as $i => $r): ?>
            <div class="resultado-pregunta <?= $r['es_correcta'] ? 'correcta' : 'incorrecta' ?>">
                <div class="resultado-pregunta-header">
                    <span class="resultado-num">Pregunta <?= $i + 1 ?></span>
                    <span class="resultado-icono-pequeño"><?= $r['es_correcta'] ? '✅' : '❌' ?></span>
                </div>
                <p class="resultado-enunciado"><?= htmlspecialchars($r['enunciado']) ?></p>
                <p>Tu respuesta: <strong><?= htmlspecialchars($r['respuesta_seleccionada'] ?? 'Sin respuesta') ?></strong></p>
                <?php if (!$r['es_correcta']): ?>
                    <p class="respuesta-correcta">Respuesta correcta: <strong><?= htmlspecialchars($r['respuesta_correcta']) ?></strong></p>
                <?php endif; ?>
                <?php if (!empty($r['explicacion'])): ?>
                    <p class="resultado-explicacion">💡 <?= htmlspecialchars($r['explicacion']) ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>

    <div class="resultado-acciones">
        <a href="<?= BASE_URL ?>/juegos/cuestionarios" class="btn-principal">Volver a cuestionarios</a>
        <a href="<?= BASE_URL ?>/usuarios/perfil" class="btn-secundario">Ver mi perfil</a>
    </div>
</div>