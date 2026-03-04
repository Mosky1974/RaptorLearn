<?php
/**
 * Router simple para RaptorLearn
 * 
 * Formato de URL: /controlador/accion/parametro
 * Ejemplo: /especies/detalle/3  →  EspeciesController::detalle(3)
 */
class Router {

    public function despachar(): void {
        // Obtener la URI limpia
        $uri = $_SERVER['REQUEST_URI'];
        $base = parse_url(BASE_URL, PHP_URL_PATH);
        $uri = substr($uri, strlen($base));
        $uri = strtok($uri, '?');   // quitar query string
        $uri = trim($uri, '/');

        // Dividir en segmentos
        $segmentos = $uri ? explode('/', $uri) : [];

        $controladorNombre = $segmentos[0] ?? 'inicio';
        $accion            = $segmentos[1] ?? 'index';
        $parametro         = $segmentos[2] ?? null;

        // Construir nombre de clase del controlador
        $clase = ucfirst($controladorNombre) . 'Controller';
        $archivo = ROOT_PATH . '/controllers/' . $clase . '.php';

        if (!file_exists($archivo)) {
            $this->error404();
            return;
        }

        require_once $archivo;

        if (!class_exists($clase)) {
            $this->error404();
            return;
        }

        $controlador = new $clase();

        if (!method_exists($controlador, $accion)) {
            $this->error404();
            return;
        }

        // Llamar al método con o sin parámetro
        $parametro !== null
            ? $controlador->$accion($parametro)
            : $controlador->$accion();
    }

    private function error404(): void {
        http_response_code(404);
        require_once VIEWS_PATH . '/errors/404.php';
        exit;
    }
}