<?php

class PublisherGateway {
    private PDO $conn;
    
    public function __construct(Database $database) {
        $this->conn = $database->getConnect();
    }

    /**
     * Get all Publishers from database
     * @return array
     */
    public function getAll(): array {
        $sql = "SELECT * FROM `publishers`";

        $stmt = $this->conn->query($sql);
        
        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row; // Append each row to the $data array
        }

        return $data;
    }

    /**
     * Get specific Publisher by its ID
     * @param string $id
     * @return array|false
     */
    public function get(string $id): array|false {
        $sql = "SELECT * FROM `publishers` WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Add a new Publisher to the database
     * @param array $data
     * @return string
     */
    public function create(array $data): string {
        // Check content type of incoming request
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === "application/json") {
            // Handle JSON input
            $input = json_decode(file_get_contents('php://input'), true);

            $name = $input['name'] ?? null;
            $founded = $input['founded'] ?? null;
            $headquarters = $input['headquarters'] ?? null;
            $ceo = $input['ceo'] ?? null;
            $website = $input['website'] ?? null;
            $contact_email = $input['contact_email'] ?? null;

        } elseif (strpos($contentType, "multipart/form-data") !== false) {
            // Handle form-data input
            $name = $_POST['name'] ?? null;
            $founded = $_POST['founded'] ?? null;
            $headquarters = $_POST['headquarters'] ?? null;
            $ceo = $_POST['ceo'] ?? null;
            $website = $_POST['website'] ?? null;
            $contact_email = $_POST['contact_email'] ?? null;

        } else {
            // Unsupported content type
            throw new Exception("Unsupported content type: $contentType");
        }

        // Check for required fields
        if (empty($name)) {
            throw new Exception("Missing required field: name");
        }

        // Prepare SQL statement
        $sql = "INSERT INTO `publishers` 
                (`name`, `founded`, `headquarters`, `ceo`, `website`, `contact_email`, `created_at`, `updated_at`) 
                VALUES 
                (:name, :founded, :headquarters, :ceo, :website, :contact_email, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    
        $stmt = $this->conn->prepare($sql);
    
        // Bind values to placeholders
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':founded', $founded, PDO::PARAM_STR);
        $stmt->bindValue(':headquarters', $headquarters, PDO::PARAM_STR);
        $stmt->bindValue(':ceo', $ceo, PDO::PARAM_STR);
        $stmt->bindValue(':website', $website, PDO::PARAM_STR);
        $stmt->bindValue(':contact_email', $contact_email, PDO::PARAM_STR);
    
        $stmt->execute();
    
        return $this->conn->lastInsertId();
    }

    /**
     * Update a specific Publisher in the database
     * @param string $id
     * @param array $data
     * @return void
     */
    public function update(string $id, array $data): void {
        try {
            // Check if name exists in the input data
            if (!isset($data['name'])) {
                throw new Exception("Missing required field: name");
            }
    
            // Extract fields from $data
            $name = $data['name'];
            $founded = $data['founded'] ?? null;
            $headquarters = $data['headquarters'] ?? null;
            $ceo = $data['ceo'] ?? null;
            $website = $data['website'] ?? null;
            $contact_email = $data['contact_email'] ?? null;
    
            // Prepare SQL statement
            $sql = "UPDATE `publishers` SET 
                    `name` = :name, 
                    `founded` = :founded, 
                    `headquarters` = :headquarters, 
                    `ceo` = :ceo, 
                    `website` = :website, 
                    `contact_email` = :contact_email, 
                    `updated_at` = CURRENT_TIMESTAMP 
                    WHERE id = :id";
    
            // Prepare and execute the SQL statement
            $stmt = $this->conn->prepare($sql);
    
            $stmt->bindValue(':name', $name, PDO::PARAM_STR);
            $stmt->bindValue(':founded', $founded, PDO::PARAM_STR);
            $stmt->bindValue(':headquarters', $headquarters, PDO::PARAM_STR);
            $stmt->bindValue(':ceo', $ceo, PDO::PARAM_STR);
            $stmt->bindValue(':website', $website, PDO::PARAM_STR);
            $stmt->bindValue(':contact_email', $contact_email, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    
            $stmt->execute();
    
            // Check if any rows were affected
            if ($stmt->rowCount() === 0) {
                throw new Exception("Publisher with ID $id not found or no changes made.");
            }
    
            // Success response
            http_response_code(200);
            echo json_encode(["success" => "Publisher updated!", "id" => $id]);
    
        } catch (Exception $e) {
            // Handle or respond to the error appropriately
            http_response_code(400); // or appropriate error code
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    /**
     * Delete a specific Publisher from the database
     * @param string $id
     * @return void
     */
    public function delete(string $id): void {
        $sql = "DELETE FROM `publishers` WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        // Check if any rows were affected
        if ($stmt->rowCount() === 0) {
            throw new Exception("Publisher with ID $id not found.");
        }
        
        // Optionally, you can handle success response here
        // For example, setting HTTP response code or logging success
    }
}
