<?php
class EspecieModel extends Model {

    protected string $tabla = 'especies';

    /**
     * Obtener todas las especies con su imagen principal
     */
    public function obtenerTodos(): array {
        $stmt = $this->db->query(
            "SELECT e.id_especie, e.nombre_comun, e.nombre_cientifico,
                    e.estado_conservacion, e.dificultad_identificacion,
                    e.familia, e.activa,
                    i.ruta_imagen
             FROM especies e
             LEFT JOIN imagenes_especies i
                    ON i.id_especie = e.id_especie
                   AND i.es_principal = 1
             WHERE e.activa = 1
             ORDER BY e.nombre_comun"
        );
        return $stmt->fetchAll();
    }

    /**
     * Obtener especies destacadas para la portada
     */
    public function obtenerDestacadas(int $limite = 6): array {
    $stmt = $this->db->prepare(
        "SELECT e.id_especie, e.nombre_comun, e.nombre_cientifico, 
                e.estado_conservacion, e.dificultad_identificacion,
                i.ruta_imagen, i.creditos
         FROM especies e
         LEFT JOIN imagenes_especies i 
                ON i.id_especie = e.id_especie 
               AND i.es_principal = 1
         WHERE e.activa = 1 
         ORDER BY RAND() 
         LIMIT ?"
    );
    $stmt->execute([$limite]);
    return $stmt->fetchAll();
}

    /**
     * Buscar especies por nombre
     */
    public function buscar(string $termino): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM especies 
             WHERE activa = 1 
               AND (nombre_comun LIKE ? OR nombre_cientifico LIKE ?)
             ORDER BY nombre_comun"
        );
        $like = '%' . $termino . '%';
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    /**
     * Búsqueda optimizada para la API (devuelve solo los campos necesarios)
     */
    public function buscarParaApi(string $termino): array {
        $stmt = $this->db->prepare(
            "SELECT e.id_especie, e.nombre_comun, e.nombre_cientifico,
                    e.estado_conservacion, e.dificultad_identificacion,
                    e.familia,
                    i.ruta_imagen
             FROM especies e
             LEFT JOIN imagenes_especies i
                    ON i.id_especie = e.id_especie
                   AND i.es_principal = 1
             WHERE e.activa = 1
               AND (e.nombre_comun      LIKE :like1
                OR  e.nombre_cientifico LIKE :like2
                OR  e.nombre_ingles     LIKE :like3
                OR  e.familia           LIKE :like4)
             ORDER BY
                CASE WHEN e.nombre_comun LIKE :starts THEN 0 ELSE 1 END,
                e.nombre_comun
             LIMIT 10"
        );
        $like   = '%' . $termino . '%';
        $starts = $termino . '%';
        $stmt->execute([
            ':like1'  => $like,
            ':like2'  => $like,
            ':like3'  => $like,
            ':like4'  => $like,
            ':starts' => $starts,
        ]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener especie con todos sus datos relacionados
     */
    public function obtenerCompleta(int $id): array|false {
        $especie = $this->obtenerPorId($id);
        if (!$especie) return false;

        // Imágenes
        $stmt = $this->db->prepare(
            "SELECT * FROM imagenes_especies WHERE id_especie = ? ORDER BY orden_visualizacion"
        );
        $stmt->execute([$id]);
        $especie['imagenes'] = $stmt->fetchAll();

        // Audios
        $stmt = $this->db->prepare(
            "SELECT * FROM audios_especies WHERE id_especie = ?"
        );
        $stmt->execute([$id]);
        $especie['audios'] = $stmt->fetchAll();

        return $especie;
    }
    /**
     * Obtener todas las especies con su autor
     */
    public function obtenerTodosConAutor(): array {
        $stmt = $this->db->query(
            "SELECT e.*, u.nombre as nombre_autor
            FROM especies e
            LEFT JOIN usuarios u ON u.id_usuario = e.id_autor
            ORDER BY e.nombre_comun"
        );
        return $stmt->fetchAll();
    }

    /**
     * Contar especies creadas por un autor
     */
    public function contarPorAutor(int $idAutor): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM especies WHERE id_autor = ?"
        );
        $stmt->execute([$idAutor]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Crear nueva especie
     */
    public function crear(array $datos, int $idAutor): int|false {
        $stmt = $this->db->prepare(
            "INSERT INTO especies (
                nombre_cientifico, nombre_comun, nombre_ingles, familia, orden,
                descripcion, caracteristicas_fisicas,
                envergadura_min, envergadura_max, peso_min, peso_max,
                longitud_min, longitud_max, dimorfismo_sexual,
                habitat, distribucion_geografica, altitud_min, altitud_max,
                dieta, comportamiento_caza, reproduccion,
                epoca_cria, numero_huevos, estado_conservacion,
                poblacion_iberica, amenazas, medidas_conservacion,
                curiosidades, dificultad_identificacion, id_autor, activa
            ) VALUES (
                :nombre_cientifico, :nombre_comun, :nombre_ingles, :familia, :orden,
                :descripcion, :caracteristicas_fisicas,
                :envergadura_min, :envergadura_max, :peso_min, :peso_max,
                :longitud_min, :longitud_max, :dimorfismo_sexual,
                :habitat, :distribucion_geografica, :altitud_min, :altitud_max,
                :dieta, :comportamiento_caza, :reproduccion,
                :epoca_cria, :numero_huevos, :estado_conservacion,
                :poblacion_iberica, :amenazas, :medidas_conservacion,
                :curiosidades, :dificultad_identificacion, :id_autor, 1
            )"
        );

        $ok = $stmt->execute([
            ':nombre_cientifico'      => trim($datos['nombre_cientifico']),
            ':nombre_comun'           => trim($datos['nombre_comun']),
            ':nombre_ingles'          => trim($datos['nombre_ingles'] ?? ''),
            ':familia'                => trim($datos['familia'] ?? ''),
            ':orden'                  => trim($datos['orden'] ?? 'Accipitriformes'),
            ':descripcion'            => trim($datos['descripcion']),
            ':caracteristicas_fisicas' => trim($datos['caracteristicas_fisicas'] ?? ''),
            ':envergadura_min'        => $datos['envergadura_min'] ?: null,
            ':envergadura_max'        => $datos['envergadura_max'] ?: null,
            ':peso_min'               => $datos['peso_min'] ?: null,
            ':peso_max'               => $datos['peso_max'] ?: null,
            ':longitud_min'           => $datos['longitud_min'] ?: null,
            ':longitud_max'           => $datos['longitud_max'] ?: null,
            ':dimorfismo_sexual'      => trim($datos['dimorfismo_sexual'] ?? ''),
            ':habitat'                => trim($datos['habitat'] ?? ''),
            ':distribucion_geografica' => trim($datos['distribucion_geografica'] ?? ''),
            ':altitud_min'            => $datos['altitud_min'] ?: null,
            ':altitud_max'            => $datos['altitud_max'] ?: null,
            ':dieta'                  => trim($datos['dieta'] ?? ''),
            ':comportamiento_caza'    => trim($datos['comportamiento_caza'] ?? ''),
            ':reproduccion'           => trim($datos['reproduccion'] ?? ''),
            ':epoca_cria'             => trim($datos['epoca_cria'] ?? ''),
            ':numero_huevos'          => trim($datos['numero_huevos'] ?? ''),
            ':estado_conservacion'    => $datos['estado_conservacion'] ?? 'LC',
            ':poblacion_iberica'      => trim($datos['poblacion_iberica'] ?? ''),
            ':amenazas'               => trim($datos['amenazas'] ?? ''),
            ':medidas_conservacion'   => trim($datos['medidas_conservacion'] ?? ''),
            ':curiosidades'           => trim($datos['curiosidades'] ?? ''),
            ':dificultad_identificacion' => $datos['dificultad_identificacion'] ?? 'medio',
            ':id_autor'               => $idAutor,
        ]);

        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    /**
     * Actualizar especie existente
     */
    public function actualizar(int $id, array $datos): bool {
        $stmt = $this->db->prepare(
            "UPDATE especies SET
                nombre_cientifico       = :nombre_cientifico,
                nombre_comun            = :nombre_comun,
                nombre_ingles           = :nombre_ingles,
                familia                 = :familia,
                orden                   = :orden,
                descripcion             = :descripcion,
                caracteristicas_fisicas = :caracteristicas_fisicas,
                envergadura_min         = :envergadura_min,
                envergadura_max         = :envergadura_max,
                peso_min                = :peso_min,
                peso_max                = :peso_max,
                longitud_min            = :longitud_min,
                longitud_max            = :longitud_max,
                dimorfismo_sexual       = :dimorfismo_sexual,
                habitat                 = :habitat,
                distribucion_geografica = :distribucion_geografica,
                altitud_min             = :altitud_min,
                altitud_max             = :altitud_max,
                dieta                   = :dieta,
                comportamiento_caza     = :comportamiento_caza,
                reproduccion            = :reproduccion,
                epoca_cria              = :epoca_cria,
                numero_huevos           = :numero_huevos,
                estado_conservacion     = :estado_conservacion,
                poblacion_iberica       = :poblacion_iberica,
                amenazas                = :amenazas,
                medidas_conservacion    = :medidas_conservacion,
                curiosidades            = :curiosidades,
                dificultad_identificacion = :dificultad_identificacion
            WHERE id_especie = :id"
        );

        return $stmt->execute([
            ':nombre_cientifico'       => trim($datos['nombre_cientifico']),
            ':nombre_comun'            => trim($datos['nombre_comun']),
            ':nombre_ingles'           => trim($datos['nombre_ingles'] ?? ''),
            ':familia'                 => trim($datos['familia'] ?? ''),
            ':orden'                   => trim($datos['orden'] ?? 'Accipitriformes'),
            ':descripcion'             => trim($datos['descripcion']),
            ':caracteristicas_fisicas' => trim($datos['caracteristicas_fisicas'] ?? ''),
            ':envergadura_min'         => $datos['envergadura_min'] ?: null,
            ':envergadura_max'         => $datos['envergadura_max'] ?: null,
            ':peso_min'                => $datos['peso_min'] ?: null,
            ':peso_max'                => $datos['peso_max'] ?: null,
            ':longitud_min'            => $datos['longitud_min'] ?: null,
            ':longitud_max'            => $datos['longitud_max'] ?: null,
            ':dimorfismo_sexual'       => trim($datos['dimorfismo_sexual'] ?? ''),
            ':habitat'                 => trim($datos['habitat'] ?? ''),
            ':distribucion_geografica' => trim($datos['distribucion_geografica'] ?? ''),
            ':altitud_min'             => $datos['altitud_min'] ?: null,
            ':altitud_max'             => $datos['altitud_max'] ?: null,
            ':dieta'                   => trim($datos['dieta'] ?? ''),
            ':comportamiento_caza'     => trim($datos['comportamiento_caza'] ?? ''),
            ':reproduccion'            => trim($datos['reproduccion'] ?? ''),
            ':epoca_cria'              => trim($datos['epoca_cria'] ?? ''),
            ':numero_huevos'           => trim($datos['numero_huevos'] ?? ''),
            ':estado_conservacion'     => $datos['estado_conservacion'] ?? 'LC',
            ':poblacion_iberica'       => trim($datos['poblacion_iberica'] ?? ''),
            ':amenazas'                => trim($datos['amenazas'] ?? ''),
            ':medidas_conservacion'    => trim($datos['medidas_conservacion'] ?? ''),
            ':curiosidades'            => trim($datos['curiosidades'] ?? ''),
            ':dificultad_identificacion' => $datos['dificultad_identificacion'] ?? 'medio',
            ':id'                      => $id,
        ]);
    }

    /**
     * Obtener imágenes de una especie
     */
    public function obtenerImagenes(int $idEspecie): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM imagenes_especies 
            WHERE id_especie = ? 
            ORDER BY es_principal DESC, orden_visualizacion ASC"
        );
        $stmt->execute([$idEspecie]);
        return $stmt->fetchAll();
    }

    /**
     * Añadir imagen a una especie
     */
    public function añadirImagen(array $datos): void {
        $stmt = $this->db->prepare(
            "INSERT INTO imagenes_especies 
                (id_especie, ruta_imagen, descripcion, tipo, es_principal, creditos)
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $datos['id_especie'],
            $datos['ruta_imagen'],
            $datos['descripcion'],
            $datos['tipo'],
            $datos['es_principal'] ? 1 : 0,
            $datos['creditos'],
        ]);
    }

    /**
     * Quitar imagen principal de una especie
     */
    public function quitarPrincipal(int $idEspecie): void {
        $stmt = $this->db->prepare(
            "UPDATE imagenes_especies SET es_principal = 0 WHERE id_especie = ?"
        );
        $stmt->execute([$idEspecie]);
    }
}