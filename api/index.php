<?php
//Strict type checking
declare(strict_types=1);

//Show errors
ini_set("display_errors", "On");

header("Content-type: application/json; charset=UTF-8");

require dirname(__DIR__) . "/vendor/autoload.php";

//Set Exception
set_exception_handler("ErrorHandler::handleException");

$dotnev = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotnev->load();

// Create varible. This variable is equals to our URI
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Explode URI for `/`
$parts = explode("/", $path);

$resource = $parts[3];

// If there is no Id $id will be null
$id = $parts[4] ?? null;

// Add database
$database = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
$database->getConnect();

// Router part
switch ($resource) {
    case "games":
        $gameGateway = new GameGateway($database);
        $controller = new GameController($gameGateway);
        $controller->processRequest($_SERVER['REQUEST_METHOD'], $id);
        break;
    case "developers":
        break;
    case "publishers":
        break;
    default:
        http_response_code(404);
        break;
}

