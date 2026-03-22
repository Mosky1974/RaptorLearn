<div class="formulario-contenedor">
    <h1>Nueva contraseña</h1>

    <?php if (!empty($error)): ?>
        <div class="alertas errores">
            <p>Error: <?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/usuarios/resetPassword/<?= htmlspecialchars($token) ?>">
        <div class="campo">
            <label for="password">Nueva contraseña (mínimo 8 caracteres)</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="campo">
            <label for="password_confirm">Repetir contraseña</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        <button type="submit" class="btn-principal">Guardar contraseña</button>
    </form>
</div>