<article class="detalle-especie">
    <h1><?= htmlspecialchars($especie['nombre_comun']) ?></h1>
    <h2><em><?= htmlspecialchars($especie['nombre_cientifico']) ?></em></h2>

    <p><?= htmlspecialchars($especie['descripcion']) ?></p>

    <section>
        <h3>Características físicas</h3>
        <p><?= htmlspecialchars($especie['caracteristicas_fisicas']) ?></p>
        <ul>
            <li>Envergadura: <?= $especie['envergadura_min'] ?> – <?= $especie['envergadura_max'] ?> m</li>
            <li>Peso: <?= $especie['peso_min'] ?> – <?= $especie['peso_max'] ?> g</li>
        </ul>
    </section>

    <section>
        <h3>Hábitat</h3>
        <p><?= htmlspecialchars($especie['habitat']) ?></p>
    </section>

    <section>
        <h3>Dieta</h3>
        <p><?= htmlspecialchars($especie['dieta']) ?></p>
    </section>

    <section>
        <h3>Estado de conservación</h3>
        <span class="conservacion estado-<?= strtolower($especie['estado_conservacion']) ?>">
            <?= htmlspecialchars($especie['estado_conservacion']) ?>
        </span>
    </section>
</article>