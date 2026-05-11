<div class="educadores-contenedor">
    <div class="seccion-cabecera">
        <h1>❓ Mis Cuestionarios</h1>
        <a href="<?= BASE_URL ?>/educadores/crearCuestionario" class="btn-principal">+ Nuevo cuestionario</a>
    </div>

    <?php if (empty($cuestionarios)): ?>
        <p class="texto-gris">Aún no has creado ningún cuestionario.</p>
    <?php else: ?>
        <table class="tabla-gestion">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Preguntas</th>
                    <th>Tiempo</th>
                    <th>Mín. aprobado</th>
                    <th>Autor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cuestionarios as $c): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($c['titulo']) ?></strong></td>
                        <td><?= $c['total_preguntas'] ?></td>
                        <td><?= $c['tiempo_total'] / 60 ?> min</td>
                        <td><?= $c['puntuacion_minima_aprobar'] ?>%</td>
                        <td><?= htmlspecialchars($c['nombre_autor'] ?? 'Sistema') ?></td>
                        <td class="acciones">
                            <?php if ($c['id_autor'] == $_SESSION['usuario_id'] || $_SESSION['tipo_usuario'] === 'admin'): ?>
                                <a href="<?= BASE_URL ?>/educadores/editarCuestionario/<?= $c['id_cuestionario'] ?>"
                                   class="btn-accion editar">✏️ Editar</a>
                                <a href="<?= BASE_URL ?>/educadores/editarPreguntas/<?= $c['id_cuestionario'] ?>"
                                   class="btn-accion imagenes">📝 Preguntas</a>
                                <a href="<?= BASE_URL ?>/educadores/eliminarCuestionario/<?= $c['id_cuestionario'] ?>"
                                    class="btn-accion eliminar"
                                    onclick="return confirm('¿Seguro que quieres eliminar este cuestionario? Esta acción no se puede deshacer.')">
                                    🗑️ Eliminar</a>
                            <?php else: ?>
                                <span class="texto-gris">Sin permisos</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>