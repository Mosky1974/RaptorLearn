<div class="formulario-contenedor">
    <h1>Editar perfil</h1>

    <?php if (!empty($errores)): ?>
        <div class="alertas errores">
            <?php foreach ($errores as $error): ?>
                <p>Error: <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/usuarios/editarPerfil" enctype="multipart/form-data">

        <div class="campo">
            <label>Avatar actual</label>
            <?php if (!empty($usuario['avatar'])): ?>
                <img src="<?= BASE_URL ?>/public/img/avatares/<?= htmlspecialchars($usuario['avatar']) ?>"
                     alt="Avatar" class="avatar-preview">
            <?php else: ?>
                <p class="texto-gris">Sin avatar</p>
            <?php endif; ?>
        </div>

        <div class="campo">
            <label for="avatar">Cambiar avatar (JPG, PNG, máx. 2MB)</label>
            <input type="file" id="avatar" name="avatar" accept="image/*">
        </div>

        <div class="campo">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre"
                   value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
        </div>

        <div class="campo">
            <label for="apellidos">Apellidos</label>
            <input type="text" id="apellidos" name="apellidos"
                   value="<?= htmlspecialchars($usuario['apellidos'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="fecha_nacimiento">Fecha de nacimiento</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                   value="<?= htmlspecialchars($usuario['fecha_nacimiento'] ?? '') ?>">
        </div>

        <button type="submit" class="btn-principal">Guardar cambios</button>
        <a href="<?= BASE_URL ?>/usuarios/perfil" class="btn-secundario">Cancelar</a>
    </form>
</div>