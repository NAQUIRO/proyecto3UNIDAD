<?php
require_once 'Database.php';

class PonenteDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function obtenerTodos() {
        $query = "SELECT * FROM ponentes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Método para insertar ponente (usado por el admin)
    public function insertar($nombre, $cargo, $tema, $imagen) {
        $query = "INSERT INTO ponentes (nombre, cargo, tema, imagen) VALUES (:nombre, :cargo, :tema, :imagen)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':cargo' => $cargo,
            ':tema' => $tema,
            ':imagen' => $imagen
        ]);
    }
}
?>