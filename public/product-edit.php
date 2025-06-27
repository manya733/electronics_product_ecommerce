<?php
require __DIR__ . '/../config/db.php';

if (!isset($_GET['id'])) {
    echo "Product ID is missing.";
    exit;
}

$conn = getPDOConnection();
$id = (int) $_GET['id'];
$errors = [];

// Fetch existing product
$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id AND is_deleted = 0");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product Not Found");
}

$name   = $product['name'];
$price  = $product['price'];
$stock  = $product['stock'];
$status = $product['status'];
$image  = $product['image'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name   = trim($_POST['name']);
    $price  = $_POST['price'];
    $stock  = $_POST['stock'];
    $status = $_POST['status'];

    if (empty($name)) $errors[] = "Product name is required";
    if (!is_numeric($price)) $errors[] = "Valid price is required";
    if (!is_numeric($stock)) $errors[] = "Valid stock is required";

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $uploadDir = __DIR__ . "/../public/images/";
        $uploadPath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            $image = $imageName;
        } else {
            $errors[] = "Failed to upload image.";
        }
    }

    if (empty($errors)) {
        $query = "UPDATE products SET name = :name, image = :image, price = :price, stock = :stock, status = :status WHERE id = :id";
        $stmt = $conn->prepare($query);
        $success = $stmt->execute([
            'name' => $name,
            'image' => $image,
            'price' => (float)$price,
            'stock' => (int)$stock,
            'status' => $status,
            'id' => $id
        ]);

        if ($success) {
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Update failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Edit Product</h2>

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
            <label>Current Image</label><br>
            <?php if ($image): ?>
                <img src="images/<?= htmlspecialchars($image) ?>" width="100">
            <?php else: ?>
                <p>No image uploaded</p>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label>Upload New Image (optional)</label>
            <input type="file" name="image" class="form-control">
        </div>
        <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active" <?= ($status == 'active') ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($status == 'inactive') ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Product</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
