<?php

require __DIR__ . "/vendor/autoload.php";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $dotnev = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotnev->load();

    $database = new Database($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    $conn = $database->getConnect();

    $sql = "INSERT INTO users (name, username, password_hash, api_key)
            VALUES (:name, :username, :password_hash, :api_key)";

    $stmt = $conn->prepare($sql);

    $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $apiKey = bin2hex(random_bytes(16));

    $stmt->bindValue(":name", $_POST['name'], PDO::PARAM_STR);
    $stmt->bindValue(":username", $_POST['username'], PDO::PARAM_STR);
    $stmt->bindValue(":password_hash", $passwordHash, PDO::PARAM_STR);
    $stmt->bindValue(":api_key", $apiKey, PDO::PARAM_STR);

    $stmt->execute();

    echo "Thanks for registering. Your API key is: ", $apiKey;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css"
    />
    <title>Document</title>
</head>
<body>
    <main class="container">

    <h1>Register</h1>
        <form method="POST">
            <label for="name">
                Name
                <input type="text" name="name" id="name" required>
            </label>

            <label for="username">
                Username
                <input type="text" name="username" required>
            </label>

            <label for="password">
                Password
                <input type="password" name="password" required>
            </label>

            <button type="submit">Register</button>
        </form>
    </main>
</body>
</html>
