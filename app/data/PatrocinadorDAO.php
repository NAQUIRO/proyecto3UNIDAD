<?php
require_once 'Database.php';

class PatrocinadorDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener patrocinadores filtrados por tipo (Principal, Oficial, Colaborador)
    public function obtenerPorTipo($tipo) {
        $query = "SELECT * FROM patrocinadores WHERE tipo = :tipo";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tipo", $tipo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerTodos() {
        $query = "SELECT * FROM patrocinadores";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>