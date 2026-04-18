<?php
require_once ROOT_PATH . '/models/EspecieModel.php';
require_once ROOT_PATH . '/models/JuegoModel.php';

class EducadoresController extends Controller {

    private EspecieModel $modeloEspecies;
    private JuegoModel   $modeloJuegos;

    public function __construct() {
        $this->modeloEspecies = new EspecieModel();
        $this->modeloJuegos   = new JuegoModel();
    }

    // Verificar que es educador o admin
    private function requiereEducador(): void {
        $this->requiereLogin();
        if (!in_array($_SESSION['tipo_usuario'], ['educador', 'admin'])) {
            $this->error403();
        }
    }

    // ============================================================
    // PANEL PRINCIPAL
    // ============================================================

    public function index(): void {
        $this->requiereEducador();

        $idUsuario     = $_SESSION['usuario_id'];
        $misEspecies   = $this->modeloEspecies->contarPorAutor($idUsuario);
        $misCuestionarios = $this->modeloJuegos->contarPorAutor($idUsuario);
        $totalEspecies = $this->modeloEspecies->contar();

        $this->cargarVista('educadores/index', [
            'titulo'          => 'Área de Educadores',
            'misEspecies'     => $misEspecies,
            'misCuestionarios' => $misCuestionarios,
            'totalEspecies'   => $totalEspecies,
        ]);
    }

    // ============================================================
    // GESTIÓN DE ESPECIES
    // ============================================================

    public function especies(): void {
        $this->requiereEducador();
        $especies = $this->modeloEspecies->obtenerTodosConAutor();
        $this->cargarVista('educadores/especies', [
            'titulo'   => 'Gestión de Especies',
            'especies' => $especies,
        ]);
    }

    public function crearEspecie(): void {
        $this->requiereEducador();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errores = $this->validarEspecie($_POST);
            if (empty($errores)) {
                $id = $this->modeloEspecies->crear($_POST, $_SESSION['usuario_id']);
                if ($id) {
                    $this->redirigir('educadores/imagenes/' . $id);
                    return;
                }
                $errores[] = 'Error al crear la especie.';
            }
            $this->cargarVista('educadores/especie_form', [
                'titulo'  => 'Nueva especie',
                'especie' => $_POST,
                'errores' => $errores,
                'editar'  => false,
            ]);
            return;
        }

        $this->cargarVista('educadores/especie_form', [
            'titulo'  => 'Nueva especie',
            'especie' => [],
            'errores' => [],
            'editar'  => false,
        ]);
    }

    public function editarEspecie(string $id): void {
        $this->requiereEducador();
        $especie = $this->modeloEspecies->obtenerPorId((int) $id);
        if (!$especie) { $this->error404(); return; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errores = $this->validarEspecie($_POST);
            if (empty($errores)) {
                $this->modeloEspecies->actualizar((int) $id, $_POST);
                $this->redirigir('educadores/especies');
                return;
            }
            $this->cargarVista('educadores/especie_form', [
                'titulo'  => 'Editar especie',
                'especie' => array_merge($especie, $_POST),
                'errores' => $errores,
                'editar'  => true,
            ]);
            return;
        }

        $this->cargarVista('educadores/especie_form', [
            'titulo'  => 'Editar especie',
            'especie' => $especie,
            'errores' => [],
            'editar'  => true,
        ]);
    }

    public function imagenes(string $id): void {
        $this->requiereEducador();
        $especie  = $this->modeloEspecies->obtenerPorId((int) $id);
        if (!$especie) { $this->error404(); return; }

        $imagenes = $this->modeloEspecies->obtenerImagenes((int) $id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errores = [];

            if (empty($_FILES['imagen']['name'])) {
                $errores[] = 'Debes seleccionar una imagen.';
            } else {
                $ext        = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                $permitidos = ['jpg', 'jpeg', 'png', 'webp'];

                if (!in_array($ext, $permitidos)) {
                    $errores[] = 'Formato no permitido. Usa JPG, PNG o WEBP.';
                } elseif ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
                    $errores[] = 'La imagen no puede superar 5MB.';
                } else {
                    $nombreArchivo = strtolower(str_replace(' ', '_', $especie['nombre_cientifico']))
                                   . '_' . time() . '.' . $ext;
                    $destino = ROOT_PATH . '/public/img/especies/' . $nombreArchivo;

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                        // Si es principal, quitar principal a las demás
                        $esPrincipal = !empty($_POST['es_principal']);
                        if ($esPrincipal) {
                            $this->modeloEspecies->quitarPrincipal((int) $id);
                        }

                        $this->modeloEspecies->añadirImagen([
                            'id_especie'   => (int) $id,
                            'ruta_imagen'  => $nombreArchivo,
                            'descripcion'  => trim($_POST['descripcion'] ?? ''),
                            'tipo'         => $_POST['tipo'] ?? 'foto',
                            'es_principal' => $esPrincipal,
                            'creditos'     => trim($_POST['creditos'] ?? ''),
                        ]);

                        $this->redirigir('educadores/imagenes/' . $id);
                        return;
                    } else {
                        $errores[] = 'Error al subir la imagen.';
                    }
                }
            }

            $this->cargarVista('educadores/imagenes', [
                'titulo'   => 'Imágenes de ' . $especie['nombre_comun'],
                'especie'  => $especie,
                'imagenes' => $imagenes,
                'errores'  => $errores,
            ]);
            return;
        }

        $this->cargarVista('educadores/imagenes', [
            'titulo'   => 'Imágenes de ' . $especie['nombre_comun'],
            'especie'  => $especie,
            'imagenes' => $imagenes,
            'errores'  => [],
        ]);
    }

    // ============================================================
    // GESTIÓN DE CUESTIONARIOS
    // ============================================================

    public function cuestionarios(): void {
        $this->requiereEducador();
        $cuestionarios = $this->modeloJuegos->obtenerCuestionariosConAutor($_SESSION['usuario_id']);
        $this->cargarVista('educadores/cuestionarios', [
            'titulo'        => 'Mis cuestionarios',
            'cuestionarios' => $cuestionarios,
        ]);
    }

    public function crearCuestionario(): void {
        $this->requiereEducador();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errores = $this->validarCuestionario($_POST);
            if (empty($errores)) {
                $id = $this->modeloJuegos->crearCuestionario($_POST, $_SESSION['usuario_id']);
                if ($id) {
                    $this->redirigir('educadores/editarPreguntas/' . $id);
                    return;
                }
                $errores[] = 'Error al crear el cuestionario.';
            }
            $this->cargarVista('educadores/cuestionario_form', [
                'titulo'       => 'Nuevo cuestionario',
                'cuestionario' => $_POST,
                'errores'      => $errores,
                'editar'       => false,
            ]);
            return;
        }

        $this->cargarVista('educadores/cuestionario_form', [
            'titulo'       => 'Nuevo cuestionario',
            'cuestionario' => [],
            'errores'      => [],
            'editar'       => false,
        ]);
    }

    public function editarCuestionario(string $id): void {
        $this->requiereEducador();
        $cuestionario = $this->modeloJuegos->obtenerCuestionarioPorId((int) $id);
        if (!$cuestionario) { $this->error404(); return; }

        // Solo puede editar el autor
        if ($cuestionario['id_autor'] != $_SESSION['usuario_id'] 
            && $_SESSION['tipo_usuario'] !== 'admin') {
            $this->error403();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errores = $this->validarCuestionario($_POST);
            if (empty($errores)) {
                $this->modeloJuegos->actualizarCuestionario((int) $id, $_POST);
                $this->redirigir('educadores/cuestionarios');
                return;
            }
            $this->cargarVista('educadores/cuestionario_form', [
                'titulo'       => 'Editar cuestionario',
                'cuestionario' => array_merge($cuestionario, $_POST),
                'errores'      => $errores,
                'editar'       => true,
            ]);
            return;
        }

        $this->cargarVista('educadores/cuestionario_form', [
            'titulo'       => 'Editar cuestionario',
            'cuestionario' => $cuestionario,
            'errores'      => [],
            'editar'       => true,
        ]);
    }

    public function editarPreguntas(string $id): void {
        $this->requiereEducador();
        $cuestionario = $this->modeloJuegos->obtenerCuestionario((int) $id);
        if (!$cuestionario) { $this->error404(); return; }

        if ($cuestionario['id_autor'] != $_SESSION['usuario_id']
            && $_SESSION['tipo_usuario'] !== 'admin') {
            $this->error403();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->modeloJuegos->guardarPreguntas((int) $id, $_POST);
            $this->redirigir('educadores/cuestionarios');
            return;
        }

        $this->cargarVista('educadores/preguntas_form', [
            'titulo'      => 'Preguntas: ' . $cuestionario['titulo'],
            'cuestionario' => $cuestionario,
        ]);
    }

    // ============================================================
    // VALIDACIONES
    // ============================================================

    private function validarEspecie(array $datos): array {
        $errores = [];
        if (empty($datos['nombre_comun']))     $errores[] = 'El nombre común es obligatorio.';
        if (empty($datos['nombre_cientifico'])) $errores[] = 'El nombre científico es obligatorio.';
        if (empty($datos['descripcion']))      $errores[] = 'La descripción es obligatoria.';
        return $errores;
    }

    private function validarCuestionario(array $datos): array {
        $errores = [];
        if (empty($datos['titulo']))           $errores[] = 'El título es obligatorio.';
        if (empty($datos['numero_preguntas'])) $errores[] = 'El número de preguntas es obligatorio.';
        return $errores;
    }
}