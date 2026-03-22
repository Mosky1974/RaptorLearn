<div class="formulario-contenedor">
    <h1>Crear cuenta</h1>

    <?php if (!empty($errores)): ?>
        <div class="alertas errores">
            <?php foreach ($errores as $error): ?>
                <p>Error: <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/usuarios/registro">
        <div class="campo">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre" 
                   value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" required>
        </div>

        <div class="campo">
            <label for="apellidos">Apellidos</label>
            <input type="text" id="apellidos" name="apellidos"
                   value="<?= htmlspecialchars($datos['apellidos'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email"
                   value="<?= htmlspecialchars($datos['email'] ?? '') ?>" required>
        </div>

        <div class="campo">
            <label for="fecha_nacimiento">Fecha de nacimiento</label>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                   value="<?= htmlspecialchars($datos['fecha_nacimiento'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="password">Contraseña * (mínimo 8 caracteres)</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="campo">
            <label for="password_confirm">Repetir contraseña *</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>

        <div class="campo">
            <label>Tipo de usuario *</label>
            <div class="radio-grupo">
                <label>
                    <input type="radio" name="tipo_usuario" value="estudiante"
                        <?= ($datos['tipo_usuario'] ?? 'estudiante') === 'estudiante' ? 'checked' : '' ?>>
                    Estudiante
                </label>
                <label>
                    <input type="radio" name="tipo_usuario" value="educador"
                        <?= ($datos['tipo_usuario'] ?? '') === 'educador' ? 'checked' : '' ?>>
                    Educador
                </label>
            </div>
        </div>

        <button type="submit" class="btn-principal">Crear cuenta</button>

        <p class="enlace-alternativo">
            ¿Ya tienes cuenta? <a href="<?= BASE_URL ?>/usuarios/login">Inicia sesión</a>
        </p>
    </form>
</div>