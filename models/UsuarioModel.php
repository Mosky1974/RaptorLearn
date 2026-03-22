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
}