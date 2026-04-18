<div class="educadores-contenedor">
    <h1><?= htmlspecialchars($titulo) ?></h1>

    <?php if (!empty($errores)): ?>
        <div class="alertas errores">
            <?php foreach ($errores as $error): ?>
                <p>⚠️ <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/educadores/<?= $editar ? 'editarCuestionario/' . $cuestionario['id_cuestionario'] : 'crearCuestionario' ?>">
        <div class="form-seccion">
            <div class="form-grid">
                <div class="campo">
                    <label>Título *</label>
                    <input type="text" name="titulo" required
                           value="<?= htmlspecialchars($cuestionario['titulo'] ?? '') ?>">
                </div>
                <div class="campo">
                    <label>Categoría</label>
                    <input type="text" name="categoria"
                           value="<?= htmlspecialchars($cuestionario['categoria'] ?? 'general') ?>">
                </div>
                <div class="campo">
                    <label>Descripción</label>
                    <textarea name="descripcion" rows="2"><?= htmlspecialchars($cuestionario['descripcion'] ?? '') ?></textarea>
                </div>
                <div class="campo">
                    <label>Número de preguntas *</label>
                    <input type="number" name="numero_preguntas" min="1" max="20" required
                           value="<?= $cuestionario['numero_preguntas'] ?? 5 ?>">
                </div>
                <div class="campo">
                    <label>Tiempo total (segundos)</label>
                    <input type="number" name="tiempo_total" min="60"
                           value="<?= $cuestionario['tiempo_total'] ?? 300 ?>">
                </div>
                <div class="campo">
                    <label>Puntuación mínima para aprobar (%)</label>
                    <input type="number" name="puntuacion_minima_aprobar" min="0" max="100"
                           value="<?= $cuestionario['puntuacion_minima_aprobar'] ?? 60 ?>">
                </div>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" class="btn-principal">
                <?= $editar ? 'Guardar cambios' : 'Crear y añadir preguntas' ?>
            </button>
            <a href="<?= BASE_URL ?>/educadores/cuestionarios" class="btn-secundario">Cancelar</a>
        </div>
    </form>
</div>