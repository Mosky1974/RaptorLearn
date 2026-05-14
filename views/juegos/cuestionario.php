<div class="cuestionario-contenedor">
    <div class="cuestionario-cabecera">
        <h1><?= htmlspecialchars($cuestionario['titulo']) ?></h1>
        <div class="cuestionario-timer" id="timer">
            <span id="tiempo"><?= $cuestionario['tiempo_total'] ?></span>s
        </div>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/juegos/resultado" id="form-cuestionario">

        <?php foreach ($cuestionario['preguntas'] as $i => $pregunta): ?>
            <div class="pregunta-bloque">
                <h3>Pregunta <?= $i + 1 ?> de <?= count($cuestionario['preguntas']) ?></h3>
                <p class="pregunta-enunciado"><?= htmlspecialchars($pregunta['enunciado']) ?></p>

                <?php if (!empty($pregunta['imagen_asociada'])): ?>
                    <img src="<?= BASE_URL ?>/public/img/<?= htmlspecialchars($pregunta['imagen_asociada']) ?>"
                         alt="Imagen de la pregunta" class="pregunta-imagen">
                <?php endif; ?>

                <div class="respuestas-grid">
                    <?php foreach ($pregunta['respuestas'] as $respuesta): ?>
                        <label class="respuesta-opcion">
                            <input type="radio" 
                                   name="respuesta_<?= $pregunta['id_pregunta'] ?>"
                                   value="<?= $respuesta['id_respuesta'] ?>" required>
                            <span><?= htmlspecialchars($respuesta['texto_respuesta']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="cuestionario-footer">
            <button type="submit" class="btn-principal">Enviar respuestas</button>
        </div>
    </form>
</div>

<script>
// Temporizador
let segundos = <?= $cuestionario['tiempo_total'] ?>;
const timerEl = document.getElementById('tiempo');
const form    = document.getElementById('form-cuestionario');

const intervalo = setInterval(() => {
    segundos--;
    timerEl.textContent = segundos;
    if (segundos <= 30) timerEl.parentElement.classList.add('timer-urgente');
    if (segundos <= 0) {
        clearInterval(intervalo);
        form.submit();
    }
}, 1000);

// Confirmar antes de salir
window.onbeforeunload = () => 'Si sales perderás el progreso del cuestionario.';
form.addEventListener('submit', () => window.onbeforeunload = null);
</script>