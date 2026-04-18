<div class="educadores-contenedor">
    <div class="seccion-cabecera">
        <h1>🖼️ Imágenes: <?= htmlspecialchars($especie['nombre_comun']) ?></h1>
        <a href="<?= BASE_URL ?>/educadores/especies" class="btn-secundario">← Volver</a>
    </div>

    <?php if (!empty($errores)): ?>
        <div class="alertas errores">
            <?php foreach ($errores as $error): ?>
                <p>⚠️ <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Subir nueva imagen -->
    <div class="form-seccion">
        <h3>Subir nueva imagen</h3>
        <form method="POST" enctype="multipart/form-data"
              action="<?= BASE_URL ?>/educadores/imagenes/<?= $especie['id_especie'] ?>">
            <div class="form-grid">
                <div class="campo">
                    <label>Imagen (JPG, PNG, WEBP - máx. 5MB) *</label>
                    <input type="file" name="imagen" accept="image/*" required>
                </div>
                <div class="campo">
                    <label>Descripción</label>
                    <input type="text" name="descripcion" placeholder="Ej: En vuelo sobre el valle">
                </div>
                <div class="campo">
                    <label>Tipo</label>
                    <select name="tipo">
                        <option value="foto">Foto</option>
                        <option value="silueta">Silueta</option>
                        <option value="vuelo">En vuelo</option>
                        <option value="juvenil">Juvenil</option>
                        <option value="habitat">Hábitat</option>
                    </select>
                </div>
                <div class="campo">
                    <label>Créditos / Autor *</label>
                    <input type="text" name="creditos" placeholder="© Autor / Fuente" required>
                </div>
                <div class="campo">
                    <label>
                        <input type="checkbox" name="es_principal" value="1">
                        Usar como imagen principal
                    </label>
                </div>
            </div>
            <button type="submit" class="btn-principal">Subir imagen</button>
        </form>
    </div>

    <!-- Imágenes existentes -->
    <?php if (!empty($imagenes)): ?>
        <div class="form-seccion">
            <h3>Imágenes actuales</h3>
            <div class="imagenes-grid">
                <?php foreach ($imagenes as $img): ?>
                    <div class="imagen-tarjeta <?= $img['es_principal'] ? 'imagen-principal' : '' ?>">
                        <img src="<?= BASE_URL ?>/public/img/especies/<?= htmlspecialchars($img['ruta_imagen']) ?>"
                             alt="<?= htmlspecialchars($img['descripcion'] ?? '') ?>">
                        <?php if ($img['es_principal']): ?>
                            <span class="badge-principal">⭐ Principal</span>
                        <?php endif; ?>
                        <p class="imagen-tipo"><?= htmlspecialchars($img['tipo']) ?></p>
                        <p class="imagen-creditos"><?= htmlspecialchars($img['creditos']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>