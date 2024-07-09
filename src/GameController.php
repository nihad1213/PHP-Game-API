<?php
class GameController {

    public function __construct(private GameGateway $gateway) {

    }
    public function processRequest(string $method, ?string $id): void {
        // If there is no ID
        if ($id === null) {
            
            if ($method == "GET") {
            
                echo json_encode($this->gateway->getAll());

            } else if ($method == "POST") {
                
                $data = (array) json_decode(file_get_contents("php://input"), true);
                $errors = $this->getValidationErrors($data);
                
                if (!empty($errors)) {
                    $this->respondUnprocessableEntity($errors);
                    return;
                }

                $id = $this->gateway->create($data);
                $this->respondCreated($id);
                
            } else {

                $this->respondMethodNotAllowed("POST, GET");

            }
        
        // If there is ID
        } else {

            $game = $this->gateway->get($id);

            if ($game == false) {
                $this->respondNotFound($id);
            }

            switch ($method) {

                case "GET":

                    echo json_encode($game);
                    break;

                case "PATCH":
                    $data = (array) json_decode(file_get_contents("php://input"), true);
                    $errors = $this->getValidationErrors($data, false);
                    
                    if (!empty($errors)) {
                        $this->respondUnprocessableEntity($errors);
                        return;
                    }
                    
                    try {
                        $this->gateway->update($id, $data);
                        echo json_encode(["success" => "Game updated!", "id" => $id]);
                    } catch (Exception $e) {
                        http_response_code(500);
                        echo json_encode(["error" => $e->getMessage()]);
                    }

                    break;
                
                case "DELETE":

                    try {
                        $this->gateway->delete($id);
                        echo json_encode(["success" => "Game deleted!", "id" => $id]);
                    } catch (Exception $e) {
                        http_response_code(500);
                        echo json_encode(["error" => $e->getMessage()]);
                    }
                    break;
                
                default:
                    
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE");
            }
        }
    }


    private function respondUnprocessableEntity(array $errors): void {
        http_response_code(422);
        echo json_encode(["errors"=>$errors]);
    }

    private function respondMethodNotAllowed(string $allowedMethods): void {
        http_response_code(405);
        header("Allow: $allowedMethods");
    }

    private function respondNotFound(string $id): void {
        ob_start();
        http_response_code(404);
        echo json_encode(["error" => "Game with ID $id is not found!"]);
        ob_end_flush();
        exit;
    }

    private function respondCreated(string $id): void {
        http_response_code(201);
        echo json_encode(["success" => "Game created!", "id" => $id]);
    }

    private function getValidationErrors(array $data, bool $is_new = true): array {
        $errors = [];
    
        // Check if title is empty when creating a new entry
        if ($is_new && !empty($data['title'])) {
            $errors[] = "Title is required.";
        }
    
        // Validate status if provided
        if (isset($data['status'])) {
            if (!filter_var($data['status'], FILTER_VALIDATE_BOOLEAN)) {
                $errors[] = "Status must be true or false.";
            }
        }
    
        return $errors;
    }
    
    

}
