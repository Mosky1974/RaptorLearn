<?php
class EspecieModel extends Model {

    protected string $tabla = 'especies';

    /**
     * Obtener especies destacadas para la portada
     */
    public function obtenerDestacadas(int $limite = 6): array {
    $stmt = $this->db->prepare(
        "SELECT e.id_especie, e.nombre_comun, e.nombre_cientifico, 
                e.estado_conservacion, e.dificultad_identificacion,
                i.ruta_imagen, i.creditos
         FROM especies e
         LEFT JOIN imagenes_especies i 
                ON i.id_especie = e.id_especie 
               AND i.es_principal = 1
         WHERE e.activa = 1 
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