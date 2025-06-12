<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../login.php");
    exit();
}

// Handle book return
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['return_book'])) {
    $borrow_id = $_POST['borrow_id'];
    $book_id = $_POST['book_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update borrow record
        $stmt = $conn->prepare("UPDATE borrow_records SET status = 'returned' WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $borrow_id, $_SESSION['user_id']);
        $stmt->execute();
        
        // Update book stock
        $stmt = $conn->prepare("UPDATE books SET stock = stock + 1 WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success'] = "Book returned successfully!";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = "Error returning book: " . $e->getMessage();
    }
    
    header("Location: dashboard.php");
    exit();
}

// Get user's borrowed books
$stmt = $conn->prepare("
    SELECT br.*, b.title, b.author, b.imglink 
    FROM borrow_records br 
    JOIN books b ON br.book_id = b.id 
    WHERE br.user_id = ? 
    ORDER BY br.borrow_date DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$borrowed_books = $stmt->get_result();

// Get statistics
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_borrowed,
        SUM(CASE WHEN status = 'borrowed' THEN 1 ELSE 0 END) as currently_borrowed,
        SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) as returned
    FROM borrow_records 
    WHERE user_id = ?
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Get user's name for greeting
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Prepare data for charts
// 1. Books to return vs returned
$books_to_return = $stats['currently_borrowed'];
$books_returned = $stats['returned'];

// 2. Categories of books borrowed
$stmt = $conn->prepare("
    SELECT b.category, COUNT(*) as count
    FROM borrow_records br
    JOIN books b ON br.book_id = b.id
    WHERE br.user_id = ?
    GROUP BY b.category
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$cat_result = $stmt->get_result();
$categories = [];
$cat_counts = [];
while ($row = $cat_result->fetch_assoc()) {
    $categories[] = $row['category'];
    $cat_counts[] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Library System</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <a href="../index.php">
                <i class="fas fa-book-reader"></i>
                Library System
            </a>
        </div>
        <div class="navbar-menu">
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="../logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>
    <div class="container">
        <div class="dashboard-header enhanced-header">
            <h2>Welcome back, <span class="username"><?= htmlspecialchars($user['name']) ?></span>!</h2>
            <p class="dashboard-sub">Here's a quick overview of your library activity.</p>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="message success">
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error">
                <?= $_SESSION['error'] ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-cards">
            <div class="card dashboard-card">
                <h3>Book Return Status</h3>
                <canvas id="returnChart" class="dashboard-chart" width="180" height="180"></canvas>
            </div>
            <div class="card dashboard-card">
                <h3>Books by Category</h3>
                <canvas id="categoryChart" class="dashboard-chart" width="180" height="180"></canvas>
            </div>
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Books Borrowed</h3>
                <div class="number"><?= $stats['total_borrowed'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Currently Borrowed</h3>
                <div class="number"><?= $stats['currently_borrowed'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Books Returned</h3>
                <div class="number"><?= $stats['returned'] ?></div>
            </div>
        </div>

        <h2>My Borrowed Books</h2>
        
        <?php if ($borrowed_books->num_rows == 0): ?>
            <div class="empty-state">
                <p>You haven't borrowed any books yet.</p>
                <a href="../index.php" class="btn btn-primary">Browse Books</a>
            </div>
        <?php else: ?>
            <div class="books-grid">
                <?php while ($book = $borrowed_books->fetch_assoc()): ?>
                    <div class="book-card">
                        <?php if (!empty($book['imglink'])): ?>
                            <img src="<?= htmlspecialchars($book['imglink']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-image">
                        <?php else: ?>
                            <img src="../images/no-image.jpg" alt="No image" class="book-image">
                        <?php endif; ?>
                        <h3><?= htmlspecialchars($book['title']) ?></h3>
                        <p>By <?= htmlspecialchars($book['author']) ?></p>
                        <div class="dates">
                            <p>Borrowed on: <?= date('F j, Y', strtotime($book['borrow_date'])) ?></p>
                            <p>Return by: <?= date('F j, Y', strtotime($book['return_date'])) ?></p>
                        </div>
                        <span class="status <?= $book['status'] ?>"><?= ucfirst($book['status']) ?></span>
                        
                        <?php if ($book['status'] == 'borrowed'): ?>
                            <form method="POST">
                                <input type="hidden" name="return_book" value="1">
                                <input type="hidden" name="borrow_id" value="<?= $book['id'] ?>">
                                <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                                <button type="submit" class="btn btn-success">Return Book</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
    <script>
    // Chart 1: Book Return Status
    const returnChart = new Chart(document.getElementById('returnChart'), {
        type: 'doughnut',
        data: {
            labels: ['To Return', 'Returned'],
            datasets: [{
                data: [<?= $books_to_return ?>, <?= $books_returned ?>],
                backgroundColor: ['#f6ad55', '#48bb78'],
                borderWidth: 2
            }]
        },
        options: {
            plugins: {
                legend: { display: true, position: 'bottom' }
            }
        }
    });
    // Chart 2: Categories
    const categoryChart = new Chart(document.getElementById('categoryChart'), {
        type: 'pie',
        data: {
            labels: <?= json_encode($categories) ?>,
            datasets: [{
                data: <?= json_encode($cat_counts) ?>,
                backgroundColor: [
                    '#3498db', '#f6ad55', '#48bb78', '#e53e3e', '#9b59b6', '#2ecc71', '#f39c12', '#1abc9c'
                ],
                borderWidth: 2
            }]
        },
        options: {
            plugins: {
                legend: { display: true, position: 'bottom' }
            }
        }
    });
    </script>
</body>
</html> 