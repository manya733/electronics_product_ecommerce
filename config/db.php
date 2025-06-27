<?php
function getPDOConnection() {
    $host = 'localhost';
    $dbname = 'ecommerce';
    $username = 'root';
    $password = 'mysql';

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}
