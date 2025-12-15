<?php
require_once 'Database.php';

class EventoDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Listar todos los eventos
    public function obtenerTodos() {
        $query = "SELECT * FROM eventos ORDER BY fecha_inicio ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un evento por ID
    public function obtenerPorId($id) {
        $query = "SELECT * FROM eventos WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear nuevo evento
    public function crear($titulo, $fecha_texto, $lugar, $precio, $imagen) {
        $query = "INSERT INTO eventos (titulo, fecha_texto, lugar, precio, imagen) VALUES (:titulo, :fecha, :lugar, :precio, :imagen)";
        $stmt = $this->conn->prepare($query);
        // Bind parameters...
        // Retorna true si se ejecuta correctamente
        return $stmt->execute([
            ':titulo' => $titulo,
            ':fecha' => $fecha_texto,
            ':lugar' => $lugar,
            ':precio' => $precio,
            ':imagen' => $imagen
        ]);
    }
}
?>