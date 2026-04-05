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
        $especie = $this->modelo->obtenerCompleta((int) $id);
        if (!$especie) {
            $this->error404();
            return;
        }

        // Gamificación: registrar descubrimiento si está logueado
        $resultadoGamificacion = null;
        if (!empty($_SESSION['usuario_id'])) {
            $gamificacion = new GamificacionService();
            $resultadoGamificacion = $gamificacion->descubrirEspecie(
                $_SESSION['usuario_id'],
                (int) $id,
                $especie['nombre_comun']
            );

            if (!empty($resultadoGamificacion['subio_nivel'])) {
                $_SESSION['nivel_nuevo'] = $resultadoGamificacion['nivel_nuevo']['nombre_nivel'];
            }

            // Pasar a la vista si es nueva para mostrar mensaje
            if (!empty($resultadoGamificacion['nueva'])) {
                $_SESSION['especie_nueva'] = $especie['nombre_comun'];
            }
        }

        $this->cargarVista('especies/detalle', [
            'titulo'               => $especie['nombre_comun'] . ' - RaptorLearn',
            'especie'              => $especie,
            'resultadoGamificacion' => $resultadoGamificacion,
        ]);
    }
}