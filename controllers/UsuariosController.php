<?php
require_once ROOT_PATH . '/models/UsuarioModel.php';
require_once ROOT_PATH . '/core/EmailService.php';

class UsuariosController extends Controller {

    private UsuarioModel $modelo;
    private EmailService $email;

    public function __construct() {
        $this->modelo = new UsuarioModel();
        $this->email  = new EmailService();
    }

    // ============================================================
    // REGISTRO
    // ============================================================

    public function registro(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errores = $this->validarRegistro($_POST);

            if (empty($errores)) {
                $token = bin2hex(random_bytes(32));

                $id = $this->modelo->registrar([
                    'email'              => trim($_POST['email']),
                    'password'           => $_POST['password'],
                    'nombre'             => trim($_POST['nombre']),
                    'apellidos'          => trim($_POST['apellidos'] ?? ''),
                    'fecha_nacimiento'   => $_POST['fecha_nacimiento'] ?? null,
                    'tipo_usuario'       => $_POST['tipo_usuario'],
                    'token_verificacion' => $token,
                ]);

                if ($id) {
                    $this->email->enviarVerificacion(
                        trim($_POST['email']),
                        trim($_POST['nombre']),
                        $token
                    );
                    $this->cargarVista('usuarios/registro_ok', [
                        'titulo' => 'Registro completado',
                        'email'  => trim($_POST['email']),
                    ]);
                    return;
                }

                $errores[] = 'Error al crear la cuenta. Inténtalo de nuevo.';
            }

            $this->cargarVista('usuarios/registro', [
                'titulo'  => 'Crear cuenta',
                'errores' => $errores,
                'datos'   => $_POST,
            ]);
            return;
        }

        $this->cargarVista('usuarios/registro', [
            'titulo'  => 'Crear cuenta',
            'errores' => [],
            'datos'   => [],
        ]);
    }

    // ============================================================
    // VERIFICACIÓN DE EMAIL
    // ============================================================

    public function verificar(string $token): void {
        $ok = $this->modelo->verificarEmail($token);
        $this->cargarVista('usuarios/verificacion', [
            'titulo'    => 'Verificación de cuenta',
            'verificado' => $ok,
        ]);
    }

    // ============================================================
    // LOGIN
    // ============================================================

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $this->modelo->buscarPorEmail(trim($_POST['email']));

            if (!$usuario || !password_verify($_POST['password'], $usuario['password_hash'])) {
                $this->cargarVista('usuarios/login', [
                    'titulo' => 'Iniciar sesión',
                    'error'  => 'Email o contraseña incorrectos.',
                    'email'  => trim($_POST['email']),
                ]);
                return;
            }

            if (!$usuario['email_verificado']) {
                $this->cargarVista('usuarios/login', [
                    'titulo' => 'Iniciar sesión',
                    'error'  => 'Debes verificar tu email antes de entrar.',
                    'email'  => trim($_POST['email']),
                ]);
                return;
            }

            // Iniciar sesión
            $_SESSION['usuario_id']    = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['tipo_usuario']  = $usuario['tipo_usuario'];

            $this->modelo->actualizarUltimoAcceso($usuario['id_usuario']);
            $this->redirigir('');
        }

        $this->cargarVista('usuarios/login', [
            'titulo' => 'Iniciar sesión',
            'error'  => '',
            'email'  => '',
        ]);
    }

    // ============================================================
    // LOGOUT
    // ============================================================

    public function logout(): void {
        session_destroy();
        $this->redirigir('');
    }

    // ============================================================
    // RECUPERACIÓN DE CONTRASEÑA
    // ============================================================

    public function recuperar(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $this->modelo->buscarPorEmail(trim($_POST['email']));

            // Siempre mostramos el mismo mensaje por seguridad
            // (no revelar si el email existe o no)
            if ($usuario) {
                $token = bin2hex(random_bytes(32));
                $this->modelo->guardarTokenRecuperacion($usuario['id_usuario'], $token);
                $this->email->enviarRecuperacion(
                    $usuario['email'],
                    $usuario['nombre'],
                    $token
                );
            }

            $this->cargarVista('usuarios/recuperar_ok', [
                'titulo' => 'Recuperar contraseña',
            ]);
            return;
        }

        $this->cargarVista('usuarios/recuperar', [
            'titulo' => 'Recuperar contraseña',
            'error'  => '',
        ]);
    }

    public function resetPassword(string $token): void {
        $usuario = $this->modelo->buscarPorTokenRecuperacion($token);

        if (!$usuario) {
            $this->cargarVista('usuarios/reset_invalido', [
                'titulo' => 'Enlace no válido',
            ]);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_POST['password'] !== $_POST['password_confirm']) {
                $this->cargarVista('usuarios/reset_password', [
                    'titulo' => 'Nueva contraseña',
                    'token'  => $token,
                    'error'  => 'Las contraseñas no coinciden.',
                ]);
                return;
            }

            $this->modelo->actualizarPassword($usuario['id_usuario'], $_POST['password']);
            $this->cargarVista('usuarios/reset_ok', [
                'titulo' => 'Contraseña actualizada',
            ]);
            return;
        }

        $this->cargarVista('usuarios/reset_password', [
            'titulo' => 'Nueva contraseña',
            'token'  => $token,
            'error'  => '',
        ]);
    }

    // ============================================================
    // VALIDACIÓN DEL FORMULARIO DE REGISTRO
    // ============================================================

    private function validarRegistro(array $datos): array {
        $errores = [];

        if (empty($datos['nombre'])) {
            $errores[] = 'El nombre es obligatorio.';
        }

        if (empty($datos['email']) || !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email no es válido.';
        } elseif ($this->modelo->buscarPorEmail(trim($datos['email']))) {
            $errores[] = 'Este email ya está registrado.';
        }

        if (empty($datos['password']) || strlen($datos['password']) < 8) {
            $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
        }

        if ($datos['password'] !== $datos['password_confirm']) {
            $errores[] = 'Las contraseñas no coinciden.';
        }

        if (!in_array($datos['tipo_usuario'], ['estudiante', 'educador'])) {
            $errores[] = 'El tipo de usuario no es válido.';
        }

        return $errores;
    }
    // ============================================================
    // PERFIL
    // ============================================================

    public function perfil(): void {
        $this->requiereLogin();

        $id      = $_SESSION['usuario_id'];
        $usuario = $this->modelo->obtenerPerfil($id);
        $insignias = $this->modelo->obtenerInsignias($id);
        $historial = $this->modelo->obtenerHistorial($id);

        // Si no tiene progreso inicializado, crearlo
        if (empty($usuario['puntos_totales'])) {
            $this->modelo->inicializarProgreso($id);
            $usuario = $this->modelo->obtenerPerfil($id);
        }

        $this->cargarVista('usuarios/perfil', [
            'titulo'    => 'Mi perfil',
            'usuario'   => $usuario,
            'insignias' => $insignias,
            'historial' => $historial,
        ]);
    }

    // ============================================================
    // EDITAR PERFIL
    // ============================================================

    public function editarPerfil(): void {
        $this->requiereLogin();

        $id      = $_SESSION['usuario_id'];
        $usuario = $this->modelo->obtenerPorId($id);
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Subida de avatar
            $avatar = $usuario['avatar'];
            if (!empty($_FILES['avatar']['name'])) {
                $ext      = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
                $permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($ext, $permitidos)) {
                    $errores[] = 'Formato de imagen no permitido.';
                } elseif ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
                    $errores[] = 'La imagen no puede superar 2MB.';
                } else {
                    $nombreArchivo = 'avatar_' . $id . '.' . $ext;
                    $destino = ROOT_PATH . '/public/img/avatares/' . $nombreArchivo;
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destino)) {
                        $avatar = $nombreArchivo;
                    } else {
                        $errores[] = 'Error al subir la imagen.';
                    }
                }
            }

            if (empty($_POST['nombre'])) {
                $errores[] = 'El nombre es obligatorio.';
            }

            if (empty($errores)) {
                $this->modelo->actualizarPerfil($id, [
                    'nombre'          => trim($_POST['nombre']),
                    'apellidos'       => trim($_POST['apellidos'] ?? ''),
                    'fecha_nacimiento' => $_POST['fecha_nacimiento'] ?? null,
                    'avatar'          => $avatar,
                ]);

                // Actualizar nombre en sesión
                $_SESSION['usuario_nombre'] = trim($_POST['nombre']);

                $this->redirigir('usuarios/perfil');
                return;
            }
        }

        $this->cargarVista('usuarios/editar_perfil', [
            'titulo'  => 'Editar perfil',
            'usuario' => $usuario,
            'errores' => $errores,
        ]);
    }

    // ============================================================
    // CAMBIAR CONTRASEÑA
    // ============================================================

    public function cambiarPassword(): void {
        $this->requiereLogin();

        $id     = $_SESSION['usuario_id'];
        $errores = [];
        $ok     = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (strlen($_POST['password_nueva']) < 8) {
                $errores[] = 'La nueva contraseña debe tener al menos 8 caracteres.';
            } elseif ($_POST['password_nueva'] !== $_POST['password_confirm']) {
                $errores[] = 'Las contraseñas nuevas no coinciden.';
            } else {
                $resultado = $this->modelo->cambiarPassword(
                    $id,
                    $_POST['password_actual'],
                    $_POST['password_nueva']
                );

                if ($resultado === true) {
                    $ok = true;
                } else {
                    $errores[] = $resultado;
                }
            }
        }

        $this->cargarVista('usuarios/cambiar_password', [
            'titulo'  => 'Cambiar contraseña',
            'errores' => $errores,
            'ok'      => $ok,
        ]);
    }
}