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
        // Check content type of incoming request
        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

        if ($contentType === "application/json") {
            // Handle JSON input
            $input = json_decode(file_get_contents('php://input'), true);

            $title = $input['title'] ?? null;
            $genre = $input['genre'] ?? null;
            $platform = $input['platform'] ?? null;
            $developer_ID = $input['developer_ID'] ?? null;
            $publisher_ID = $input['publisher_ID'] ?? null;
            $release_data = $input['release_data'] ?? null;
            $status = $input['status'] ?? null;
            $rating = $input['rating'] ?? null;
            $price = $input['price'] ?? null;
            $description = $input['description'] ?? null;

        } elseif (strpos($contentType, "multipart/form-data") !== false) {
            // Handle form-data input
            $title = $_POST['title'] ?? null;
            $genre = $_POST['genre'] ?? null;
            $platform = $_POST['platform'] ?? null;
            $developer_ID = $_POST['developer_ID'] ?? null;
            $publisher_ID = $_POST['publisher_ID'] ?? null;
            $release_data = $_POST['release_data'] ?? null;
            $status = $_POST['status'] ?? null;
            $rating = $_POST['rating'] ?? null;
            $price = $_POST['price'] ?? null;
            $description = $_POST['description'] ?? null;

        } else {
            // Unsupported content type
            throw new Exception("Unsupported content type: $contentType");
        }

        // Check for required fields
        $requiredFields = ['title', 'genre', 'platform', 'developer_ID', 'publisher_ID', 'release_data', 'rating', 'price', 'description'];
        foreach ($requiredFields as $field) {
            if (empty($$field)) {
                throw new Exception("Missing required field: $field");
            }
        }

        // Handle file upload
        $fileName = null;
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $targetDir = __DIR__ . "/../uploads/games/";

            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0775, true);
            }
            
            // Generate file name
            $fileName = basename($_FILES['cover_image']['name']);
            $targetFilePath = $targetDir . uniqid() . "_" . $fileName;
            
            // Move the uploaded file to the target directory
            if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $targetFilePath)) {
                throw new Exception("Failed to move uploaded file.");
            }
        }

        // Prepare SQL statement
        $sql = "INSERT INTO `games` 
                (`title`, `genre`, `platform`, `developer_ID`, `publisher_ID`, `release_data`, `status`, `rating`, `price`, `description`, `cover_image`, `created_at`, `updated_at`) 
                VALUES 
                (:title, :genre, :platform, :developer_ID, :publisher_ID, :release_data, :status, :rating, :price, :description, :cover_image, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    
        $stmt = $this->conn->prepare($sql);
    
        // Bind values to placeholders
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':genre', $genre, PDO::PARAM_STR);
        $stmt->bindValue(':platform', json_encode($platform), PDO::PARAM_STR);
        $stmt->bindValue(':developer_ID', $developer_ID, PDO::PARAM_INT);
        $stmt->bindValue(':publisher_ID', $publisher_ID, PDO::PARAM_INT);
        $stmt->bindValue(':release_data', $release_data, PDO::PARAM_STR);
        $stmt->bindValue(':status', $status, PDO::PARAM_INT); // Assuming status is an integer
        $stmt->bindValue(':rating', $rating, PDO::PARAM_STR);
        $stmt->bindValue(':price', $price, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':cover_image', $fileName, PDO::PARAM_STR);
    
        $stmt->execute();
    
        return $this->conn->lastInsertId();
    }

    /**
     * Update a specific game in the database
     * @param string $id
     * @param array $data
     * @return array
     */
    public function update(string $id, array $data): void {
        try {
            // Check if title exists in the input data
            if (!isset($data['title'])) {
                throw new Exception("Missing required field: title");
            }
    
            // Extract fields from $data
            $title = $data['title'];
            $genre = $data['genre'] ?? null;
            $platform = $data['platform'] ?? null;
            $developer_ID = $data['developer_ID'] ?? null;
            $publisher_ID = $data['publisher_ID'] ?? null;
            $release_data = $data['release_data'] ?? null;
            $status = $data['status'] ?? null;
            $rating = $data['rating'] ?? null;
            $price = $data['price'] ?? null;
            $description = $data['description'] ?? null;
    
            // Prepare SQL statement
            $sql = "UPDATE `games` SET 
                    `title` = :title, 
                    `genre` = :genre, 
                    `platform` = :platform, 
                    `developer_ID` = :developer_ID, 
                    `publisher_ID` = :publisher_ID, 
                    `release_data` = :release_data, 
                    `status` = :status, 
                    `rating` = :rating, 
                    `price` = :price, 
                    `description` = :description, 
                    `updated_at` = CURRENT_TIMESTAMP 
                    WHERE id = :id";
    
            // Prepare and execute the SQL statement
            $stmt = $this->conn->prepare($sql);
    
            $stmt->bindValue(':title', $title, PDO::PARAM_STR);
            $stmt->bindValue(':genre', $genre, PDO::PARAM_STR);
            $stmt->bindValue(':platform', json_encode($platform), PDO::PARAM_STR);
            $stmt->bindValue(':developer_ID', $developer_ID, PDO::PARAM_INT);
            $stmt->bindValue(':publisher_ID', $publisher_ID, PDO::PARAM_INT);
            $stmt->bindValue(':release_data', $release_data, PDO::PARAM_STR);
            $stmt->bindValue(':status', $status, PDO::PARAM_INT); // Assuming status is an integer
            $stmt->bindValue(':rating', $rating, PDO::PARAM_STR);
            $stmt->bindValue(':price', $price, PDO::PARAM_STR);
            $stmt->bindValue(':description', $description, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    
            $stmt->execute();
    
            // Check if any rows were affected
            if ($stmt->rowCount() === 0) {
                throw new Exception("Game with ID $id not found or no changes made.");
            }
    
            // Success response
            http_response_code(200);
            echo json_encode(["success" => "Game updated!", "id" => $id]);
    
        } catch (Exception $e) {
            // Handle or respond to the error appropriately
            http_response_code(400); // or appropriate error code
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    /**
     * Delete a specific game from the database
     * @param string $id
     * @return void
     */
    public function delete(string $id): void {
        $sql = "DELETE FROM `games` WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        
        $stmt->execute();
        
        // Check if any rows were affected
        if ($stmt->rowCount() === 0) {
            throw new Exception("Game with ID $id not found.");
        }
        
        // Optionally, you can handle success response here
        // For example, setting HTTP response code or logging success
    }
}
