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

    /**
     * Add data to database
     * @param array $data
     * @return string
     */
    public function create(array $data): string {
        
        $sql = "INSERT INTO `games` 
                (`title`, `genre`, `platform`, `developer_ID`, `publisher_ID`, `release_data`, `status`, `rating`, `price`, `description`, `cover_image`, `created_at`, `updated_at`) 
                VALUES 
                (:title, :genre, :platform, :developer_ID, :publisher_ID, :release_data, :status, :rating, :price, :description, :cover_image, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    
        $stmt = $this->conn->prepare($sql);
    
        // Avoiding from error: Array to string
        $platformJson = json_encode($data['platform']);
    
        // Bind values to placeholders
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':genre', $data['genre'], PDO::PARAM_STR);
        $stmt->bindValue(':platform', $platformJson, PDO::PARAM_STR);
        $stmt->bindValue(':developer_ID', $data['developer_ID'], PDO::PARAM_INT);
        $stmt->bindValue(':publisher_ID', $data['publisher_ID'], PDO::PARAM_INT);
        $stmt->bindValue(':release_data', $data['release_data'], PDO::PARAM_STR);
        $stmt->bindValue(':status', $data['status'], PDO::PARAM_BOOL);
        $stmt->bindValue(':rating', $data['rating'], PDO::PARAM_STR);
        $stmt->bindValue(':price', $data['price'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);
        $stmt->bindValue(':cover_image', $data['cover_image'], PDO::PARAM_STR);
    
        if (empty($data['status'])) {
            $stmt->bindValue(":status", null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(":status", $data['status'], PDO::PARAM_INT);
        }
    
        $stmt->execute();
    
        return $this->conn->lastInsertId();
    }
    
    
}
