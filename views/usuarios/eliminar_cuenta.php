<div class="formulario-contenedor">
    <h1>Eliminar cuenta</h1>

    <div class="alertas errores">
        <p>Esta acción es <strong>permanente e irreversible</strong>. Se eliminarán todos tus datos, progreso, insignias e historial de actividad.</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alertas errores">
            <p><?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/usuarios/eliminarCuenta">
        <div class="campo">
            <label for="password">Introduce tu contraseña para confirmar</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn-eliminar"
                onclick="return confirm('¿Estás completamente seguro? Esta acción no se puede deshacer.')">
            Eliminar mi cuenta definitivamente
        </button>
        <a href="<?= BASE_URL ?>/usuarios/perfil" class="btn-secundario" style="margin-top:0.5rem;display:block;text-align:center;">
            Cancelar
        </a>
    </form>
</div>