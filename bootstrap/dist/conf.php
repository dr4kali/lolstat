<?php

$databaseName = 'lolstat';
$username = 'user';
$password = 'password';

try {
    $db = new PDO("mysql:host=localhost;dbname=$databaseName", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Database connection failed: ' . $e->getMessage();
    exit();
}
