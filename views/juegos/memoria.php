<div class="memoria-contenedor">
    <h1>🃏 <?= htmlspecialchars($titulo) ?></h1>
    <p class="texto-gris">Encuentra las parejas de especies iguales.</p>

    <div class="memoria-stats">
        <span id="memoria-timer">⏱️ 0s</span>
        <span id="memoria-intentos">Intentos: 0</span>
        <span id="memoria-parejas">Parejas: 0 / <?= count($especies) ?></span>
    </div>

    <div class="memoria-tablero" id="tablero-memoria"></div>

    <div class="memoria-completado" id="memoria-completado" style="display:none;">
        <h2>🎉 ¡Memoria completada!</h2>
        <p id="memoria-resultado-texto"></p>
        <a href="<?= BASE_URL ?>/juegos/memoria" class="btn-principal">Jugar otra vez</a>
        <a href="<?= BASE_URL ?>/juegos" class="btn-secundario">Volver a mini-juegos</a>
    </div>
</div>

<script>
const especies = <?= json_encode($especies, JSON_UNESCAPED_UNICODE) ?>;
const baseUrlImg = '<?= BASE_URL ?>/public/img/especies/';
const tablero = document.getElementById('tablero-memoria');

// Crear pares de cartas y desordenar
let cartas = [];
especies.forEach(esp => {
    cartas.push({ id: esp.id_especie, img: esp.ruta_imagen, nombre: esp.nombre_comun });
    cartas.push({ id: esp.id_especie, img: esp.ruta_imagen, nombre: esp.nombre_comun });
});
cartas.sort(() => Math.random() - 0.5);

let primeraCarta = null;
let segundaCarta = null;
let bloqueado = false;
let intentos = 0;
let parejas = 0;
let segundos = 0;
const totalParejas = especies.length;

cartas.forEach((carta, index) => {
    const el = document.createElement('div');
    el.className = 'memoria-carta';
    el.dataset.id = carta.id;
    el.dataset.index = index;
    el.innerHTML = `
        <div class="memoria-carta-inner">
            <div class="memoria-carta-frente">🦅</div>
            <div class="memoria-carta-dorso" style="background-image:url('${baseUrlImg}${carta.img}')"></div>
        </div>
    `;
    el.addEventListener('click', () => seleccionarCarta(el, carta));
    tablero.appendChild(el);
});

function seleccionarCarta(el, carta) {
    if (bloqueado || el.classList.contains('volteada') || el.classList.contains('encontrada')) return;

    el.classList.add('volteada');

    if (!primeraCarta) {
        primeraCarta = { el, carta };
        return;
    }

    segundaCarta = { el, carta };
    intentos++;
    document.getElementById('memoria-intentos').textContent = `Intentos: ${intentos}`;
    bloqueado = true;

    if (primeraCarta.carta.id === segundaCarta.carta.id) {
        // Pareja encontrada
        primeraCarta.el.classList.add('encontrada');
        segundaCarta.el.classList.add('encontrada');
        parejas++;
        document.getElementById('memoria-parejas').textContent = `Parejas: ${parejas} / ${totalParejas}`;
        resetSeleccion();

        if (parejas === totalParejas) {
            finalizarMemoria();
        }
    } else {
        // No coinciden: voltear de vuelta tras una pausa
        setTimeout(() => {
            primeraCarta.el.classList.remove('volteada');
            segundaCarta.el.classList.remove('volteada');
            resetSeleccion();
        }, 900);
    }
}

function resetSeleccion() {
    primeraCarta = null;
    segundaCarta = null;
    bloqueado = false;
}

const timerInterval = setInterval(() => {
    segundos++;
    document.getElementById('memoria-timer').textContent = `⏱️ ${segundos}s`;
}, 1000);

function finalizarMemoria() {
    clearInterval(timerInterval);

    fetch('<?= BASE_URL ?>/juegos/finalizarMemoria', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `intentos=${intentos}&parejas=${parejas}`
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('tablero-memoria').style.display = 'none';
        document.getElementById('memoria-completado').style.display = 'block';
        document.getElementById('memoria-resultado-texto').textContent =
            `Has ganado ${data.puntuacion} puntos en ${data.tiempoEmpleado} segundos con ${intentos} intentos.`;
    });
}
</script>