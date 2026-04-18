<?php
require_once ROOT_PATH . '/models/JuegoModel.php';

class JuegosController extends Controller {

    private JuegoModel $modelo;

    public function __construct() {
        $this->modelo = new JuegoModel();
    }

    /**
     * Lista de juegos disponibles
     */
    public function index(): void {
        $juegos = $this->modelo->obtenerActivos();
        $this->cargarVista('juegos/index', [
            'titulo'  => 'Mini-juegos',
            'juegos'  => $juegos,
        ]);
    }

    /**
     * Lista de cuestionarios disponibles
     */
    public function cuestionarios(): void {
        $cuestionarios = $this->modelo->obtenerCuestionariosPorJuego(1);
        $ranking       = $this->modelo->obtenerRanking(1);
        $this->cargarVista('juegos/cuestionarios', [
            'titulo'        => 'Cuestionarios',
            'cuestionarios' => $cuestionarios,
            'ranking'       => $ranking,
        ]);
    }

    /**
     * Jugar un cuestionario
     */
    public function jugar(string $id): void {
        $this->requiereLogin();

        $cuestionario = $this->modelo->obtenerCuestionario((int) $id);
        if (!$cuestionario) {
            $this->error404();
            return;
        }

        // Iniciar partida
        $idPartida = $this->modelo->iniciarPartida(
            $_SESSION['usuario_id'],
            $cuestionario['id_juego']
        );

        // Guardar en sesión para verificar al enviar
        $_SESSION['partida_activa'] = [
            'id_partida'      => $idPartida,
            'id_cuestionario' => (int) $id,
            'inicio'          => time(),
        ];

        $this->cargarVista('juegos/cuestionario', [
            'titulo'      => $cuestionario['titulo'],
            'cuestionario' => $cuestionario,
            'idPartida'   => $idPartida,
        ]);
    }

    /**
     * Procesar respuestas del cuestionario
     */
    public function resultado(): void {
        $this->requiereLogin();

        // Verificar que hay una partida activa
        if (empty($_SESSION['partida_activa'])) {
            $this->redirigir('juegos/cuestionarios');
            return;
        }

        $partidaActiva   = $_SESSION['partida_activa'];
        $idPartida       = $partidaActiva['id_partida'];
        $idCuestionario  = $partidaActiva['id_cuestionario'];
        $tiempoEmpleado  = time() - $partidaActiva['inicio'];

        unset($_SESSION['partida_activa']);

        $cuestionario = $this->modelo->obtenerCuestionario($idCuestionario);
        if (!$cuestionario) {
            $this->redirigir('juegos');
            return;
        }

        // Procesar respuestas
        $puntuacionTotal = 0;
        $correctas       = 0;

        foreach ($cuestionario['preguntas'] as $pregunta) {
            $idPregunta  = $pregunta['id_pregunta'];
            $idRespuesta = (int) ($_POST['respuesta_' . $idPregunta] ?? 0);

            // Verificar si es correcta
            $esCorrecta = false;
            foreach ($pregunta['respuestas'] as $respuesta) {
                if ($respuesta['id_respuesta'] === $idRespuesta && $respuesta['es_correcta']) {
                    $esCorrecta = true;
                    break;
                }
            }

            if ($esCorrecta) {
                $puntuacionTotal += $pregunta['puntos'];
                $correctas++;
            }

            // Guardar respuesta
            if ($idRespuesta > 0) {
                $this->modelo->guardarRespuesta(
                    $idPartida,
                    $idPregunta,
                    $idRespuesta,
                    $esCorrecta,
                    $tiempoEmpleado
                );
            }
        }

        // Finalizar partida
        $this->modelo->finalizarPartida($idPartida, $puntuacionTotal, $tiempoEmpleado);

        // Gamificación: añadir puntos
        $gamificacion  = new GamificacionService();
        $resultado     = $gamificacion->añadirPuntos(
            $_SESSION['usuario_id'],
            $puntuacionTotal,
            'juego_jugado',
            "Cuestionario completado: {$cuestionario['titulo']} ({$correctas}/{$cuestionario['numero_preguntas']} correctas)"
        );

        if (!empty($resultado['subio_nivel'])) {
            $_SESSION['nivel_nuevo'] = $resultado['nivel_nuevo']['nombre_nivel'];
        }

        // Obtener resultados detallados
        $resultados = $this->modelo->obtenerResultados($idPartida);

        $porcentaje = round(($correctas / $cuestionario['numero_preguntas']) * 100);
        $aprobado   = $porcentaje >= ($cuestionario['puntuacion_minima_aprobar'] ?? 60);

        $this->cargarVista('juegos/resultado', [
            'titulo'        => 'Resultado del cuestionario',
            'cuestionario'  => $cuestionario,
            'resultados'    => $resultados,
            'puntuacion'    => $puntuacionTotal,
            'correctas'     => $correctas,
            'porcentaje'    => $porcentaje,
            'aprobado'      => $aprobado,
            'tiempoEmpleado' => $tiempoEmpleado,
        ]);
    }
}