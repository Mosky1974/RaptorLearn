<?php
/**
 * RAPTORLEARN - Script de instalación de estructura MVC
 * 
 * INSTRUCCIONES:
 * 1. Copia este archivo en: htdocs/raptorlearn/setup.php
 * 2. Abre en el navegador: http://localhost/raptorlearn/setup.php
 * 3. Una vez ejecutado, ELIMINA este archivo por seguridad.
 */

$base = __DIR__;

// ============================================================
// ESTRUCTURA DE CARPETAS
// ============================================================
$carpetas = [
    'config',
    'core',
    'controllers',
    'models',
    'views',
    'views/layouts',
    'views/especies',
    'views/usuarios',
    'views/juegos',
    'views/educadores',
    'views/errors',
    'public',
    'public/css',
    'public/js',
    'public/img',
    'public/img/especies',
    'public/img/avatares',
    'public/audio',
];

// ============================================================
// ARCHIVOS BASE
// ============================================================
$archivos = [];

// --- config/config.php ---
$archivos['config/config.php'] = <<<'PHP'
<?php
// ============================================================
// CONFIGURACIÓN GLOBAL DE RAPTORLEARN
// ============================================================

// Entorno: 'development' o 'production'
define('ENVIRONMENT', 'development');

// URL base de la aplicación
define('BASE_URL', 'http://localhost/raptorlearn');

// Rutas del sistema
define('ROOT_PATH',   __DIR__ . '/..');
define('VIEWS_PATH',  ROOT_PATH . '/views');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Nombre de la aplicación
define('APP_NAME', 'RaptorLearn');

// Zona horaria
date_default_timezone_set('Europe/Madrid');

// Mostrar errores solo en desarrollo
if (ENVIRONMENT === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
PHP;

// --- config/database.php ---
$archivos['config/database.php'] = <<<'PHP'
<?php
// ============================================================
// CONFIGURACIÓN DE BASE DE DATOS
// ============================================================

define('DB_HOST',    'localhost');
define('DB_NAME',    'raptorlearn');
define('DB_USER',    'root');
define('DB_PASS',    '');
define('DB_CHARSET', 'utf8mb4');
PHP;

// --- core/Database.php ---
$archivos['core/Database.php'] = <<<'PHP'
<?php
/**
 * Clase Database - Singleton para conexión PDO
 */
class Database {

    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            $dsn = 'mysql:host=' . DB_HOST
                 . ';dbname=' . DB_NAME
                 . ';charset=' . DB_CHARSET;

            $opciones = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $opciones);
            } catch (PDOException $e) {
                // En producción nunca mostrar el mensaje real
                error_log('Error de conexión: ' . $e->getMessage());
                die('Error de conexión a la base de datos.');
            }
        }
        return self::$instance;
    }
}
PHP;

// --- core/Model.php ---
$archivos['core/Model.php'] = <<<'PHP'
<?php
/**
 * Clase base Model
 * Todos los modelos extienden de esta clase.
 */
abstract class Model {

    protected PDO $db;
    protected string $tabla = '';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Obtener todos los registros de la tabla
     */
    public function obtenerTodos(): array {
        $stmt = $this->db->query("SELECT * FROM {$this->tabla}");
        return $stmt->fetchAll();
    }

    /**
     * Obtener un registro por ID
     */
    public function obtenerPorId(int $id): array|false {
        $pk = $this->obtenerClavePrimaria();
        $stmt = $this->db->prepare("SELECT * FROM {$this->tabla} WHERE {$pk} = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Eliminar un registro por ID
     */
    public function eliminar(int $id): bool {
        $pk = $this->obtenerClavePrimaria();
        $stmt = $this->db->prepare("DELETE FROM {$this->tabla} WHERE {$pk} = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Contar registros totales
     */
    public function contar(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->tabla}");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Devuelve el nombre de la clave primaria (convención: id_nombretabla)
     * Puede sobreescribirse en cada modelo si es necesario.
     */
    protected function obtenerClavePrimaria(): string {
        // Convención: tabla 'especies' → PK 'id_especie' (singular)
        $singular = rtrim($this->tabla, 's');
        return 'id_' . $singular;
    }
}
PHP;

// --- core/Controller.php ---
$archivos['core/Controller.php'] = <<<'PHP'
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
PHP;

// --- core/Router.php ---
$archivos['core/Router.php'] = <<<'PHP'
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
PHP;

// --- index.php ---
$archivos['index.php'] = <<<'PHP'
<?php
/**
 * RAPTORLEARN - Punto de entrada único
 */

// Cargar configuración
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

// Cargar clases del núcleo
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Router.php';

// Despachar la petición
$router = new Router();
$router->despachar();
PHP;

// --- .htaccess ---
$archivos['.htaccess'] = <<<'HTACCESS'
Options -Indexes

RewriteEngine On
RewriteBase /raptorlearn/

# No reescribir archivos o carpetas que existen realmente
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Redirigir todo lo demás a index.php
RewriteRule ^(.*)$ index.php [QSA,L]
HTACCESS;

// --- Controlador de inicio ---
$archivos['controllers/InicioController.php'] = <<<'PHP'
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
PHP;

// --- Controlador de especies ---
$archivos['controllers/EspeciesController.php'] = <<<'PHP'
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
PHP;

// --- Modelo de especie ---
$archivos['models/EspecieModel.php'] = <<<'PHP'
<?php
class EspecieModel extends Model {

    protected string $tabla = 'especies';

    /**
     * Obtener especies destacadas para la portada
     */
    public function obtenerDestacadas(int $limite = 6): array {
        $stmt = $this->db->prepare(
            "SELECT id_especie, nombre_comun, nombre_cientifico, 
                    imagen_principal, estado_conservacion, dificultad_identificacion
             FROM especies 
             WHERE activa = 1 
             ORDER BY RAND() 
             LIMIT ?"
        );
        $stmt->execute([$limite]);
        return $stmt->fetchAll();
    }

    /**
     * Buscar especies por nombre
     */
    public function buscar(string $termino): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM especies 
             WHERE activa = 1 
               AND (nombre_comun LIKE ? OR nombre_cientifico LIKE ?)
             ORDER BY nombre_comun"
        );
        $like = '%' . $termino . '%';
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener especie con todos sus datos relacionados
     */
    public function obtenerCompleta(int $id): array|false {
        $especie = $this->obtenerPorId($id);
        if (!$especie) return false;

        // Imágenes
        $stmt = $this->db->prepare(
            "SELECT * FROM imagenes_especies WHERE id_especie = ? ORDER BY orden_visualizacion"
        );
        $stmt->execute([$id]);
        $especie['imagenes'] = $stmt->fetchAll();

        // Audios
        $stmt = $this->db->prepare(
            "SELECT * FROM audios_especies WHERE id_especie = ?"
        );
        $stmt->execute([$id]);
        $especie['audios'] = $stmt->fetchAll();

        return $especie;
    }
}
PHP;

// --- Layout principal ---
$archivos['views/layouts/main.php'] = <<<'PHP'
<?php
// $contenido y $titulo vienen del controlador vía extract()
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/style.css">
</head>
<body>

    <header>
        <nav>
            <a href="<?= BASE_URL ?>"><strong>🦅 RaptorLearn</strong></a>
            <ul>
                <li><a href="<?= BASE_URL ?>/especies">Enciclopedia</a></li>
                <li><a href="<?= BASE_URL ?>/juegos">Mini-juegos</a></li>
                <li><a href="<?= BASE_URL ?>/educadores">Educadores</a></li>
                <?php if (!empty($_SESSION['usuario_id'])): ?>
                    <li><a href="<?= BASE_URL ?>/usuarios/perfil">Mi perfil</a></li>
                    <li><a href="<?= BASE_URL ?>/usuarios/logout">Salir</a></li>
                <?php else: ?>
                    <li><a href="<?= BASE_URL ?>/usuarios/login">Entrar</a></li>
                    <li><a href="<?= BASE_URL ?>/usuarios/registro">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <?php require_once $contenido; ?>
    </main>

    <footer>
        <p>&copy; <?= date('Y') ?> RaptorLearn - Portal Educativo sobre Rapaces Ibéricas</p>
    </footer>

    <script src="<?= BASE_URL ?>/public/js/main.js"></script>
</body>
</html>
PHP;

// --- Vista inicio ---
$archivos['views/inicio/index.php'] = <<<'PHP'
<section class="portada">
    <h1>Descubre las Rapaces Ibéricas</h1>
    <p>Aprende, juega y conviértete en un experto ornitólogo.</p>
    <a href="<?= BASE_URL ?>/especies" class="btn-principal">Ver enciclopedia</a>
</section>

<section class="especies-destacadas">
    <h2>Especies destacadas</h2>
    <div class="grid-especies">
        <?php foreach ($especies as $especie): ?>
            <a href="<?= BASE_URL ?>/especies/detalle/<?= $especie['id_especie'] ?>" class="tarjeta-especie">
                <img src="<?= BASE_URL ?>/public/img/especies/<?= htmlspecialchars($especie['imagen_principal'] ?? 'default.jpg') ?>" 
                     alt="<?= htmlspecialchars($especie['nombre_comun']) ?>">
                <h3><?= htmlspecialchars($especie['nombre_comun']) ?></h3>
                <em><?= htmlspecialchars($especie['nombre_cientifico']) ?></em>
            </a>
        <?php endforeach; ?>
    </div>
</section>
PHP;

// --- Vista especies/index ---
$archivos['views/especies/index.php'] = <<<'PHP'
<h1>Enciclopedia de Rapaces Ibéricas</h1>

<div class="grid-especies">
    <?php foreach ($especies as $especie): ?>
        <a href="<?= BASE_URL ?>/especies/detalle/<?= $especie['id_especie'] ?>" class="tarjeta-especie">
            <h3><?= htmlspecialchars($especie['nombre_comun']) ?></h3>
            <em><?= htmlspecialchars($especie['nombre_cientifico']) ?></em>
            <span class="conservacion estado-<?= strtolower($especie['estado_conservacion']) ?>">
                <?= htmlspecialchars($especie['estado_conservacion']) ?>
            </span>
        </a>
    <?php endforeach; ?>
</div>
PHP;

// --- Vista especies/detalle ---
$archivos['views/especies/detalle.php'] = <<<'PHP'
<article class="detalle-especie">
    <h1><?= htmlspecialchars($especie['nombre_comun']) ?></h1>
    <h2><em><?= htmlspecialchars($especie['nombre_cientifico']) ?></em></h2>

    <p><?= htmlspecialchars($especie['descripcion']) ?></p>

    <section>
        <h3>Características físicas</h3>
        <p><?= htmlspecialchars($especie['caracteristicas_fisicas']) ?></p>
        <ul>
            <li>Envergadura: <?= $especie['envergadura_min'] ?> – <?= $especie['envergadura_max'] ?> m</li>
            <li>Peso: <?= $especie['peso_min'] ?> – <?= $especie['peso_max'] ?> g</li>
        </ul>
    </section>

    <section>
        <h3>Hábitat</h3>
        <p><?= htmlspecialchars($especie['habitat']) ?></p>
    </section>

    <section>
        <h3>Dieta</h3>
        <p><?= htmlspecialchars($especie['dieta']) ?></p>
    </section>

    <section>
        <h3>Estado de conservación</h3>
        <span class="conservacion estado-<?= strtolower($especie['estado_conservacion']) ?>">
            <?= htmlspecialchars($especie['estado_conservacion']) ?>
        </span>
    </section>
</article>
PHP;

// --- Vistas de error ---
$archivos['views/errors/404.php'] = <<<'PHP'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>404 - Página no encontrada | RaptorLearn</title>
</head>
<body>
    <h1>404 - Página no encontrada</h1>
    <p>La página que buscas no existe o ha sido movida.</p>
    <a href="<?= BASE_URL ?>">Volver al inicio</a>
</body>
</html>
PHP;

$archivos['views/errors/403.php'] = <<<'PHP'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>403 - Acceso denegado | RaptorLearn</title>
</head>
<body>
    <h1>403 - Acceso denegado</h1>
    <p>No tienes permisos para acceder a esta sección.</p>
    <a href="<?= BASE_URL ?>">Volver al inicio</a>
</body>
</html>
PHP;

// --- CSS base ---
$archivos['public/css/style.css'] = <<<'CSS'
/* ============================================================
   RAPTORLEARN - Estilos base
   ============================================================ */

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    font-size: 16px;
    line-height: 1.6;
    color: #333;
    background: #f5f5f5;
}

/* Navegación */
header nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    background: #1a3a2a;
    color: white;
}

header nav a {
    color: white;
    text-decoration: none;
}

header nav ul {
    display: flex;
    list-style: none;
    gap: 1.5rem;
}

/* Contenido principal */
main {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

/* Grid de especies */
.grid-especies {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.tarjeta-especie {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.tarjeta-especie:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.tarjeta-especie img {
    width: 100%;
    height: 140px;
    object-fit: cover;
    border-radius: 4px;
    margin-bottom: 0.5rem;
}

/* Estados de conservación */
.conservacion { 
    display: inline-block; 
    padding: 2px 8px; 
    border-radius: 4px; 
    font-size: 0.8rem; 
    font-weight: bold; 
    color: white; 
}
.estado-lc { background: #60C659; }
.estado-nt { background: #CCE226; color: #333; }
.estado-vu { background: #F9E814; color: #333; }
.estado-en { background: #FC7F3F; }
.estado-cr { background: #D81E05; }

/* Botón principal */
.btn-principal {
    display: inline-block;
    margin-top: 1rem;
    padding: 0.75rem 2rem;
    background: #2d6a4f;
    color: white;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.2s;
}

.btn-principal:hover {
    background: #1b4332;
}

/* Footer */
footer {
    text-align: center;
    padding: 2rem;
    margin-top: 3rem;
    background: #1a3a2a;
    color: #aaa;
    font-size: 0.9rem;
}
CSS;

// --- JS base ---
$archivos['public/js/main.js'] = <<<'JS'
// ============================================================
// RAPTORLEARN - JavaScript principal
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
    console.log('RaptorLearn cargado correctamente.');
});
JS;

// ============================================================
// EJECUCIÓN: crear carpetas y archivos
// ============================================================
$errores = [];
$creados = [];

// Crear carpetas
foreach ($carpetas as $carpeta) {
    $ruta = $base . '/' . $carpeta;
    if (!is_dir($ruta)) {
        if (mkdir($ruta, 0755, true)) {
            $creados[] = ' ' . $carpeta . '/';
        } else {
            $errores[] = ' No se pudo crear: ' . $carpeta . '/';
        }
    } else {
        $creados[] = ' Ya existía: ' . $carpeta . '/';
    }
}

// Crear archivos
foreach ($archivos as $relativo => $contenido) {
    // Asegurar que la carpeta padre existe
    $dir = $base . '/' . dirname($relativo);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $ruta = $base . '/' . $relativo;
    if (!file_exists($ruta)) {
        if (file_put_contents($ruta, $contenido) !== false) {
            $creados[] = ' ' . $relativo;
        } else {
            $errores[] = ' No se pudo crear: ' . $relativo;
        }
    } else {
        $creados[] = ' Ya existía (no sobreescrito): ' . $relativo;
    }
}

// ============================================================
// OUTPUT HTML
// ============================================================
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>RaptorLearn - Setup</title>
    <style>
        body { font-family: monospace; background: #1a1a2e; color: #eee; padding: 2rem; }
        h1   { color: #52b788; margin-bottom: 1rem; }
        h2   { color: #74c69d; margin: 1.5rem 0 0.5rem; }
        li   { padding: 2px 0; }
        .ok  { color: #52b788; }
        .err { color: #e63946; }
        .warn{ color: #f4a261; }
        .aviso { background: #e63946; color: white; padding: 1rem; border-radius: 6px; margin-top: 2rem; font-size: 1.1rem; }
        a    { color: #52b788; }
    </style>
</head>
<body>
    <h1>RaptorLearn - Instalación de estructura MVC</h1>

    <h2>Resultado:</h2>
    <ul>
        <?php foreach ($creados as $msg): ?>
            <li class="<?= str_starts_with($msg, '') ? 'ok' : (str_starts_with($msg, '') ? 'err' : 'warn') ?>">
                <?= htmlspecialchars($msg) ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($errores): ?>
        <h2>Errores:</h2>
        <ul>
            <?php foreach ($errores as $err): ?>
                <li class="err"><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <div class="aviso">
        <strong>IMPORTANTE:</strong> Elimina este archivo <code>setup.php</code> ahora que la instalación ha terminado.<br><br>
        Luego abre <a href="http://localhost/raptorlearn">http://localhost/raptorlearn</a> para ver la aplicación.
    </div>
</body>
</html>