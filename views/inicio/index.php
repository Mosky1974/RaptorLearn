<section class="portada">
    <h1>Descubre las Rapaces Ibéricas</h1>
    <p>Aprende, juega y conviértete en un experto ornitólogo.</p>
    <a href="<?= BASE_URL ?>/especies" class="btn-principal">Ver enciclopedia</a>
</section>

<section class="especies-destacadas">
    <h2>Especies destacadas</h2>
    <div class="grid-especies">
        <?php foreach ($especies as $especie): ?>
            <a href="<?= BASE_URL ?>/especies/detalle/<?= $especie['id_especie'] ?>" class="tarjeta-especie">
                <figure>
                    <img src="<?= BASE_URL ?>/public/img/especies/<?= htmlspecialchars($especie['ruta_imagen'] ?? 'default.jpg') ?>" 
                        alt="<?= htmlspecialchars($especie['nombre_comun']) ?>">
                    <?php if (!empty($especie['creditos'])): ?>
                        <figcaption><?= htmlspecialchars($especie['creditos']) ?></figcaption>
                    <?php endif; ?>
                </figure>
                <h3><?= htmlspecialchars($especie['nombre_comun']) ?></h3>
                <em><?= htmlspecialchars($especie['nombre_cientifico']) ?></em>
            </a>
        <?php endforeach; ?>
    </div>
</section>