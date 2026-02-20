<?php

if (!class_exists('MongoDB\\Client')) {
    $autoload = __DIR__ . '/vendor/autoload.php';
    if (file_exists($autoload)) {
        require_once $autoload;
    }
}

if (!class_exists('MongoDB\\Client')) {
    die('MongoDB library not found. Run: composer require mongodb/mongodb');
}

$mongoUri = getenv('MONGODB_URI') ?: 'mongodb://127.0.0.1:27017';
$dbName = getenv('MONGODB_DB') ?: 'poll_system';

$mongoClient = new MongoDB\Client($mongoUri);
$db = $mongoClient->selectDatabase($dbName);

function oid($id)
{
    if (!is_string($id) || !preg_match('/^[a-f0-9]{24}$/i', $id)) {
        return null;
    }

    return new MongoDB\BSON\ObjectId($id);
}

function objectIdToString($id)
{
    return $id instanceof MongoDB\BSON\ObjectId ? (string)$id : '';
}

function isPasswordValid($plainPassword, $storedPassword)
{
    if (!is_string($plainPassword) || !is_string($storedPassword)) {
        return false;
    }

    if (password_get_info($storedPassword)['algo'] !== null) {
        return password_verify($plainPassword, $storedPassword);
    }

    return hash_equals($storedPassword, $plainPassword);
}
