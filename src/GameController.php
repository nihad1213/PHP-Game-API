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

                    echo "update $id";
                    break;
                
                case "DELETE":

                    echo "Delete {$id}";
                    break;
                
                default:
                    
                    $this->respondMethodNotAllowed("GET, PATCH, DELETE");
            }
        }
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
    

}
