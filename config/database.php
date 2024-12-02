<?php
class Database {
    private $host = 'localhost';
    private $dbname = 'rtb_db';
    private $user = 'root';
    private $password = 'password';
    public $conn;

    public function connect() {
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            echo json_encode(['error' => 'Database Connection Failed: ' . $e->getMessage()]);
            exit();
        }
    }
}
?>