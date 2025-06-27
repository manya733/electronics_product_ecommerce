<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Product.php';

$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$products = getAllProducts($conn, $search, $status);

require_once __DIR__ . '/../views/products/index.view.php';
?>