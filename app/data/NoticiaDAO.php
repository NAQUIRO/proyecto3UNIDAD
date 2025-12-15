<?php
require_once 'Database.php';

class NoticiaDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerDestacada() {
        $query = "SELECT * FROM noticias WHERE es_destacada = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerRecientes() {
        $query = "SELECT * FROM noticias WHERE es_destacada = 0 ORDER BY fecha_publicacion DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>