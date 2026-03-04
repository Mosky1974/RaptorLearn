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