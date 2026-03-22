<div class="formulario-contenedor">
    <h1>Iniciar sesión</h1>

    <?php if (!empty($error)): ?>
        <div class="alertas errores">
            <p>Alertas: <?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/usuarios/login">
        <div class="campo">
            <label for="email">Email</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($email ?? '') ?>" required>
        </div>

        <div class="campo">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn-principal">Entrar</button>

        <p class="enlace-alternativo">
            <a href="<?= BASE_URL ?>/usuarios/recuperar">¿Olvidaste tu contraseña?</a>
        </p>
        <p class="enlace-alternativo">
            ¿No tienes cuenta? <a href="<?= BASE_URL ?>/usuarios/registro">Regístrate</a>
        </p>
    </form>
</div>