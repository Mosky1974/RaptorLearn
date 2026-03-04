<?php
require_once ROOT_PATH . '/models/EspecieModel.php';

class InicioController extends Controller {

    public function index(): void {
        $modelo = new EspecieModel();
        $especies = $modelo->obtenerDestacadas(6);

        $this->cargarVista('inicio/index', [
            'titulo'   => 'Bienvenido a RaptorLearn',
            'especies' => $especies,
        ]);
    }
}