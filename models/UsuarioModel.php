<?php
class UsuarioModel extends Model {

    protected string $tabla = 'usuarios';

    /**
     * Registrar nuevo usuario
     */
    public function registrar(array $datos): int|false {
        $stmt = $this->db->prepare(
            "INSERT INTO usuarios 
                (email, password_hash, nombre, apellidos, fecha_nacimiento, 
                 tipo_usuario, token_verificacion, activo, email_verificado)
             VALUES 
                (:email, :password_hash, :nombre, :apellidos, :fecha_nacimiento,
                 :tipo_usuario, :token_verificacion, 1, 0)"
        );

        $ok = $stmt->execute([
            ':email'               => $datos['email'],
            ':password_hash'       => password_hash($datos['password'], PASSWORD_BCRYPT),
            ':nombre'              => $datos['nombre'],
            ':apellidos'           => $datos['apellidos'] ?? null,
            ':fecha_nacimiento'    => $datos['fecha_nacimiento'] ?? null,
            ':tipo_usuario'        => $datos['tipo_usuario'],
            ':token_verificacion'  => $datos['token_verificacion'],
        ]);

        return $ok ? (int) $this->db->lastInsertId() : false;
    }

    /**
     * Buscar usuario por email
     */
    public function buscarPorEmail(string $email): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Verificar cuenta con token
     */
    public function verificarEmail(string $token): bool {
        $stmt = $this->db->prepare(
            "UPDATE usuarios 
             SET email_verificado = 1, token_verificacion = NULL
             WHERE token_verificacion = ?"
        );
        $stmt->execute([$token]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Guardar token de recuperación de contraseña
     */
    public function guardarTokenRecuperacion(int $id, string $token): bool {
        $stmt = $this->db->prepare(
            "UPDATE usuarios 
             SET token_recuperacion = ?,
                 token_recuperacion_expira = DATE_ADD(NOW(), INTERVAL 1 HOUR)
             WHERE id_usuario = ?"
        );
        return $stmt->execute([$token, $id]);
    }

    /**
     * Buscar usuario por token de recuperación válido
     */
    public function buscarPorTokenRecuperacion(string $token): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM usuarios 
             WHERE token_recuperacion = ? 
               AND token_recuperacion_expira > NOW()
             LIMIT 1"
        );
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    /**
     * Actualizar contraseña
     */
    public function actualizarPassword(int $id, string $nuevaPassword): bool {
        $stmt = $this->db->prepare(
            "UPDATE usuarios 
             SET password_hash = ?,
                 token_recuperacion = NULL,
                 token_recuperacion_expira = NULL
             WHERE id_usuario = ?"
        );
        return $stmt->execute([
            password_hash($nuevaPassword, PASSWORD_BCRYPT),
            $id
        ]);
    }

    /**
     * Actualizar último acceso
     */
    public function actualizarUltimoAcceso(int $id): void {
        $stmt = $this->db->prepare(
            "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id_usuario = ?"
        );
        $stmt->execute([$id]);
    }
    /**
     * Obtener perfil completo con estadísticas
     */
    public function obtenerPerfil(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT u.*, 
                    n.nombre_nivel, n.numero_nivel,
                    p.puntos_totales, p.especies_descubiertas, 
                    p.racha_dias, p.puntos_nivel_actual,
                    n2.puntos_necesarios as puntos_siguiente_nivel
            FROM usuarios u
            LEFT JOIN progreso_usuarios p ON p.id_usuario = u.id_usuario
            LEFT JOIN niveles n ON n.id_nivel = p.id_nivel_actual
            LEFT JOIN niveles n2 ON n2.numero_nivel = n.numero_nivel + 1
            WHERE u.id_usuario = ?
            LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Obtener insignias del usuario
     */
    public function obtenerInsignias(int $id): array {
        $stmt = $this->db->prepare(
            "SELECT i.*, iu.fecha_obtencion
            FROM insignias_usuarios iu
            JOIN insignias i ON i.id_insignia = iu.id_insignia
            WHERE iu.id_usuario = ?
            ORDER BY iu.fecha_obtencion DESC"
        );
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener historial de actividad reciente
     */
    public function obtenerHistorial(int $id, int $limite = 10): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM historial_actividad
            WHERE id_usuario = ?
            ORDER BY fecha_actividad DESC
            LIMIT ?"
        );
        $stmt->execute([$id, $limite]);
        return $stmt->fetchAll();
    }

    /**
     * Actualizar datos personales
     */
    public function actualizarPerfil(int $id, array $datos): bool {
        $stmt = $this->db->prepare(
            "UPDATE usuarios 
            SET nombre = ?, apellidos = ?, fecha_nacimiento = ?, avatar = ?
            WHERE id_usuario = ?"
        );
        return $stmt->execute([
            $datos['nombre'],
            $datos['apellidos'] ?? null,
            $datos['fecha_nacimiento'] ?? null,
            $datos['avatar'] ?? null,
            $id,
        ]);
    }

    /**
     * Cambiar contraseña verificando la actual
     */
    public function cambiarPassword(int $id, string $passwordActual, string $passwordNueva): bool|string {
        $usuario = $this->obtenerPorId($id);
        if (!$usuario) return 'Usuario no encontrado.';

        if (!password_verify($passwordActual, $usuario['password_hash'])) {
            return 'La contraseña actual no es correcta.';
        }

        $stmt = $this->db->prepare(
            "UPDATE usuarios SET password_hash = ? WHERE id_usuario = ?"
        );
        $stmt->execute([password_hash($passwordNueva, PASSWORD_BCRYPT), $id]);
        return true;
    }

    /**
     * Inicializar progreso para usuario nuevo
     */
    public function inicializarProgreso(int $id): void {
        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO progreso_usuarios 
                (id_usuario, id_nivel_actual, puntos_totales)
            VALUES (?, 1, 0)"
        );
        $stmt->execute([$id]);
    }
}