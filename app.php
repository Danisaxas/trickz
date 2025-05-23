```php
<?php
require 'vendor/autoload.php'; // Make sure mongodb/mongodb is installed via Composer

use MongoDB\Client;

// Enable CORS for local testing (adjust as needed)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['message' => 'Method Not Allowed']);
    exit;
}

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (
    !isset($input['telegramId']) ||
    !isset($input['password']) ||
    !isset($input['key'])
) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing required fields']);
    exit;
}

$telegramId = trim($input['telegramId']);
$password = trim($input['password']);
$key = trim($input['key']);

// Validate telegramId is numeric
if (!is_numeric($telegramId)) {
    http_response_code(400);
    echo json_encode(['message' => 'ID must be a number']);
    exit;
}

// Validate key
if ($key !== 'Dan1906') {
    http_response_code(403);
    echo json_encode(['message' => 'Invalid registration key']);
    exit;
}

try {
    $uri = "mongodb://mongo:yDbngSrObCWdQOebAgPPpyhzUlgLcxjB@centerbeam.proxy.rlwy.net:46196";
    $client = new Client($uri);
    $database = $client->selectDatabase('minoruchk');
    $collection = $database->selectCollection('user');

    // Check if user already exists
    $existingUser = $collection->findOne(['telegramId' => $telegramId]);
    if ($existingUser) {
        http_response_code(409);
        echo json_encode(['message' => 'User with this Telegram ID already exists']);
        exit;
    }

    // Hash password securely
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user document
    $insertResult = $collection->insertOne([
        'telegramId' => $telegramId,
        'password' => $passwordHash,
        'createdAt' => new MongoDB\BSON\UTCDateTime(),
    ]);

    if ($insertResult->getInsertedCount() === 1) {
        echo json_encode(['message' => 'User registered successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Failed to register user']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Server error: ' . $e->getMessage()]);
}
