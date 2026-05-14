<div class="perfil-contenedor">

    <!-- Cabecera del perfil -->
    <div class="perfil-cabecera">
        <div class="perfil-avatar">
            <?php if (!empty($usuario['avatar'])): ?>
                <img src="<?= BASE_URL ?>/public/img/avatares/<?= htmlspecialchars($usuario['avatar']) ?>"
                    alt="Avatar de <?= htmlspecialchars($usuario['nombre']) ?>"
                    style="width:100px; height:100px; border-radius:50%; object-fit:cover; display:block;">
            <?php else: ?>
                <div class="avatar-placeholder">
                    <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="perfil-info">
            <h1><?= htmlspecialchars($usuario['nombre']) ?> <?= htmlspecialchars($usuario['apellidos'] ?? '') ?></h1>
            <p class="perfil-tipo"><?= ucfirst($usuario['tipo_usuario']) ?></p>
            <p class="perfil-nivel"><?= htmlspecialchars($usuario['nombre_nivel'] ?? 'Observador Novato') ?></p>
        </div>
        <div class="perfil-acciones">
            <a href="<?= BASE_URL ?>/usuarios/editarPerfil" class="btn-principal">Editar perfil</a>
            <a href="<?= BASE_URL ?>/usuarios/cambiarPassword" class="btn-secundario">Cambiar contraseña</a>
            <a href="<?= BASE_URL ?>/usuarios/eliminarCuenta" class="btn-eliminar-cuenta">Eliminar cuenta</a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="perfil-stats">
        <div class="stat-tarjeta">
            <span class="stat-valor"><?= $usuario['puntos_totales'] ?? 0 ?></span>
            <span class="stat-label">Puntos totales</span>
        </div>
        <div class="stat-tarjeta">
            <span class="stat-valor"><?= $usuario['especies_descubiertas'] ?? 0 ?></span>
            <span class="stat-label">Especies descubiertas</span>
        </div>
        <div class="stat-tarjeta">
            <span class="stat-valor"><?= $usuario['racha_dias'] ?? 0 ?></span>
            <span class="stat-label">Días de racha</span>
        </div>
        <div class="stat-tarjeta">
            <span class="stat-valor"><?= count($insignias) ?></span>
            <span class="stat-label">Insignias</span>
        </div>
    </div>

    <!-- Barra de progreso de nivel -->
    <?php if (!empty($usuario['puntos_siguiente_nivel'])): ?>
        <?php
            $porcentaje = min(100, round(
                ($usuario['puntos_nivel_actual'] / $usuario['puntos_siguiente_nivel']) * 100
            ));
        ?>
        <div class="perfil-progreso">
            <p>Progreso al siguiente nivel: <strong><?= $porcentaje ?>%</strong></p>
            <div class="barra-progreso">
                <div class="barra-relleno" style="width: <?= $porcentaje ?>%"></div>
            </div>
            <p class="progreso-puntos">
                <?= $usuario['puntos_nivel_actual'] ?> / <?= $usuario['puntos_siguiente_nivel'] ?> puntos
            </p>
        </div>
    <?php endif; ?>

    <!-- Insignias -->
    <?php if (!empty($insignias)): ?>
        <section class="perfil-seccion">
            <h2>Insignias obtenidas</h2>
            <div class="insignias-grid">
                <?php foreach ($insignias as $insignia): ?>
                    <div class="insignia-tarjeta rareza-<?= $insignia['rareza'] ?>">
                        <div class="insignia-icono"></div>
                        <h4><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 12c-3.314 0-6 2.687-6 6s2.686 6 6 6 6-2.687 6-6-2.686-6-6-6zm0 11c-2.757 0-5-2.243-5-5s2.243-5 5-5 5 2.243 5 5-2.243 5-5 5zm0-9c-2.206 0-4 1.794-4 4s1.794 4 4 4 4-1.794 4-4-1.794-4-4-4zm1.39 5.939l-1.39-.743-1.39.744.276-1.552-1.136-1.094 1.562-.215.688-1.419.688 1.419 1.562.215-1.137 1.093.277 1.552zm-9.732-17.925l-.658.986 5.234 7.946c.355-.19.731-.345 1.116-.48l-5.692-8.452zm11.537.986l3.051-3h-12.539l3.293 3h6.195zm4.699-1.661l-.667-1-6.605 9.692c.437.034.859.112 1.274.213l5.998-8.905zm-15.787.001l5.996 8.903c.397-.097.802-.173 1.219-.208l-6.571-9.66-.644.965zm10.541 9.127c.369.13.73.277 1.073.458l5.279-7.925-.657-.986-5.695 8.453z"/></svg><br><?= htmlspecialchars($insignia['nombre']) ?></h4>
                        <p><?= htmlspecialchars($insignia['descripcion']) ?></p>
                        <small><?= date('d/m/Y', strtotime($insignia['fecha_obtencion'])) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Historial de actividad -->
    <?php if (!empty($historial)): ?>
        <section class="perfil-seccion">
            <h2>Actividad reciente</h2>
            <ul class="historial-lista">
                <?php foreach ($historial as $actividad): ?>
                    <li class="historial-item">
                        <span class="historial-desc"><?= htmlspecialchars($actividad['descripcion']) ?></span>
                        <?php if ($actividad['puntos_obtenidos'] > 0): ?>
                            <span class="historial-puntos">+<?= $actividad['puntos_obtenidos'] ?> pts</span>
                        <?php endif; ?>
                        <span class="historial-fecha">
                            <?= date('d/m/Y H:i', strtotime($actividad['fecha_actividad'])) ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

</div>