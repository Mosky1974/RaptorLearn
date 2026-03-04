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