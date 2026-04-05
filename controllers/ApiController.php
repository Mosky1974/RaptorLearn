<?php
require_once ROOT_PATH . '/models/EspecieModel.php';

/**
 * ApiController
 * Endpoints REST que devuelven JSON.
 * URL: /api/especies?q=termino
 */
class ApiController extends Controller {

    private EspecieModel $especieModel;

    public function __construct() {
        $this->especieModel = new EspecieModel();
    }

    // GET /api/especies?q=termino
    public function especies(): void {
        // Limpiar cualquier output previo y forzar cabecera JSON
        while (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        $termino = trim($_GET['q'] ?? '');

        if (strlen($termino) < 2) {
            echo json_encode(['resultados' => [], 'total' => 0, 'termino' => $termino], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $especies = $this->especieModel->buscarParaApi($termino);

        echo json_encode([
            'resultados' => $especies,
            'total'      => count($especies),
            'termino'    => $termino,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}