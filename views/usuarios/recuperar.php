<div class="formulario-contenedor">
    <h1>Recuperar contraseña</h1>
    <p>Introduce tu email y te enviaremos un enlace para restablecer tu contraseña.</p>

    <form method="POST" action="<?= BASE_URL ?>/usuarios/recuperar">
        <div class="campo">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
        </div>
        <button type="submit" class="btn-principal">Enviar enlace</button>
        <p class="enlace-alternativo">
            <a href="<?= BASE_URL ?>/usuarios/login">Volver al login</a>
        </p>
    </form>
</div>