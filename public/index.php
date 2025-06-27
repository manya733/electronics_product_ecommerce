<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/header.php';
require __DIR__ . '/../models/Product.php';

$conn = getPDOConnection(); // ✅ Get PDO connection

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';

$products = getAllProducts($conn, $search, $status);

// Include view properly
// require __DIR__ . '/../views/index.view.php';

// require __DIR__ . '/../config/footer.php';


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Electronics Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .card-img-top {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
      width: 60%;
      height: 180px;
      object-fit: cover;
      border-radius: 10px;
      margin: 40px auto 0;
    }
    .product-card {
      transition: 0.3s;
      box-shadow: 0 0 8px rgba(0,0,0,0.08);
      border-radius: 10px;
    }
    .product-card:hover {
      box-shadow: 0 0 15px rgba(0,0,0,0.15);
    }
  </style>
</head>
<body class="bg-light">

<div class="container my-5">
  <h2 class="text-center mb-4">Explore Our Electronics Products</h2>


  <form method="get" class="row g-3 justify-content-center mb-5 "  enctype="multipart/form-data">
    <div class="col-md-3">
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search product">
    </div>
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option value="">All Status</option>
        <option value="active" <?= ($status == 'active') ? 'selected' : '' ?>>Active</option>
        <option value="inactive" <?= ($status == 'inactive') ? 'selected' : '' ?>>Inactive</option>
      </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
      <button type="submit" class="btn btn-primary">Search</button>
      <a href="product-add.php" class="btn btn-success">Add Product</a>
    </div>
  </form>

 
<div class="row">
  <?php if (count($products) > 0): ?>
    <?php foreach ($products as $row): ?>
      <div class="col-md-3 mb-4">
        <div class="card product-card h-100 text-center">
          <?php if (!empty($row['image'])): ?>
            <img src="images/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="Product">
          <?php else: ?>
            <img src="images/no-image.png" class="card-img-top" alt="No image">
          <?php endif; ?>
          <div class="card-body">
            <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
            <p class="card-text text-muted mb-1">Price: ₹<?= htmlspecialchars($row['price']) ?></p>
            <p class="card-text mb-1">Stock: <?= htmlspecialchars($row['stock']) ?></p>
            <p class="text-secondary">Status: <strong><?= ucfirst(htmlspecialchars($row['status'])) ?></strong></p>
            <div class="d-flex justify-content-center gap-2 mt-2">
              <a href="product-edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
              <a href="product-delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
              <a href="product-status.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-secondary">
                <?= ($row['status'] == 'active') ? 'Deactivate' : 'Activate' ?>
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="col-12">
      <div class="alert alert-info text-center">No products found.</div>
    </div>
  <?php endif; ?>
</div>

</div>

</body>
</html>
<?php
require __DIR__ . '/../config/footer.php';
?>