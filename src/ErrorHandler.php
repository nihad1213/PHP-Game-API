<?php 

class ErrorHandler {

    public static function handleException(Throwable $exception):void {

        // Give Server Error
        http_response_code(500);
        
        // Give error as a JSON format
        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file"=>$exception->getFile(),
            "line"=>$exception->getLine()
        ]);
    }
}