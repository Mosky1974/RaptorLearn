<div class="juegos-contenedor">
    <h1>❓ Cuestionarios</h1>
    <p>Selecciona un cuestionario para comenzar.</p>

    <div class="cuestionarios-lista">
        <?php foreach ($cuestionarios as $c): ?>
            <div class="cuestionario-tarjeta">
                <div class="cuestionario-info">
                    <h3><?= htmlspecialchars($c['titulo']) ?></h3>
                    <p><?= htmlspecialchars($c['descripcion']) ?></p>
                    <div class="cuestionario-meta">
                        <span>📝 <?= $c['numero_preguntas'] ?> preguntas</span>
                        <span>⏱️ <?= $c['tiempo_total'] / 60 ?> minutos</span>
                        <span>✅ Mínimo para aprobar: <?= $c['puntuacion_minima_aprobar'] ?>%</span>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/juegos/jugar/<?= $c['id_cuestionario'] ?>" 
                   class="btn-principal">Jugar</a>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($ranking)): ?>
        <section class="ranking-seccion">
            <h2>🏆 Mejores puntuaciones</h2>
            <table class="ranking-tabla">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Jugador</th>
                        <th>Puntuación</th>
                        <th>Tiempo</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ranking as $i => $entrada): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($entrada['nombre']) ?></td>
                            <td><?= $entrada['puntuacion'] ?> pts</td>
                            <td><?= $entrada['tiempo_empleado'] ?>s</td>
                            <td><?= date('d/m/Y', strtotime($entrada['fecha_fin'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    <?php endif; ?>
</div>