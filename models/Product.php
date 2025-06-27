<?php
function getAllProducts($conn, $search = '', $status = '') {
    $query = "SELECT * FROM products WHERE is_deleted = 0";
    $params = [];

    if (!empty($search)) {
        $query .= " AND name LIKE :search";
        $params['search'] = "%$search%";
    }

    if (!empty($status)) {
        $query .= " AND status = :status";
        $params['status'] = $status;
    }

    $query .= " ORDER BY id DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute($params);

    return $stmt->fetchAll(); 
}
?>
