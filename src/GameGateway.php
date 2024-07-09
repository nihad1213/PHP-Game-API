<?php

class GameGateway {
    private PDO $conn;
    
    public function __construct(Database $database) {
        $this->conn=$database->getConnect();
    }

    public function getAll(): array {
        $sql = "SELECT * FROM `games`";

        $stmt = $this->conn->query($sql);
        
        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['status'] = (bool) $row['status'];
            $data = $row;
        }

        return $data;
    }
}