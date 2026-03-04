<?php
/**
 * Clase base Controller
 * Todos los controladores extienden de esta clase.
 */
abstract class Controller {

    /**
     * Cargar una vista con datos opcionales
     * 
     * @param string $vista  Ruta relativa dentro de views/ (ej: 'especies/detalle')
     * @param array  $datos  Variables que estarán disponibles en la vista
     */
    protected function cargarVista(string $vista, array $datos = []): void {
        // Extraer el array para que las claves sean variables en la vista
        extract($datos);

        $ruta = VIEWS_PATH . '/' . $vista . '.php';

        if (!file_exists($ruta)) {
            $this->error404();
            return;
        }

        // Cargar layout principal envolviendo la vista
        $contenido = $ruta;
        require_once VIEWS_PATH . '/layouts/main.php';
    }

    /**
     * Redirigir a otra URL
     */
    protected function redirigir(string $url): void {
        header('Location: ' . BASE_URL . '/' . ltrim($url, '/'));
        exit;
    }

    /**
     * Verificar si el usuario está logueado
     */
    protected function requiereLogin(): void {
        if (empty($_SESSION['usuario_id'])) {
            $this->redirigir('usuarios/login');
        }
    }

    /**
     * Verificar si el usuario es administrador
     */
    protected function requiereAdmin(): void {
        $this->requiereLogin();
        if ($_SESSION['tipo_usuario'] !== 'admin') {
            $this->error403();
        }
    }

    /**
     * Mostrar página de error 404
     */
    protected function error404(): void {
        http_response_code(404);
        require_once VIEWS_PATH . '/errors/404.php';
        exit;
    }

    /**
     * Mostrar página de error 403
     */
    protected function error403(): void {
        http_response_code(403);
        require_once VIEWS_PATH . '/errors/403.php';
        exit;
    }

    /**
     * Devolver respuesta JSON (para peticiones AJAX)
     */
    protected function json(array $datos, int $codigo = 200): void {
        http_response_code($codigo);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        exit;
    }
}