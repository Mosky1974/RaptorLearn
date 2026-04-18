<div class="educadores-contenedor">
    <h1>👩‍🏫 Área de Educadores</h1>
    <p>Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>. Desde aquí puedes gestionar el contenido de RaptorLearn.</p>

    <div class="educadores-grid">
        <a href="<?= BASE_URL ?>/educadores/especies" class="educador-tarjeta">
            <div class="educador-icono">🦅</div>
            <h3>Gestión de Especies</h3>
            <p>Crea y edita fichas de rapaces ibéricas.</p>
            <span class="educador-stat"><?= $totalEspecies ?> especies en total</span>
        </a>

        <a href="<?= BASE_URL ?>/educadores/cuestionarios" class="educador-tarjeta">
            <div class="educador-icono">❓</div>
            <h3>Mis Cuestionarios</h3>
            <p>Crea y edita cuestionarios educativos.</p>
            <span class="educador-stat"><?= $misCuestionarios ?> creados por ti</span>
        </a>
    </div>
</div>