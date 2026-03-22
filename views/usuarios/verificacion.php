<div class="formulario-contenedor texto-centro">
    <?php if ($verificado): ?>
        <h1>Cuenta verificada</h1>
        <p>Tu cuenta ha sido activada correctamente.</p>
        <a href="<?= BASE_URL ?>/usuarios/login" class="btn-principal">Iniciar sesión</a>
    <?php else: ?>
        <h1>Enlace no válido</h1>
        <p>El enlace de verificación no es válido o ya ha sido usado.</p>
        <a href="<?= BASE_URL ?>">Volver al inicio</a>
    <?php endif; ?>
</div>