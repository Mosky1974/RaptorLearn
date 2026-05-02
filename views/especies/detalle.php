<article class="detalle-especie">

    <!-- Cabecera -->
    <div class="especie-cabecera">
        <?php
        $imagenPrincipal = null;
        foreach ($especie['imagenes'] as $img) {
            if ($img['es_principal']) {
                $imagenPrincipal = $img;
                break;
            }
        }
        ?>
        <?php if ($imagenPrincipal): ?>
            <figure class="especie-figura">
                <img src="<?= BASE_URL ?>/public/img/especies/<?= htmlspecialchars($imagenPrincipal['ruta_imagen']) ?>"
                     alt="<?= htmlspecialchars($especie['nombre_comun']) ?>">
                <figcaption><?= htmlspecialchars($imagenPrincipal['creditos']) ?></figcaption>
            </figure>
        <?php endif; ?>

        <div class="especie-titulo">
            <h1><?= htmlspecialchars($especie['nombre_comun']) ?></h1>
            <h2><em><?= htmlspecialchars($especie['nombre_cientifico']) ?></em></h2>
            <?php if (!empty($especie['nombre_ingles'])): ?>
                <p class="nombre-ingles"><?= htmlspecialchars($especie['nombre_ingles']) ?></p>
            <?php endif; ?>
            <span class="conservacion estado-<?= strtolower($especie['estado_conservacion']) ?>">
                <?= htmlspecialchars($especie['estado_conservacion']) ?>
            </span>
            <span class="dificultad">
                Identificación: <?= htmlspecialchars($especie['dificultad_identificacion']) ?>
            </span>
        </div>
    </div>
    <!-- Galería de imágenes -->
    <?php
    $imagenesGaleria = array_filter($especie['imagenes'], fn($img) => !$img['es_principal']);
    ?>
    <?php if (!empty($imagenesGaleria)): ?>
        <section class="especie-seccion">
            <h3>📷 Galería de imágenes</h3>
            <div class="galeria-grid">
                <?php foreach ($imagenesGaleria as $img): ?>
                    <figure class="galeria-item" onclick="abrirLightbox('<?= BASE_URL ?>/public/img/especies/<?= htmlspecialchars($img['ruta_imagen']) ?>', '<?= htmlspecialchars(addslashes($img['creditos'] ?? '')) ?>')">
                        <img src="<?= BASE_URL ?>/public/img/especies/<?= htmlspecialchars($img['ruta_imagen']) ?>"
                            alt="<?= htmlspecialchars($img['descripcion'] ?? $especie['nombre_comun']) ?>">
                        <?php if (!empty($img['descripcion'])): ?>
                            <figcaption><?= htmlspecialchars($img['descripcion']) ?></figcaption>
                        <?php endif; ?>
                    </figure>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- Lightbox -->
    <div class="lightbox" id="lightbox" onclick="cerrarLightbox()">
        <button class="lightbox-cerrar" onclick="cerrarLightbox()">✕</button>
        <figure class="lightbox-contenido">
            <img id="lightbox-img" src="" alt="">
            <figcaption id="lightbox-creditos"></figcaption>
        </figure>
    </div>

    <script>
    function abrirLightbox(src, creditos) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox-creditos').textContent = creditos;
        document.getElementById('lightbox').classList.add('activo');
        document.body.style.overflow = 'hidden';
    }

    function cerrarLightbox() {
        document.getElementById('lightbox').classList.remove('activo');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') cerrarLightbox();
    });
    </script>

    <!-- Descripción -->
    <section class="especie-seccion">
        <h3>Descripción</h3>
        <p><?= htmlspecialchars($especie['descripcion']) ?></p>
    </section>

    <!-- Características físicas -->
    <section class="especie-seccion">
        <h3>Características físicas</h3>
        <p><?= htmlspecialchars($especie['caracteristicas_fisicas']) ?></p>
        <div class="especie-medidas">
            <div class="medida">
                <span class="medida-valor"><?= $especie['envergadura_min'] ?>–<?= $especie['envergadura_max'] ?> m</span>
                <span class="medida-label">Envergadura</span>
            </div>
            <div class="medida">
                <span class="medida-valor"><?= $especie['peso_min'] ?>–<?= $especie['peso_max'] ?> g</span>
                <span class="medida-label">Peso</span>
            </div>
            <div class="medida">
                <span class="medida-valor"><?= $especie['longitud_min'] ?>–<?= $especie['longitud_max'] ?> cm</span>
                <span class="medida-label">Longitud</span>
            </div>
        </div>
        <?php if (!empty($especie['dimorfismo_sexual'])): ?>
            <p><strong>Dimorfismo sexual:</strong> <?= htmlspecialchars($especie['dimorfismo_sexual']) ?></p>
        <?php endif; ?>
    </section>

    <!-- Hábitat y distribución -->
    <section class="especie-seccion">
        <h3>Hábitat y distribución</h3>
        <p><?= htmlspecialchars($especie['habitat']) ?></p>
        <p><?= htmlspecialchars($especie['distribucion_geografica']) ?></p>
        <?php if (!empty($especie['altitud_min']) || !empty($especie['altitud_max'])): ?>
            <p><strong>Altitud:</strong> <?= $especie['altitud_min'] ?>–<?= $especie['altitud_max'] ?> m</p>
        <?php endif; ?>
    </section>

    <!-- Dieta -->
    <section class="especie-seccion">
        <h3>Dieta y comportamiento de caza</h3>
        <p><?= htmlspecialchars($especie['dieta']) ?></p>
        <?php if (!empty($especie['comportamiento_caza'])): ?>
            <p><?= htmlspecialchars($especie['comportamiento_caza']) ?></p>
        <?php endif; ?>
    </section>

    <!-- Reproducción -->
    <section class="especie-seccion">
        <h3>Reproducción</h3>
        <p><?= htmlspecialchars($especie['reproduccion']) ?></p>
        <p><strong>Época de cría:</strong> <?= htmlspecialchars($especie['epoca_cria']) ?></p>
        <p><strong>Número de huevos:</strong> <?= htmlspecialchars($especie['numero_huevos']) ?></p>
    </section>

    <!-- Conservación -->
    <section class="especie-seccion">
        <h3>Estado de conservación y amenazas</h3>
        <p><?= htmlspecialchars($especie['amenazas']) ?></p>
        <p><strong>Medidas de conservación:</strong> <?= htmlspecialchars($especie['medidas_conservacion']) ?></p>
        <p><strong>Población ibérica:</strong> <?= htmlspecialchars($especie['poblacion_iberica']) ?></p>
    </section>

    <!-- Curiosidades -->
    <?php if (!empty($especie['curiosidades'])): ?>
        <section class="especie-seccion especie-curiosidades">
            <h3>💡 ¿Sabías que...?</h3>
            <p><?= htmlspecialchars($especie['curiosidades']) ?></p>
        </section>
    <?php endif; ?>

    <!-- Audios -->
    <?php if (!empty($especie['audios'])): ?>
        <section class="especie-seccion">
            <h3>🔊 Cantos y llamadas</h3>
            <?php foreach ($especie['audios'] as $audio): ?>
                <div class="audio-item">
                    <p><?= htmlspecialchars($audio['descripcion'] ?? $audio['tipo_canto']) ?></p>
                    <audio controls>
                        <source src="<?= BASE_URL ?>/public/audio/<?= htmlspecialchars($audio['ruta_audio']) ?>">
                    </audio>
                    <?php if (!empty($audio['creditos'])): ?>
                        <small><?= htmlspecialchars($audio['creditos']) ?></small>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <!-- Mapa de distribución -->
    <section class="especie-seccion">
        <h3>🗺️ Distribución en la Península Ibérica</h3>

        <?php
        $distribucion = $modeloEspecies->obtenerDistribucion($especie['id_especie']);
        $distribucionJson = json_encode($distribucion, JSON_UNESCAPED_UNICODE);
        ?>

        <div class="mapa-leyenda">
            <span class="mapa-badge residente">Residente</span>
            <span class="mapa-badge reproductor">Reproductor</span>
            <span class="mapa-badge invernante">Invernante</span>
            <span class="mapa-badge migrante">Migrante</span>
            <span class="mapa-badge ocasional">Ocasional</span>
        </div>

        <div id="mapa-distribucion"></div>

        <?php if (!empty($distribucion)): ?>
            <div class="mapa-tabla">
                <table class="ranking-tabla">
                    <thead>
                        <tr>
                            <th>Comunidad</th>
                            <th>Presencia</th>
                            <th>Población estimada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($distribucion as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['nombre']) ?></td>
                                <td><span class="mapa-badge <?= htmlspecialchars($d['presencia']) ?>"><?= ucfirst(htmlspecialchars($d['presencia'])) ?></span></td>
                                <td><?= htmlspecialchars($d['poblacion_estimada'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>

    <script>
    const distribucionData = <?= $distribucionJson ?>;
    const colores = {
        residente:   '#2d6a4f',
        reproductor: '#52b788',
        invernante:  '#74c69d',
        migrante:    '#f4a261',
        ocasional:   '#e9c46a',
    };

    const mapa = L.map('mapa-distribucion', {
        center: [40.0, -3.5],
        zoom: 5,
        zoomControl: true,
        scrollWheelZoom: false,
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 10,
    }).addTo(mapa);

    fetch('<?= BASE_URL ?>/public/js/geo/comunidades.geojson')
        .then(r => r.json())
        .then(geojson => {
            L.geoJSON(geojson, {
                style: feature => {
                    const nombre = feature.properties.name;
                    const datos  = distribucionData.find(d =>
                        nombre && d.nombre && nombre.toLowerCase().includes(d.nombre.toLowerCase().split(' ')[0])
                    );
                    return {
                        fillColor:   datos ? (colores[datos.presencia] || '#cccccc') : '#eeeeee',
                        fillOpacity: datos ? 0.75 : 0.3,
                        color:       '#ffffff',
                        weight:      1.5,
                    };
                },
                onEachFeature: (feature, layer) => {
                    const nombre = feature.properties.name;
                    const datos  = distribucionData.find(d =>
                        nombre && d.nombre && nombre.toLowerCase().includes(d.nombre.toLowerCase().split(' ')[0])
                    );
                    if (datos) {
                        layer.bindPopup(
                            `<strong>${datos.nombre}</strong><br>
                            Presencia: <em>${datos.presencia}</em><br>
                            ${datos.poblacion_estimada ? 'Población: ' + datos.poblacion_estimada : ''}`
                        );
                    } else {
                        layer.bindPopup(`<strong>${nombre}</strong><br><em>Sin datos para esta especie</em>`);
                    }
                    layer.on('mouseover', () => layer.setStyle({ fillOpacity: 0.95, weight: 2.5 }));
                    layer.on('mouseout',  () => layer.setStyle({ fillOpacity: datos ? 0.75 : 0.3, weight: 1.5 }));
                }
            }).addTo(mapa);
        });
    </script>

    <div class="especie-volver">
        <a href="<?= BASE_URL ?>/especies" class="btn-secundario">← Volver a la enciclopedia</a>
    </div>

</article>