<?php
class JuegoModel extends Model {

    protected string $tabla = 'juegos';

    /**
     * Obtener todos los juegos activos
     */
    public function obtenerActivos(): array {
        $stmt = $this->db->query(
            "SELECT * FROM juegos WHERE activo = 1 ORDER BY tipo_juego, nombre"
        );
        return $stmt->fetchAll();
    }

    /**
     * Obtener cuestionario con sus preguntas y respuestas
     */
    public function obtenerCuestionario(int $idCuestionario): array|false {
        $stmt = $this->db->prepare(
            "SELECT c.*, j.nombre as nombre_juego, j.puntos_base
             FROM cuestionarios c
             JOIN juegos j ON j.id_juego = c.id_juego
             WHERE c.id_cuestionario = ?"
        );
        $stmt->execute([$idCuestionario]);
        $cuestionario = $stmt->fetch();
        if (!$cuestionario) return false;

        $stmt = $this->db->prepare(
            "SELECT * FROM preguntas 
             WHERE id_cuestionario = ? 
             ORDER BY orden_presentacion"
        );
        $stmt->execute([$idCuestionario]);
        $preguntas = $stmt->fetchAll();

        foreach ($preguntas as &$pregunta) {
            $stmt = $this->db->prepare(
                "SELECT * FROM respuestas 
                 WHERE id_pregunta = ? 
                 ORDER BY RAND()"
            );
            $stmt->execute([$pregunta['id_pregunta']]);
            $pregunta['respuestas'] = $stmt->fetchAll();
        }

        $cuestionario['preguntas'] = $preguntas;
        return $cuestionario;
    }

    /**
     * Obtener todos los cuestionarios de un juego
     */
    public function obtenerCuestionariosPorJuego(int $idJuego): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM cuestionarios WHERE id_juego = ? ORDER BY titulo"
        );
        $stmt->execute([$idJuego]);
        return $stmt->fetchAll();
    }

    /**
     * Iniciar una partida
     */
    public function iniciarPartida(int $idUsuario, int $idJuego): int {
        $stmt = $this->db->prepare(
            "INSERT INTO partidas (id_usuario, id_juego, fecha_inicio)
             VALUES (?, ?, NOW())"
        );
        $stmt->execute([$idUsuario, $idJuego]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Guardar respuesta de usuario
     */
    public function guardarRespuesta(int $idPartida, int $idPregunta, int $idRespuesta, bool $esCorrecta, int $tiempoRespuesta): void {
        $stmt = $this->db->prepare(
            "INSERT INTO respuestas_usuarios 
                (id_partida, id_pregunta, id_respuesta_seleccionada, es_correcta, tiempo_respuesta)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$idPartida, $idPregunta, $idRespuesta, $esCorrecta, $tiempoRespuesta]);
    }

    /**
     * Finalizar partida
     */
    public function finalizarPartida(int $idPartida, int $puntuacion, int $tiempoEmpleado): void {
        $stmt = $this->db->prepare(
            "UPDATE partidas 
             SET puntuacion = ?, tiempo_empleado = ?, completada = 1, fecha_fin = NOW()
             WHERE id_partida = ?"
        );
        $stmt->execute([$puntuacion, $tiempoEmpleado, $idPartida]);
    }

    /**
     * Obtener resultados de una partida
     */
    public function obtenerResultados(int $idPartida): array|false {
        $stmt = $this->db->prepare(
            "SELECT ru.*, 
                    p.enunciado, p.explicacion, p.puntos as puntos_pregunta,
                    r.texto_respuesta as respuesta_seleccionada,
                    rc.texto_respuesta as respuesta_correcta
             FROM respuestas_usuarios ru
             JOIN preguntas p ON p.id_pregunta = ru.id_pregunta
             LEFT JOIN respuestas r ON r.id_respuesta = ru.id_respuesta_seleccionada
             LEFT JOIN respuestas rc ON rc.id_pregunta = p.id_pregunta AND rc.es_correcta = 1
             WHERE ru.id_partida = ?
             ORDER BY ru.id_respuesta_usuario"
        );
        $stmt->execute([$idPartida]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener mejores puntuaciones de un juego
     */
    public function obtenerRanking(int $idJuego, int $limite = 10): array {
        $stmt = $this->db->prepare(
            "SELECT p.puntuacion, p.tiempo_empleado, p.fecha_fin,
                    u.nombre, u.apellidos, u.avatar
             FROM partidas p
             JOIN usuarios u ON u.id_usuario = p.id_usuario
             WHERE p.id_juego = ? AND p.completada = 1
             ORDER BY p.puntuacion DESC, p.tiempo_empleado ASC
             LIMIT ?"
        );
        $stmt->execute([$idJuego, $limite]);
        return $stmt->fetchAll();
    }

    /**
     * Contar cuestionarios creados por un autor
     */
    public function contarPorAutor(int $idAutor): int {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM cuestionarios WHERE id_autor = ?"
        );
        $stmt->execute([$idAutor]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Obtener cuestionarios con autor
     */
    public function obtenerCuestionariosConAutor(int $idUsuario): array {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.nombre as nombre_autor,
                    (SELECT COUNT(*) FROM preguntas p WHERE p.id_cuestionario = c.id_cuestionario) as total_preguntas
             FROM cuestionarios c
             LEFT JOIN usuarios u ON u.id_usuario = c.id_autor
             ORDER BY c.id_cuestionario DESC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtener cuestionario por ID (sin preguntas)
     */
    public function obtenerCuestionarioPorId(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM cuestionarios WHERE id_cuestionario = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Crear nuevo cuestionario
     */
    public function crearCuestionario(array $datos, int $idAutor): int|false {
        $stmt = $this->db->prepare(
            "INSERT INTO cuestionarios 
                (id_juego, titulo, descripcion, categoria, numero_preguntas, 
                 tiempo_total, puntuacion_minima_aprobar, id_autor)
             VALUES (1, :titulo, :descripcion, :categoria, :numero_preguntas,
                     :tiempo_total, :puntuacion_minima_aprobar, :id_autor)"
        );

        $ok = $stmt->execute([
            ':titulo'                    => trim($datos['titulo']),
            ':descripcion'               => trim($datos['descripcion'] ?? ''),
            ':categoria'                 => trim($datos['categoria'] ?? 'general'),
            ':numero_preguntas'          => (int) $datos['numero_preguntas'],
            ':tiempo_total'              => (int) ($datos['tiempo_total'] ?? 300),
            ':puntuacion_minima_aprobar' => (int) ($datos['puntuacion_minima_aprobar'] ?? 60),
            ':id_autor'                  => $idAutor,
        ]);

        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    /**
     * Actualizar cuestionario
     */
    public function actualizarCuestionario(int $id, array $datos): bool {
        $stmt = $this->db->prepare(
            "UPDATE cuestionarios SET
                titulo                    = :titulo,
                descripcion               = :descripcion,
                categoria                 = :categoria,
                numero_preguntas          = :numero_preguntas,
                tiempo_total              = :tiempo_total,
                puntuacion_minima_aprobar = :puntuacion_minima_aprobar
             WHERE id_cuestionario = :id"
        );

        return $stmt->execute([
            ':titulo'                    => trim($datos['titulo']),
            ':descripcion'               => trim($datos['descripcion'] ?? ''),
            ':categoria'                 => trim($datos['categoria'] ?? 'general'),
            ':numero_preguntas'          => (int) $datos['numero_preguntas'],
            ':tiempo_total'              => (int) ($datos['tiempo_total'] ?? 300),
            ':puntuacion_minima_aprobar' => (int) ($datos['puntuacion_minima_aprobar'] ?? 60),
            ':id'                        => $id,
        ]);
    }

    /**
     * Guardar preguntas y respuestas de un cuestionario
     */
    public function guardarPreguntas(int $idCuestionario, array $datos): void {
        // Eliminar preguntas y respuestas anteriores
        $stmt = $this->db->prepare(
            "DELETE r FROM respuestas r
             JOIN preguntas p ON p.id_pregunta = r.id_pregunta
             WHERE p.id_cuestionario = ?"
        );
        $stmt->execute([$idCuestionario]);

        $stmt = $this->db->prepare(
            "DELETE FROM preguntas WHERE id_cuestionario = ?"
        );
        $stmt->execute([$idCuestionario]);

        // Insertar nuevas preguntas
        $totalPreguntas = (int) ($datos['total_preguntas'] ?? 0);

        for ($i = 0; $i < $totalPreguntas; $i++) {
            if (empty($datos['enunciado'][$i])) continue;

            $stmtPregunta = $this->db->prepare(
                "INSERT INTO preguntas 
                    (id_cuestionario, enunciado, tipo_pregunta, puntos, explicacion, orden_presentacion)
                 VALUES (?, ?, 'multiple', 10, ?, ?)"
            );
            $stmtPregunta->execute([
                $idCuestionario,
                trim($datos['enunciado'][$i]),
                trim($datos['explicacion'][$i] ?? ''),
                $i + 1,
            ]);

            $idPregunta = (int) $this->db->lastInsertId();

            // Insertar respuestas
            $correcta = (int) ($datos['correcta'][$i] ?? 0);
            for ($j = 0; $j < 4; $j++) {
                if (empty($datos['respuesta'][$i][$j])) continue;
                $stmtResp = $this->db->prepare(
                    "INSERT INTO respuestas (id_pregunta, texto_respuesta, es_correcta, orden_presentacion)
                     VALUES (?, ?, ?, ?)"
                );
                $stmtResp->execute([
                    $idPregunta,
                    trim($datos['respuesta'][$i][$j]),
                    ($j === $correcta) ? 1 : 0,
                    $j + 1,
                ]);
            }
        }
    }
    /**
     * Eliminar cuestionario (solo el autor o admin)
     */
    public function eliminarCuestionario(int $id): bool {
        // Las preguntas y respuestas se eliminan en cascada por FK
        $stmt = $this->db->prepare(
            "DELETE FROM cuestionarios WHERE id_cuestionario = ?"
        );
        return $stmt->execute([$id]);
    }
}