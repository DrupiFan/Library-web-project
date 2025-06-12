<?php
include '../includes/db.php';
$data = $conn->query("SELECT category, COUNT(*) as count 
                      FROM borrow_records br
                      JOIN books b ON br.book_id = b.id
                      GROUP BY category");

$categories = [];
$counts = [];
while ($row = $data->fetch_assoc()) {
    $categories[] = $row['category'];
    $counts[] = $row['count'];
}
echo json_encode(['categories' => $categories, 'counts' => $counts]);
?>