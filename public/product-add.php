<?php
require_once '../config/db.php';
$conn = getPDOConnection();
$name = $price = $stock = $status = "";
$image = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name']);
    $price  = $_POST['price'];
    $stock  = $_POST['stock'];
    $status = $_POST['status'];


    if (empty($name)) $errors[] = "Name is required";
    if (!is_numeric($price)) $errors[] = "Price must be a number";
    if (!is_numeric($stock)) $errors[] = "Stock must be a number";


    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Only JPG, PNG, GIF, and WEBP images are allowed.";
        } else {
            $imageName = time() . '_' . basename($_FILES['image']['name']);
            $uploadDir = __DIR__ . "/images/";
            $uploadPath = $uploadDir . $imageName;

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $image = $imageName;
            } else {
                $errors[] = "Failed to move uploaded image.";
            }
        }
    } else {
        $errors[] = "Image is required";
    }


    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO products (name, image, price, stock, status, is_deleted)
                                    VALUES (:name, :image, :price, :stock, :status, 0)");
            $stmt->execute([
                ':name'   => $name,
                ':image'  => $image,
                ':price'  => $price,
                ':stock'  => $stock,
                ':status' => $status
            ]);

            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Add New Product</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Product Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label>Price (₹)</label>
            <input type="text" name="price" value="<?= htmlspecialchars($price) ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label>Stock</label>
            <input type="number" name="stock" value="<?= htmlspecialchars($stock) ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label>Image</label>
            <input type="file" name="image" class="form-control">
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active" <?= ($status == 'active') ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($status == 'inactive') ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
