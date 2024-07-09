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
        // Retrieve data from form-data
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
    
        // Check for required fields
        $requiredFields = ['title', 'genre', 'platform', 'developer_ID', 'publisher_ID', 'release_data', 'rating', 'price', 'description'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
    
        // Handle file upload
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
        } else {
            $targetFilePath = null;
        }
    
        $sql = "INSERT INTO `games` 
                (`title`, `genre`, `platform`, `developer_ID`, `publisher_ID`, `release_data`, `status`, `rating`, `price`, `description`, `cover_image`, `created_at`, `updated_at`) 
                VALUES 
                (:title, :genre, :platform, :developer_ID, :publisher_ID, :release_data, :status, :rating, :price, :description, :cover_image, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
    
        $stmt = $this->conn->prepare($sql);
    
        // Avoiding from error: Array to string
        $platformJson = json_encode($_POST['platform']);
    
        // Bind values to placeholders
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':genre', $genre, PDO::PARAM_STR);
        $stmt->bindValue(':platform', $platformJson, PDO::PARAM_STR);
        $stmt->bindValue(':developer_ID', $developer_ID, PDO::PARAM_INT);
        $stmt->bindValue(':publisher_ID', $publisher_ID, PDO::PARAM_INT);
        $stmt->bindValue(':release_data', $release_data, PDO::PARAM_STR);
        
        if (empty($status)) {
            $stmt->bindValue(":status", null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(":status", $status, PDO::PARAM_INT);
        }
    
        $stmt->bindValue(':rating', $rating, PDO::PARAM_STR);
        $stmt->bindValue(':price', $price, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':cover_image', $targetFilePath, PDO::PARAM_STR);
    
        $stmt->execute();
    
        return $this->conn->lastInsertId();
    }
    
    
    
    
}
