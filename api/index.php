<?php

// Create varible. This variable is equals to our URI
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Explode URI for `/`
$parts = explode("/", $path);

$resource = $parts[3];
// If there is no Id $id will be null
$id = $parts[4] ?? null;


