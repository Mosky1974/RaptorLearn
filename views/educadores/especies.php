<div class="educadores-contenedor">
    <div class="seccion-cabecera">
        <h1>Gestión de Especies</h1>
        <a href="<?= BASE_URL ?>/educadores/crearEspecie" class="btn-principal">+ Nueva especie</a>
    </div>

    <table class="tabla-gestion">
        <thead>
            <tr>
                <th>Nombre común</th>
                <th>Nombre científico</th>
                <th>Familia</th>
                <th>Conservación</th>
                <th>Autor</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($especies as $especie): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($especie['nombre_comun']) ?></strong></td>
                    <td><em><?= htmlspecialchars($especie['nombre_cientifico']) ?></em></td>
                    <td><?= htmlspecialchars($especie['familia'] ?? '-') ?></td>
                    <td>
                        <span class="conservacion estado-<?= strtolower($especie['estado_conservacion']) ?>">
                            <?= htmlspecialchars($especie['estado_conservacion']) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($especie['nombre_autor'] ?? 'Sistema') ?></td>
                    <td class="acciones">
                        <a href="<?= BASE_URL ?>/educadores/editarEspecie/<?= $especie['id_especie'] ?>" 
                           class="btn-accion editar">Editar</a>
                        <a href="<?= BASE_URL ?>/educadores/imagenes/<?= $especie['id_especie'] ?>" 
                           class="btn-accion imagenes">Imágenes</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>