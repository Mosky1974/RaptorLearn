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
require_once __DIR__ . '/core/GamificacionService.php';

// Despachar la petición
$router = new Router();
$router->despachar();