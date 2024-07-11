<?php

class DeveloperGateway {
    private PDO $conn;
    
    public function __construct(Database $database) {
        $this->conn = $database->getConnect();
    }

    /**
     * Get all Developers from the database
     * @return array
     */
    public function getAll(): array {
        $sql = "SELECT * FROM `developers`";

        $stmt = $this->conn->query($sql);
        
        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Get specific Developer by its ID
     * @param string $id
     * @return array|false
     */
    public function get(string $id): array|false {
        $sql = "SELECT * FROM `developers` WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Add a new Developer to the database
     * @param array $data
     * @return string
     */
    public function create(array $data): string {
        $sql = "INSERT INTO `developers` 
                (`name`, `founded`, `headquarters`, `ceo`, `website`, `contact_email`, `number_of_employees`, `notable_games`, `created_at`, `updated_at`) 
                VALUES 
                (:name, :founded, :headquarters, :ceo, :website, :contact_email, :number_of_employees, :notable_games, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    
        $stmt = $this->conn->prepare($sql);
    
        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':founded', $data['founded'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':headquarters', $data['headquarters'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':ceo', $data['ceo'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':website', $data['website'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':contact_email', $data['contact_email'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':number_of_employees', $data['number_of_employees'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':notable_games', $data['notable_games'] ?? null, PDO::PARAM_STR);
    
        $stmt->execute();
    
        return $this->conn->lastInsertId();
    }

    /**
     * Update a specific Developer in the database
     * @param string $id
     * @param array $data
     * @return void
     */
    public function update(string $id, array $data): void {
        try {
            $sql = "UPDATE `developers` SET 
                    `name` = :name, 
                    `founded` = :founded, 
                    `headquarters` = :headquarters, 
                    `ceo` = :ceo, 
                    `website` = :website, 
                    `contact_email` = :contact_email, 
                    `number_of_employees` = :number_of_employees, 
                    `notable_games` = :notable_games, 
                    `updated_at` = CURRENT_TIMESTAMP 
                    WHERE id = :id";
    
            $stmt = $this->conn->prepare($sql);
    
            $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
            $stmt->bindValue(':founded', $data['founded'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':headquarters', $data['headquarters'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':ceo', $data['ceo'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':website', $data['website'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':contact_email', $data['contact_email'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':number_of_employees', $data['number_of_employees'] ?? null, PDO::PARAM_INT);
            $stmt->bindValue(':notable_games', $data['notable_games'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    
            $stmt->execute();
    
            if ($stmt->rowCount() === 0) {
                throw new Exception("Developer with ID $id not found or no changes made.");
            }
    
            http_response_code(200);
            echo json_encode(["success" => "Developer updated!", "id" => $id]);
    
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    /**
     * Delete a specific Developer from the database
     * @param string $id
     * @return void
     */
    public function delete(string $id): void {
        $sql = "DELETE FROM `developers` WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            throw new Exception("Developer with ID $id not found.");
        }
    }
}
