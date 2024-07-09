<?php

class GameGateway {
    private PDO $conn;
    
    public function __construct(Database $database) {
        $this->conn = $database->getConnect();
    }

    /**
     * Get all Games from database
     * @return array
     */
    public function getAll(): array {
        $sql = "SELECT * FROM `games`";

        $stmt = $this->conn->query($sql);
        
        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['status'] = (bool) $row['status'];
            $data[] = $row; // Append each row to the $data array
        }

        return $data;
    }

    /**
     * Get specific Game for its ID
     * @param string $id
     * @return array|false
     */
    public function get(string $id): array|false {
        $sql = "SELECT * FROM `games` WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data !== false) {
            $data['status'] = (bool) $data['status'];
        }

        return $data;
    } 
}