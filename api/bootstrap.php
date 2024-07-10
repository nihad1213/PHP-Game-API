<?php
//Show errors
ini_set("display_errors", "On");

header("Content-type: application/json; charset=UTF-8");

require dirname(__DIR__) . "/vendor/autoload.php";

//Set error
set_error_handler("ErrorHandler::handleError");

//Set Exception
set_exception_handler("ErrorHandler::handleException");

$dotnev = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotnev->load();