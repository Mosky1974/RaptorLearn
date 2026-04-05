<?php
/**
 * Servicio central de gamificación
 * Gestiona puntos, niveles, insignias e historial
 */
class GamificacionService {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // ============================================================
    // PUNTOS Y NIVELES
    // ============================================================

    /**
     * Añadir puntos a un usuario y comprobar subida de nivel
     * Devuelve array con info de la transacción (incluye si subió de nivel)
     */
    public function añadirPuntos(int $idUsuario, int $puntos, string $tipo, string $descripcion): array {
        $resultado = [
            'puntos_añadidos' => $puntos,
            'subio_nivel'     => false,
            'nivel_nuevo'     => null,
        ];

        // Obtener progreso actual
        $progreso = $this->obtenerProgreso($idUsuario);
        if (!$progreso) {
            $this->inicializarProgreso($idUsuario);
            $progreso = $this->obtenerProgreso($idUsuario);
        }

        $puntosTotalesNuevos  = $progreso['puntos_totales'] + $puntos;
        $puntosNivelNuevos    = $progreso['puntos_nivel_actual'] + $puntos;
        $nivelActual          = $progreso['numero_nivel'];

        // Comprobar si sube de nivel
        $nivelSiguiente = $this->obtenerNivel($nivelActual + 1);
        if ($nivelSiguiente && $puntosNivelNuevos >= $nivelSiguiente['puntos_necesarios']) {
            $puntosNivelNuevos       = $puntosNivelNuevos - $nivelSiguiente['puntos_necesarios'];
            $resultado['subio_nivel'] = true;
            $resultado['nivel_nuevo'] = $nivelSiguiente;
            $idNivelNuevo            = $nivelSiguiente['id_nivel'];
        } else {
            $idNivelNuevo = $progreso['id_nivel_actual'];
        }

        // Actualizar progreso
        $stmt = $this->db->prepare(
            "UPDATE progreso_usuarios 
             SET puntos_totales      = ?,
                 puntos_nivel_actual = ?,
                 id_nivel_actual     = ?,
                 fecha_ultima_actividad = NOW()
             WHERE id_usuario = ?"
        );
        $stmt->execute([$puntosTotalesNuevos, $puntosNivelNuevos, $idNivelNuevo, $idUsuario]);

        // Registrar en historial
        $this->registrarActividad($idUsuario, $tipo, $descripcion, $puntos);

        return $resultado;
    }

    /**
     * Registrar login diario y racha
     */
    public function registrarLoginDiario(int $idUsuario): array {
        $progreso = $this->obtenerProgreso($idUsuario);
        if (!$progreso) {
            $this->inicializarProgreso($idUsuario);
            $progreso = $this->obtenerProgreso($idUsuario);
        }

        $hoy          = date('Y-m-d');
        $ultimaFecha  = $progreso['ultima_fecha_racha'];
        $racha        = $progreso['racha_dias'];

        // Si ya hizo login hoy, no hacer nada
        if ($ultimaFecha === $hoy) {
            return ['puntos_añadidos' => 0, 'racha' => $racha, 'subio_nivel' => false];
        }

        // Si fue ayer, incrementar racha; si no, reiniciar
        $ayer = date('Y-m-d', strtotime('-1 day'));
        $racha = ($ultimaFecha === $ayer) ? $racha + 1 : 1;

        // Actualizar racha
        $stmt = $this->db->prepare(
            "UPDATE progreso_usuarios 
             SET racha_dias = ?, ultima_fecha_racha = ?
             WHERE id_usuario = ?"
        );
        $stmt->execute([$racha, $hoy, $idUsuario]);

        // Dar puntos por login diario
        $puntosLogin = (int) $this->obtenerConfig('puntos_por_login_diario');
        $resultado   = $this->añadirPuntos(
            $idUsuario, 
            $puntosLogin, 
            'login', 
            "Login diario (racha: {$racha} días)"
        );
        $resultado['racha'] = $racha;

        return $resultado;
    }

    /**
     * Registrar descubrimiento de especie
     */
    public function descubrirEspecie(int $idUsuario, int $idEspecie, string $nombreEspecie): array {
        // Comprobar si ya la había descubierto
        $stmt = $this->db->prepare(
            "SELECT id_descubrimiento FROM especies_descubiertas 
             WHERE id_usuario = ? AND id_especie = ?"
        );
        $stmt->execute([$idUsuario, $idEspecie]);

        if ($stmt->fetch()) {
            return ['puntos_añadidos' => 0, 'nueva' => false, 'subio_nivel' => false];
        }

        // Registrar descubrimiento
        $puntos = (int) $this->obtenerConfig('puntos_por_especie_descubierta');
        $stmt   = $this->db->prepare(
            "INSERT INTO especies_descubiertas (id_usuario, id_especie, puntos_obtenidos)
             VALUES (?, ?, ?)"
        );
        $stmt->execute([$idUsuario, $idEspecie, $puntos]);

        // Actualizar contador en progreso
        $stmt = $this->db->prepare(
            "UPDATE progreso_usuarios 
             SET especies_descubiertas = especies_descubiertas + 1
             WHERE id_usuario = ?"
        );
        $stmt->execute([$idUsuario]);

        // Añadir puntos
        $resultado          = $this->añadirPuntos(
            $idUsuario,
            $puntos,
            'especie_vista',
            "Nueva especie descubierta: {$nombreEspecie}"
        );
        $resultado['nueva'] = true;

        // Comprobar insignias relacionadas
        $this->comprobarInsignias($idUsuario);

        return $resultado;
    }

    // ============================================================
    // INSIGNIAS
    // ============================================================

    /**
     * Comprobar y otorgar insignias automáticamente
     */
    public function comprobarInsignias(int $idUsuario): array {
        $progreso      = $this->obtenerProgreso($idUsuario);
        $nuevasInsignias = [];

        // Insignia: primera especie descubierta
        if ($progreso['especies_descubiertas'] >= 1) {
            $insignia = $this->otorgarInsignia($idUsuario, 1); // id_insignia=1
            if ($insignia) $nuevasInsignias[] = $insignia;
        }

        // Insignia: 5 especies descubiertas
        if ($progreso['especies_descubiertas'] >= 5) {
            $insignia = $this->otorgarInsignia($idUsuario, 2);
            if ($insignia) $nuevasInsignias[] = $insignia;
        }

        // Insignia: 10 especies descubiertas
        if ($progreso['especies_descubiertas'] >= 10) {
            $insignia = $this->otorgarInsignia($idUsuario, 3);
            if ($insignia) $nuevasInsignias[] = $insignia;
        }

        return $nuevasInsignias;
    }

    /**
     * Otorgar una insignia si no la tiene ya
     */
    public function otorgarInsignia(int $idUsuario, int $idInsignia): array|false {
        // Comprobar si ya la tiene
        $stmt = $this->db->prepare(
            "SELECT id_insignia_usuario FROM insignias_usuarios 
             WHERE id_usuario = ? AND id_insignia = ?"
        );
        $stmt->execute([$idUsuario, $idInsignia]);
        if ($stmt->fetch()) return false;

        // Otorgar insignia
        $stmt = $this->db->prepare(
            "INSERT INTO insignias_usuarios (id_usuario, id_insignia) VALUES (?, ?)"
        );
        $stmt->execute([$idUsuario, $idInsignia]);

        // Registrar en historial
        $stmt = $this->db->prepare("SELECT nombre FROM insignias WHERE id_insignia = ?");
        $stmt->execute([$idInsignia]);
        $insignia = $stmt->fetch();

        if ($insignia) {
            $this->registrarActividad(
                $idUsuario,
                'insignia_obtenida',
                "Insignia obtenida: {$insignia['nombre']}",
                0
            );
        }

        return $insignia ?: false;
    }

    // ============================================================
    // HELPERS
    // ============================================================

    public function obtenerProgreso(int $idUsuario): array|false {
        $stmt = $this->db->prepare(
            "SELECT p.*, n.numero_nivel, n.nombre_nivel, n.puntos_necesarios
             FROM progreso_usuarios p
             JOIN niveles n ON n.id_nivel = p.id_nivel_actual
             WHERE p.id_usuario = ?"
        );
        $stmt->execute([$idUsuario]);
        return $stmt->fetch();
    }

    public function inicializarProgreso(int $idUsuario): void {
        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO progreso_usuarios 
                (id_usuario, id_nivel_actual, puntos_totales)
             VALUES (?, 1, 0)"
        );
        $stmt->execute([$idUsuario]);
    }

    private function obtenerNivel(int $numero): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM niveles WHERE numero_nivel = ?"
        );
        $stmt->execute([$numero]);
        return $stmt->fetch();
    }

    private function obtenerConfig(string $clave): string {
        $stmt = $this->db->prepare(
            "SELECT valor FROM configuracion_sistema WHERE clave = ?"
        );
        $stmt->execute([$clave]);
        $fila = $stmt->fetch();
        return $fila ? $fila['valor'] : '0';
    }

    public function registrarActividad(int $idUsuario, string $tipo, string $descripcion, int $puntos): void {
        $stmt = $this->db->prepare(
            "INSERT INTO historial_actividad 
                (id_usuario, tipo_actividad, descripcion, puntos_obtenidos)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$idUsuario, $tipo, $descripcion, $puntos]);
    }
}