<?php
require_once ROOT_PATH . '/models/EspecieModel.php';

class EspeciesController extends Controller {

    private EspecieModel $modelo;

    public function __construct() {
        $this->modelo = new EspecieModel();
    }

    public function index(): void {
        $especies = $this->modelo->obtenerTodos();
        $this->cargarVista('especies/index', [
            'titulo'   => 'Enciclopedia de Rapaces',
            'especies' => $especies,
        ]);
    }

    public function detalle(string $id): void {
        $especie = $this->modelo->obtenerPorId((int) $id);
        if (!$especie) {
            $this->error404();
            return;
        }
        $this->cargarVista('especies/detalle', [
            'titulo'  => $especie['nombre_comun'] . ' - RaptorLearn',
            'especie' => $especie,
        ]);
    }
}