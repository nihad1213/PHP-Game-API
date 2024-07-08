<?php

class GameController {

    public function processRequest(string $method, ?string $id): void {
        // If there is no ID
        if ($id === null) {
            
            if ($method == "GET") {
            
                echo "index Game";
            
            } else if ($method == "POST") {
                
                echo "Created";
                
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
            }
        }
    }

}