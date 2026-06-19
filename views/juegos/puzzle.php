<div class="puzzle-contenedor">
    <h1>🧩 <?= htmlspecialchars($titulo) ?></h1>
    <p class="texto-gris">Arrastra las piezas hasta encajarlas en su posición correcta.</p>

    <div class="puzzle-stats">
        <span id="puzzle-timer">⏱️ 0s</span>
        <span id="puzzle-progreso">0 / 9 piezas</span>
    </div>

    <div class="puzzle-area">
        <div class="puzzle-tablero" id="tablero"></div>
        <div class="puzzle-piezas" id="piezas"></div>
    </div>

    <div class="puzzle-completado" id="puzzle-completado" style="display:none;">
        <h2>🎉 ¡Puzzle completado!</h2>
        <p id="puzzle-resultado-texto"></p>
        <a href="<?= BASE_URL ?>/juegos/puzzle" class="btn-principal">Jugar otra vez</a>
        <a href="<?= BASE_URL ?>/juegos" class="btn-secundario">Volver a mini-juegos</a>
    </div>
</div>

<script>
const FILAS = 3;
const COLUMNAS = 3;
const imagenSrc = '<?= BASE_URL ?>/public/img/especies/<?= htmlspecialchars($especie['ruta_imagen']) ?>';
const tablero = document.getElementById('tablero');
const piezasContenedor = document.getElementById('piezas');

let piezasColocadas = 0;
const totalPiezas = FILAS * COLUMNAS;
let segundos = 0;
let timerInterval;

// Crear celdas del tablero (zonas de destino)
for (let i = 0; i < totalPiezas; i++) {
    const celda = document.createElement('div');
    celda.className = 'puzzle-celda';
    celda.dataset.posicion = i;
    celda.addEventListener('dragover', e => e.preventDefault());
    celda.addEventListener('drop', onDrop);
    tablero.appendChild(celda);
}

// Crear piezas (desordenadas) con fragmento de imagen de fondo
const posiciones = Array.from({length: totalPiezas}, (_, i) => i);
posiciones.sort(() => Math.random() - 0.5);

posiciones.forEach(posicionCorrecta => {
    const pieza = document.createElement('div');
    pieza.className = 'puzzle-pieza';
    pieza.draggable = true;
    pieza.dataset.posicionCorrecta = posicionCorrecta;

    const fila = Math.floor(posicionCorrecta / COLUMNAS);
    const col = posicionCorrecta % COLUMNAS;

    pieza.style.backgroundImage = `url('${imagenSrc}')`;
    pieza.style.backgroundSize = `${COLUMNAS * 100}% ${FILAS * 100}%`;
    pieza.style.backgroundPosition = `${(col / (COLUMNAS - 1)) * 100}% ${(fila / (FILAS - 1)) * 100}%`;

    pieza.addEventListener('dragstart', onDragStart);
    piezasContenedor.appendChild(pieza);
});

function onDragStart(e) {
    e.dataTransfer.setData('text/plain', e.target.dataset.posicionCorrecta);
    e.dataTransfer.setData('application/pieza-id', generarIdTemporal(e.target));
}

let piezaArrastrada = null;
piezasContenedor.addEventListener('dragstart', e => {
    if (e.target.classList.contains('puzzle-pieza')) {
        piezaArrastrada = e.target;
    }
});

function generarIdTemporal(el) {
    if (!el.dataset.tempId) {
        el.dataset.tempId = 'p' + Math.random().toString(36).slice(2);
    }
    return el.dataset.tempId;
}

function onDrop(e) {
    e.preventDefault();
    const celda = e.currentTarget;
    if (celda.children.length > 0) return; // celda ocupada

    const posicionCorrecta = e.dataTransfer.getData('text/plain');
    if (parseInt(posicionCorrecta) === parseInt(celda.dataset.posicion)) {
        // Colocación correcta
        celda.appendChild(piezaArrastrada);
        piezaArrastrada.draggable = false;
        piezaArrastrada.classList.add('colocada');
        piezasColocadas++;
        document.getElementById('puzzle-progreso').textContent = `${piezasColocadas} / ${totalPiezas} piezas`;

        if (piezasColocadas === totalPiezas) {
            finalizarPuzzle();
        }
    } else {
        // Pieza incorrecta: vuelve a la zona de piezas con animación
        piezaArrastrada.classList.add('pieza-error');
        setTimeout(() => piezaArrastrada.classList.remove('pieza-error'), 400);
    }
}

// Temporizador
timerInterval = setInterval(() => {
    segundos++;
    document.getElementById('puzzle-timer').textContent = `⏱️ ${segundos}s`;
}, 1000);

function finalizarPuzzle() {
    clearInterval(timerInterval);

    fetch('<?= BASE_URL ?>/juegos/finalizarPuzzle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    })
    .then(r => r.json())
    .then(data => {
        document.querySelector('.puzzle-area').style.display = 'none';
        document.getElementById('puzzle-completado').style.display = 'block';
        document.getElementById('puzzle-resultado-texto').textContent =
            `Has ganado ${data.puntuacion} puntos en ${data.tiempoEmpleado} segundos.`;
    });
}
</script>