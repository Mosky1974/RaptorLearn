<div class="educadores-contenedor">
    <h1>📝 <?= htmlspecialchars($titulo) ?></h1>
    <p class="texto-gris">Añade las preguntas y sus 4 opciones de respuesta. Marca cuál es la correcta.</p>

    <form method="POST" action="<?= BASE_URL ?>/educadores/editarPreguntas/<?= $cuestionario['id_cuestionario'] ?>">
        <input type="hidden" name="total_preguntas" value="<?= $cuestionario['numero_preguntas'] ?>">

        <?php for ($i = 0; $i < $cuestionario['numero_preguntas']; $i++): ?>
            <?php $pregunta = $cuestionario['preguntas'][$i] ?? null; ?>
            <div class="form-seccion pregunta-editor">
                <h3>Pregunta <?= $i + 1 ?></h3>

                <div class="campo">
                    <label>Enunciado</label>
                    <input type="text" name="enunciado[<?= $i ?>]" required
                           value="<?= htmlspecialchars($pregunta['enunciado'] ?? '') ?>">
                </div>

                <div class="campo">
                    <label>Explicación (se muestra al corregir)</label>
                    <input type="text" name="explicacion[<?= $i ?>]"
                           value="<?= htmlspecialchars($pregunta['explicacion'] ?? '') ?>">
                </div>

                <div class="respuestas-editor">
                    <label>Respuestas (marca la correcta)</label>
                    <?php
                    $respuestasExistentes = $pregunta['respuestas'] ?? [];
                    for ($j = 0; $j < 4; $j++):
                        $respExistente = $respuestasExistentes[$j] ?? null;
                    ?>
                        <div class="respuesta-editor-fila">
                            <input type="radio" name="correcta[<?= $i ?>]" value="<?= $j ?>"
                                <?= ($respExistente && $respExistente['es_correcta']) ? 'checked' : '' ?>>
                            <input type="text" name="respuesta[<?= $i ?>][<?= $j ?>]"
                                   placeholder="Opción <?= $j + 1 ?>"
                                   value="<?= htmlspecialchars($respExistente['texto_respuesta'] ?? '') ?>">
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endfor; ?>

        <div class="form-acciones">
            <button type="submit" class="btn-principal">Guardar preguntas</button>
            <a href="<?= BASE_URL ?>/educadores/cuestionarios" class="btn-secundario">Cancelar</a>
        </div>
    </form>
</div>