<?php
session_start();
include 'includes/db.php';
$books = $conn->query("SELECT * FROM books ORDER BY title");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library System</title>
    <link rel="stylesheet" type="text/css" href="css/common.css">
    <link rel="stylesheet" type="text/css" href="css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <a href="index.php">
                <i class="fas fa-book-reader"></i>
                Library System
            </a>
        </div>
        <div class="navbar-menu">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= $_SESSION['role'] == 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php' ?>" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            <?php else: ?>
                <a href="login.php" class="nav-link">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
                <a href="register.php" class="nav-link">
                    <i class="fas fa-user-plus"></i> Register
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-section modern-welcome">
            <div class="welcome-image">
                <img src="https://www.svgrepo.com/show/324102/online-library-digital-book-online-learning-laptop-ebook-reading.svg" alt="Welcome to Library" />
            </div>
            <div class="welcome-content">
                <h2>Welcome to Online Library System</h2>
                <p>Discover and borrow from our extensive collection of books. From fiction to non-fiction, we have something for everyone.<br><br>
                <span style="font-size:1.1rem;color:#3498db;font-weight:500;">Start your reading journey today!</span></p>
            </div>
        </div>
        
        <h2>Available Books</h2>
        <div class="books-grid">
            <?php while ($book = $books->fetch_assoc()): ?>
                <div class="book-card">
                    <?php if (!empty($book['imglink'])): ?>
                        <img src="<?= htmlspecialchars($book['imglink']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-image">
                    <?php else: ?>
                        <img src="images/no-image.jpg" alt="No image" class="book-image">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <p>By <?= htmlspecialchars($book['author']) ?></p>
                    <span class="category"><?= htmlspecialchars($book['category']) ?></span>
                    <p class="stock">Available: <?= htmlspecialchars($book['stock']) ?></p>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'user' && $book['stock'] > 0): ?>
                        <form method="POST" action="borrow.php">
                            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                            <button type="submit" class="btn btn-primary">Borrow Book</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>