<?php
require_once 'Database.php';

class UsuarioDAO {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Buscar usuario por email para el login
    public function obtenerPorEmail($email) {
        $query = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Registrar nuevo usuario (hash de password se hace en el Service)
    public function registrar($nombre, $email, $passwordHash) {
        $query = "INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :pass)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':pass' => $passwordHash
        ]);
    }
}
?>