<div class="educadores-contenedor">
    <h1><?= htmlspecialchars($titulo) ?></h1>

    <?php if (!empty($errores)): ?>
        <div class="alertas errores">
            <?php foreach ($errores as $error): ?>
                <p>⚠️ <?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= BASE_URL ?>/educadores/<?= $editar ? 'editarEspecie/' . $especie['id_especie'] : 'crearEspecie' ?>">

        <div class="form-seccion">
            <h3>Datos básicos</h3>
            <div class="form-grid">
                <div class="campo">
                    <label>Nombre común *</label>
                    <input type="text" name="nombre_comun" required
                           value="<?= htmlspecialchars($especie['nombre_comun'] ?? '') ?>">
                </div>
                <div class="campo">
                    <label>Nombre científico *</label>
                    <input type="text" name="nombre_cientifico" required
                           value="<?= htmlspecialchars($especie['nombre_cientifico'] ?? '') ?>">
                </div>
                <div class="campo">
                    <label>Nombre en inglés</label>
                    <input type="text" name="nombre_ingles"
                           value="<?= htmlspecialchars($especie['nombre_ingles'] ?? '') ?>">
                </div>
                <div class="campo">
                    <label>Familia</label>
                    <input type="text" name="familia"
                           value="<?= htmlspecialchars($especie['familia'] ?? '') ?>">
                </div>
                <div class="campo">
                    <label>Orden</label>
                    <input type="text" name="orden"
                           value="<?= htmlspecialchars($especie['orden'] ?? 'Accipitriformes') ?>">
                </div>
                <div class="campo">
                    <label>Estado de conservación</label>
                    <select name="estado_conservacion">
                        <?php foreach (['LC','NT','VU','EN','CR','EW','EX'] as $estado): ?>
                            <option value="<?= $estado ?>" 
                                <?= ($especie['estado_conservacion'] ?? 'LC') === $estado ? 'selected' : '' ?>>
                                <?= $estado ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="campo">
                    <label>Dificultad de identificación</label>
                    <select name="dificultad_identificacion">
                        <?php foreach (['fácil','medio','difícil'] as $dif): ?>
                            <option value="<?= $dif ?>"
                                <?= ($especie['dificultad_identificacion'] ?? 'medio') === $dif ? 'selected' : '' ?>>
                                <?= ucfirst($dif) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-seccion">
            <h3>Descripción</h3>
            <div class="campo">
                <label>Descripción general *</label>
                <textarea name="descripcion" rows="4" required><?= htmlspecialchars($especie['descripcion'] ?? '') ?></textarea>
            </div>
            <div class="campo">
                <label>Características físicas</label>
                <textarea name="caracteristicas_fisicas" rows="3"><?= htmlspecialchars($especie['caracteristicas_fisicas'] ?? '') ?></textarea>
            </div>
            <div class="campo">
                <label>Dimorfismo sexual</label>
                <textarea name="dimorfismo_sexual" rows="2"><?= htmlspecialchars($especie['dimorfismo_sexual'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-seccion">
            <h3>Medidas</h3>
            <div class="form-grid">
                <div class="campo">
                    <label>Envergadura mín. (m)</label>
                    <input type="number" step="0.01" name="envergadura_min"
                           value="<?= $especie['envergadura_min'] ?? '' ?>">
                </div>
                <div class="campo">
                    <label>Envergadura máx. (m)</label>
                    <input type="number" step="0.01" name="envergadura_max"
                           value="<?= $especie['envergadura_max'] ?? '' ?>">
                </div>
                <div class="campo">
                    <label>Peso mín. (g)</label>
                    <input type="number" step="0.01" name="peso_min"
                           value="<?= $especie['peso_min'] ?? '' ?>">
                </div>
                <div class="campo">
                    <label>Peso máx. (g)</label>
                    <input type="number" step="0.01" name="peso_max"
                           value="<?= $especie['peso_max'] ?? '' ?>">
                </div>
                <div class="campo">
                    <label>Longitud mín. (cm)</label>
                    <input type="number" step="0.01" name="longitud_min"
                           value="<?= $especie['longitud_min'] ?? '' ?>">
                </div>
                <div class="campo">
                    <label>Longitud máx. (cm)</label>
                    <input type="number" step="0.01" name="longitud_max"
                           value="<?= $especie['longitud_max'] ?? '' ?>">
                </div>
                <div class="campo">
                    <label>Altitud mín. (m)</label>
                    <input type="number" name="altitud_min"
                           value="<?= $especie['altitud_min'] ?? '' ?>">
                </div>
                <div class="campo">
                    <label>Altitud máx. (m)</label>
                    <input type="number" name="altitud_max"
                           value="<?= $especie['altitud_max'] ?? '' ?>">
                </div>
            </div>
        </div>

        <div class="form-seccion">
            <h3>Ecología</h3>
            <div class="campo">
                <label>Hábitat</label>
                <textarea name="habitat" rows="2"><?= htmlspecialchars($especie['habitat'] ?? '') ?></textarea>
            </div>
            <div class="campo">
                <label>Distribución geográfica</label>
                <textarea name="distribucion_geografica" rows="2"><?= htmlspecialchars($especie['distribucion_geografica'] ?? '') ?></textarea>
            </div>
            <div class="campo">
                <label>Dieta</label>
                <textarea name="dieta" rows="2"><?= htmlspecialchars($especie['dieta'] ?? '') ?></textarea>
            </div>
            <div class="campo">
                <label>Comportamiento de caza</label>
                <textarea name="comportamiento_caza" rows="2"><?= htmlspecialchars($especie['comportamiento_caza'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-seccion">
            <h3>Reproducción</h3>
            <div class="form-grid">
                <div class="campo">
                    <label>Reproducción</label>
                    <textarea name="reproduccion" rows="2"><?= htmlspecialchars($especie['reproduccion'] ?? '') ?></textarea>
                </div>
                <div class="campo">
                    <label>Época de cría</label>
                    <input type="text" name="epoca_cria"
                           value="<?= htmlspecialchars($especie['epoca_cria'] ?? '') ?>">
                </div>
                <div class="campo">
                    <label>Número de huevos</label>
                    <input type="text" name="numero_huevos"
                           value="<?= htmlspecialchars($especie['numero_huevos'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="form-seccion">
            <h3>Conservación</h3>
            <div class="campo">
                <label>Población ibérica</label>
                <textarea name="poblacion_iberica" rows="2"><?= htmlspecialchars($especie['poblacion_iberica'] ?? '') ?></textarea>
            </div>
            <div class="campo">
                <label>Amenazas</label>
                <textarea name="amenazas" rows="2"><?= htmlspecialchars($especie['amenazas'] ?? '') ?></textarea>
            </div>
            <div class="campo">
                <label>Medidas de conservación</label>
                <textarea name="medidas_conservacion" rows="2"><?= htmlspecialchars($especie['medidas_conservacion'] ?? '') ?></textarea>
            </div>
            <div class="campo">
                <label>Curiosidades</label>
                <textarea name="curiosidades" rows="2"><?= htmlspecialchars($especie['curiosidades'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" class="btn-principal">
                <?= $editar ? 'Guardar cambios' : 'Crear especie' ?>
            </button>
            <a href="<?= BASE_URL ?>/educadores/especies" class="btn-secundario">Cancelar</a>
        </div>
    </form>
</div>