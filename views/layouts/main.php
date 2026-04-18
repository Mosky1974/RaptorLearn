<?php
// $contenido y $titulo vienen del controlador vía extract()
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>

    <header>
        <nav>
            <a href="<?= BASE_URL ?>"><strong>🦅 RaptorLearn</strong></a>
            <ul>
                <li><a href="<?= BASE_URL ?>/especies">Enciclopedia</a></li>
                <li><a href="<?= BASE_URL ?>/juegos">Mini-juegos</a></li>
                <li><a href="<?= BASE_URL ?>/educadores">Educadores</a></li>
                <?php if (!empty($_SESSION['usuario_id'])): ?>
                    <li><a href="<?= BASE_URL ?>/usuarios/perfil">Mi perfil</a></li>
                    <li><a href="<?= BASE_URL ?>/usuarios/logout">Salir</a></li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>/usuarios/login">Entrar</a></li>
                    <li><a href="<?= BASE_URL ?>/usuarios/registro">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <?php if (!empty($_SESSION['nivel_nuevo'])): ?>
            <div class="notificacion-nivel">
                🎉 ¡Felicidades! Has subido al nivel <strong><?= htmlspecialchars($_SESSION['nivel_nuevo']) ?></strong>
                <button onclick="this.parentElement.remove()">✕</button>
            </div>
            <?php unset($_SESSION['nivel_nuevo']); ?>
        <?php endif; ?>
        <?php if (!empty($_SESSION['especie_nueva'])): ?>
            <div class="notificacion-especie">
                🦅 ¡Nueva especie descubierta! <strong><?= htmlspecialchars($_SESSION['especie_nueva']) ?></strong>
                <button onclick="this.parentElement.remove()">✕</button>
            </div>
            <?php unset($_SESSION['especie_nueva']); ?>
        <?php endif; ?>
        <?php require_once $contenido; ?>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> RaptorLearn - Portal Educativo sobre Rapaces Ibéricas - by JBC</p>
    </footer>

    <script src="<?= BASE_URL ?>/public/js/main.js"></script>
</body>
</html>