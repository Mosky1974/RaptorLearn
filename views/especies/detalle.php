<article class="detalle-especie">

    <!-- Cabecera -->
    <div class="especie-cabecera">
        <?php
        $imagenPrincipal = null;
        foreach ($especie['imagenes'] as $img) {
            if ($img['es_principal']) {
                $imagenPrincipal = $img;
                break;
            }
        }
        ?>
        <?php if ($imagenPrincipal): ?>
            <figure class="especie-figura">
                <img src="<?= BASE_URL ?>/public/img/especies/<?= htmlspecialchars($imagenPrincipal['ruta_imagen']) ?>"
                     alt="<?= htmlspecialchars($especie['nombre_comun']) ?>">
                <figcaption><?= htmlspecialchars($imagenPrincipal['creditos']) ?></figcaption>
            </figure>
        <?php endif; ?>

        <div class="especie-titulo">
            <h1><?= htmlspecialchars($especie['nombre_comun']) ?></h1>
            <h2><em><?= htmlspecialchars($especie['nombre_cientifico']) ?></em></h2>
            <?php if (!empty($especie['nombre_ingles'])): ?>
                <p class="nombre-ingles"><?= htmlspecialchars($especie['nombre_ingles']) ?></p>
            <?php endif; ?>
            <span class="conservacion estado-<?= strtolower($especie['estado_conservacion']) ?>">
                <?= htmlspecialchars($especie['estado_conservacion']) ?>
            </span>
            <span class="dificultad">
                Identificación: <?= htmlspecialchars($especie['dificultad_identificacion']) ?>
            </span>
        </div>
    </div>

    <!-- Descripción -->
    <section class="especie-seccion">
        <h3>Descripción</h3>
        <p><?= htmlspecialchars($especie['descripcion']) ?></p>
    </section>

    <!-- Características físicas -->
    <section class="especie-seccion">
        <h3>Características físicas</h3>
        <p><?= htmlspecialchars($especie['caracteristicas_fisicas']) ?></p>
        <div class="especie-medidas">
            <div class="medida">
                <span class="medida-valor"><?= $especie['envergadura_min'] ?>–<?= $especie['envergadura_max'] ?> m</span>
                <span class="medida-label">Envergadura</span>
            </div>
            <div class="medida">
                <span class="medida-valor"><?= $especie['peso_min'] ?>–<?= $especie['peso_max'] ?> g</span>
                <span class="medida-label">Peso</span>
            </div>
            <div class="medida">
                <span class="medida-valor"><?= $especie['longitud_min'] ?>–<?= $especie['longitud_max'] ?> cm</span>
                <span class="medida-label">Longitud</span>
            </div>
        </div>
        <?php if (!empty($especie['dimorfismo_sexual'])): ?>
            <p><strong>Dimorfismo sexual:</strong> <?= htmlspecialchars($especie['dimorfismo_sexual']) ?></p>
        <?php endif; ?>
    </section>

    <!-- Hábitat y distribución -->
    <section class="especie-seccion">
        <h3>Hábitat y distribución</h3>
        <p><?= htmlspecialchars($especie['habitat']) ?></p>
        <p><?= htmlspecialchars($especie['distribucion_geografica']) ?></p>
        <?php if (!empty($especie['altitud_min']) || !empty($especie['altitud_max'])): ?>
            <p><strong>Altitud:</strong> <?= $especie['altitud_min'] ?>–<?= $especie['altitud_max'] ?> m</p>
        <?php endif; ?>
    </section>

    <!-- Dieta -->
    <section class="especie-seccion">
        <h3>Dieta y comportamiento de caza</h3>
        <p><?= htmlspecialchars($especie['dieta']) ?></p>
        <?php if (!empty($especie['comportamiento_caza'])): ?>
            <p><?= htmlspecialchars($especie['comportamiento_caza']) ?></p>
        <?php endif; ?>
    </section>

    <!-- Reproducción -->
    <section class="especie-seccion">
        <h3>Reproducción</h3>
        <p><?= htmlspecialchars($especie['reproduccion']) ?></p>
        <p><strong>Época de cría:</strong> <?= htmlspecialchars($especie['epoca_cria']) ?></p>
        <p><strong>Número de huevos:</strong> <?= htmlspecialchars($especie['numero_huevos']) ?></p>
    </section>

    <!-- Conservación -->
    <section class="especie-seccion">
        <h3>Estado de conservación y amenazas</h3>
        <p><?= htmlspecialchars($especie['amenazas']) ?></p>
        <p><strong>Medidas de conservación:</strong> <?= htmlspecialchars($especie['medidas_conservacion']) ?></p>
        <p><strong>Población ibérica:</strong> <?= htmlspecialchars($especie['poblacion_iberica']) ?></p>
    </section>

    <!-- Curiosidades -->
    <?php if (!empty($especie['curiosidades'])): ?>
        <section class="especie-seccion especie-curiosidades">
            <h3>💡 ¿Sabías que...?</h3>
            <p><?= htmlspecialchars($especie['curiosidades']) ?></p>
        </section>
    <?php endif; ?>

    <!-- Audios -->
    <?php if (!empty($especie['audios'])): ?>
        <section class="especie-seccion">
            <h3>🔊 Cantos y llamadas</h3>
            <?php foreach ($especie['audios'] as $audio): ?>
                <div class="audio-item">
                    <p><?= htmlspecialchars($audio['descripcion'] ?? $audio['tipo_canto']) ?></p>
                    <audio controls>
                        <source src="<?= BASE_URL ?>/public/audio/<?= htmlspecialchars($audio['ruta_audio']) ?>">
                    </audio>
                    <?php if (!empty($audio['creditos'])): ?>
                        <small><?= htmlspecialchars($audio['creditos']) ?></small>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <div class="especie-volver">
        <a href="<?= BASE_URL ?>/especies" class="btn-secundario">← Volver a la enciclopedia</a>
    </div>

</article>