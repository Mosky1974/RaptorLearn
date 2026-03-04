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
                 . ';port=' . DB_PORT
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
                die('Error: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}