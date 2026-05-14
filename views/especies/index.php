<h1>Enciclopedia de Rapaces Ibéricas</h1>

<!-- Buscador en vivo -->
<div class="buscador-wrapper">
    <div class="buscador-campo">
        <span class="buscador-icono"></span>
        <input
            type="search"
            id="buscador"
            class="buscador-input"
            placeholder="Busca por nombre, familia..."
            autocomplete="off"
            aria-label="Buscar rapaces"
        >
        <span class="buscador-spinner" id="spinner" aria-hidden="true"></span>
    </div>
    <p class="buscador-ayuda">Escribe al menos 2 caracteres para buscar</p>
</div>

<!-- Resultados de búsqueda (ocultos por defecto) -->
<div id="resultados-busqueda" class="resultados-busqueda" hidden>
    <p class="resultados-info" id="resultados-info"></p>
    <div class="grid-especies" id="grid-busqueda"></div>
</div>

<!-- Listado completo (visible por defecto) -->
<div id="catalogo-completo">
    <p class="catalogo-subtitulo"><?= count($especies) ?> especies catalogadas</p>
    <div class="grid-especies">
        <?php foreach ($especies as $especie): ?>
            <a href="<?= BASE_URL ?>/especies/detalle/<?= $especie['id_especie'] ?>" class="tarjeta-especie">
                <?php if (!empty($especie['ruta_imagen'])): ?>
                    <img src="<?= BASE_URL ?>/public/img/especies/<?= htmlspecialchars($especie['ruta_imagen']) ?>"
                         alt="<?= htmlspecialchars($especie['nombre_comun']) ?>"
                         loading="lazy">
                <?php else: ?>
                    <div class="tarjeta-sin-imagen">🦅</div>
                <?php endif; ?>
                <h3><?= htmlspecialchars($especie['nombre_comun']) ?></h3>
                <em><?= htmlspecialchars($especie['nombre_cientifico']) ?></em>
                <span class="conservacion estado-<?= strtolower($especie['estado_conservacion']) ?>">
                    <?= htmlspecialchars($especie['estado_conservacion']) ?>
                </span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const input      = document.getElementById('buscador');
    const spinner    = document.getElementById('spinner');
    const bloqRes    = document.getElementById('resultados-busqueda');
    const bloqCat    = document.getElementById('catalogo-completo');
    const gridBusq   = document.getElementById('grid-busqueda');
    const infoRes    = document.getElementById('resultados-info');
    const BASE_URL   = '<?= BASE_URL ?>';

    let timer = null;

    function renderTarjeta(e) {
        const img = e.ruta_imagen
            ? `<img src="${BASE_URL}/public/img/especies/${e.ruta_imagen}" alt="${e.nombre_comun}" loading="lazy">`
            : `<div class="tarjeta-sin-imagen"></div>`;

        return `
            <a href="${BASE_URL}/especies/detalle/${e.id_especie}" class="tarjeta-especie">
                ${img}
                <h3>${e.nombre_comun}</h3>
                <em>${e.nombre_cientifico}</em>
                <span class="conservacion estado-${e.estado_conservacion.toLowerCase()}">
                    ${e.estado_conservacion}
                </span>
            </a>`;
    }

    function mostrarResultados(data) {
        spinner.classList.remove('activo');

        if (data.total === 0) {
            infoRes.textContent = `Sin resultados para "${data.termino}"`;
            gridBusq.innerHTML  = '<p class="sin-resultados">No hemos encontrado ninguna rapaz con ese nombre. ¿Pruebas con otro término?</p>';
        } else {
            infoRes.textContent = `${data.total} resultado${data.total !== 1 ? 's' : ''} para "${data.termino}"`;
            gridBusq.innerHTML  = data.resultados.map(renderTarjeta).join('');
        }

        bloqRes.hidden = false;
        bloqCat.hidden = true;
    }

    function mostrarCatalogo() {
        bloqRes.hidden = true;
        bloqCat.hidden = false;
        spinner.classList.remove('activo');
    }

    async function buscar(termino) {
        spinner.classList.add('activo');
        try {
            const res  = await fetch(`${BASE_URL}/api/especies?q=${encodeURIComponent(termino)}`);
            const data = await res.json();
            mostrarResultados(data);
        } catch {
            spinner.classList.remove('activo');
        }
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        const q = this.value.trim();

        if (q.length < 2) {
            mostrarCatalogo();
            return;
        }

        spinner.classList.add('activo');
        timer = setTimeout(() => buscar(q), 350);
    });
});
</script>