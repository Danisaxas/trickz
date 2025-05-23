<?php
require 'vendor/autoload.php'; // Composer autoload for mongodb/mongodb

use MongoDB\Client;

class MongoDBConnection {
    private $client;
    private $database;
    private $collection;

    public function __construct() {
        $uri = "mongodb://mongo:yDbngSrObCWdQOebAgPPpyhzUlgLcxjB@centerbeam.proxy.rlwy.net:46196";
        $this->client = new Client($uri);
        $this->database = $this->client->selectDatabase('minoruchk');
        $this->collection = $this->database->selectCollection('user');

        // Create unique index on telegramId to prevent duplicates
        $this->collection->createIndex(['telegramId' => 1], ['unique' => true]);
    }

    public function registerUser($telegramId, $password, $key) {
        if ($key !== 'Dan1906') {
            throw new Exception("Invalid registration key.");
        }

        // Check if telegramId is numeric
        if (!is_numeric($telegramId)) {
            throw new Exception("Telegram ID must be a number.");
        }

        // Check if user already exists
        $existingUser = $this->collection->findOne(['telegramId' => $telegramId]);
        if ($existingUser) {
            throw new Exception("User with this Telegram ID already exists.");
        }

        // Hash password securely
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insert user document
        $result = $this->collection->insertOne([
            'telegramId' => $telegramId,
            'password' => $passwordHash,
            'createdAt' => new MongoDB\BSON\UTCDateTime(),
        ]);

        return $result->getInsertedCount() === 1;
    }
}