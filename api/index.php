<?php
//Strict type checking
declare(strict_types=1);

//Show errors
ini_set("display_errors", "On");

header("Content-type: application/json; charset=UTF-8");

require dirname(__DIR__) . "/vendor/autoload.php";

//Set Exception
set_exception_handler("ErrorHandler::handleException");

// Create varible. This variable is equals to our URI
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Explode URI for `/`
$parts = explode("/", $path);

$resource = $parts[3];

// If there is no Id $id will be null
$id = $parts[4] ?? null;

// Add database
$database = new Database("localhost", "game_db", "game_db_user", "Nihad1213!@");
$database->getConnect();

// Router part
switch ($resource) {
    case "games":
        $controller = new GameController;
        $controller->processRequest($_SERVER['REQUEST_METHOD'], $id);
    case "developers":
        break;
    case "publishers":
        break;
    default:
        http_response_code(404);
        break;
}

