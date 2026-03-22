<div class="formulario-contenedor">
    <h1>Cambiar contraseña</h1>

    <?php if ($ok): ?>
        <div class="alertas exito">
            <p>Contraseña actualizada correctamente.</p>
        </div>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
        <div class="alertas errores">
            <?php foreach ($errores as $error): ?>
                <p>Error: <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/usuarios/cambiarPassword">
        <div class="campo">
            <label for="password_actual">Contraseña actual</label>
            <input type="password" id="password_actual" name="password_actual" required>
        </div>
        <div class="campo">
            <label for="password_nueva">Nueva contraseña (mínimo 8 caracteres)</label>
            <input type="password" id="password_nueva" name="password_nueva" required>
        </div>
        <div class="campo">
            <label for="password_confirm">Repetir nueva contraseña</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        <button type="submit" class="btn-principal">Cambiar contraseña</button>
        <a href="<?= BASE_URL ?>/usuarios/perfil" class="btn-secundario">Cancelar</a>
    </form>
</div>