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
        // Datos del cuestionario
        $stmt = $this->db->prepare(
            "SELECT c.*, j.nombre as nombre_juego, j.puntos_base
             FROM cuestionarios c
             JOIN juegos j ON j.id_juego = c.id_juego
             WHERE c.id_cuestionario = ?"
        );
        $stmt->execute([$idCuestionario]);
        $cuestionario = $stmt->fetch();
        if (!$cuestionario) return false;

        // Preguntas en orden aleatorio
        $stmt = $this->db->prepare(
            "SELECT * FROM preguntas 
             WHERE id_cuestionario = ? 
             ORDER BY orden_presentacion"
        );
        $stmt->execute([$idCuestionario]);
        $preguntas = $stmt->fetchAll();

        // Respuestas de cada pregunta en orden aleatorio
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
}