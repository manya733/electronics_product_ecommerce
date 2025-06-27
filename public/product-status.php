<?php
require_once '../config/db.php';
$conn = getPDOConnection(); 

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
   
        $stmt = $conn->prepare("SELECT status FROM products WHERE id = :id AND is_deleted = 0");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();

        if ($product) {
            $currentStatus = $product['status'];
            $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';

          
            $updateStmt = $conn->prepare("UPDATE products SET status = :status WHERE id = :id");
            $updateStmt->execute([
                ':status' => $newStatus,
                ':id'     => $id
            ]);

            header("Location: index.php");
            exit;
        } else {
            echo "Product not found.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid ID.";
}
?>
