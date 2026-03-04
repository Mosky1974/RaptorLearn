<h1>Enciclopedia de Rapaces Ibéricas</h1>

<div class="grid-especies">
    <?php foreach ($especies as $especie): ?>
        <a href="<?= BASE_URL ?>/especies/detalle/<?= $especie['id_especie'] ?>" class="tarjeta-especie">
            <h3><?= htmlspecialchars($especie['nombre_comun']) ?></h3>
            <em><?= htmlspecialchars($especie['nombre_cientifico']) ?></em>
            <span class="conservacion estado-<?= strtolower($especie['estado_conservacion']) ?>">
                <?= htmlspecialchars($especie['estado_conservacion']) ?>
            </span>
        </a>
    <?php endforeach; ?>
</div>