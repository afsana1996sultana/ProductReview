<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


// Check the Server request and the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "message" => "Simple Product Review REST API Endpoint in PHP and Only POST requests are allowed"
    ]);
    exit;
}

// Only JSON input data
$data = json_decode(file_get_contents("php://input"));


// Validation
if (empty($data->product_id) || !is_numeric($data->product_id)) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid or missing product ID"]);
    exit;
}
if (empty($data->user_id) || !is_numeric($data->user_id)) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid or missing user ID"]);
    exit;
}
if (empty($data->review_text)) {
    http_response_code(400);
    echo json_encode(["message" => "Review text cannot be empty"]);
    exit;
}


// Database connection
$host = 'localhost';
$dbname = 'product_reviews_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT INTO product_reviews (product_id, user_id, review_text) VALUES (?, ?, ?)");
    $stmt->execute([$data->product_id, $data->user_id, $data->review_text]);
    http_response_code(201);
    echo json_encode(["message" => "Review submitted successfully"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Database error: " . $e->getMessage()]);
}

?>
