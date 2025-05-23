```php
<?php
require 'vendor/autoload.php'; // Make sure you have installed mongodb/mongodb via Composer

use MongoDB\Client;

try {
    // Connection string
    $uri = "mongodb://mongo:yDbngSrObCWdQOebAgPPpyhzUlgLcxjB@centerbeam.proxy.rlwy.net:46196";

    // Create MongoDB client
    $client = new Client($uri);

    // Select database (will be created if not exists)
    $database = $client->selectDatabase('minoruchk');

    // Select collection (table equivalent)
    $collection = $database->selectCollection('user');

    // Optional: Create an index on telegramId to ensure uniqueness
    $collection->createIndex(['telegramId' => 1], ['unique' => true]);

    echo "Connected to MongoDB and 'user' collection is ready.";
} catch (Exception $e) {
    echo "Error connecting to MongoDB: " . $e->getMessage();
}
?>
