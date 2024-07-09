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
                
                echo "Created";
                
            } else {

                $this->respondMethodNotAllowed("POST, GET");

            }
        
        // If there is ID
        } else {
            switch ($method) {

                case "GET":

                    echo "show {$id}";
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

}