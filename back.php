<?php
header("Content-Type: application/json");

// Database connection (WAMP defaults)
$servername = "localhost";
$username   = "root";   // default MySQL user
$password   = "";       // default has no password
$dbname     = "recipe_book";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Handle POST (Save new recipe)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = $_POST['title'];
    $ingredients = $_POST['ingredients'];
    $image       = $_POST['image'];
    $source_url  = $_POST['source_url'];

    $stmt = $conn->prepare("INSERT INTO recipes (title, ingredients, image, source_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $ingredients, $image, $source_url);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Recipe added successfully"]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Handle GET (Fetch all recipes)
$result = $conn->query("SELECT title, ingredients, image, source_url FROM recipes ORDER BY title ASC");
$recipes = [];

while ($row = $result->fetch_assoc()) {
    $recipes[] = $row;
}

echo json_encode($recipes);
$conn->close();
?>
