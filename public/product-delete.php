<?php
require __DIR__ . '/../config/db.php';


$id = $_GET['id'] ?? 0;
$id = (int)$id;

if ($id > 0) {
    try {
        $conn = getPDOConnection();
        $query = "UPDATE products SET is_deleted = 1 WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $id]);

        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        echo "Delete failed: " . $e->getMessage();
    }
} else {
    echo "Invalid product ID.";
}
