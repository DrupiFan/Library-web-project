<?php
session_start();
include '../includes/db.php';

if ($_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Library System</title>
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            <a href="books.php" class="nav-link">
                <i class="fas fa-book"></i> Manage Books
            </a>
            <a href="../logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>
    <div class="container">
        <div class="dashboard-header">
            <h2>Admin Dashboard</h2>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <h3>Quick Actions</h3>
                <a href="books.php?action=add">Add New Book</a>
                <a href="books.php">View All Books</a>
                <a href="create_admin.php">Create Admin Account</a>
            </div>
            
            <div class="card">
                <h3>Borrowing Statistics</h3>
                <canvas id="borrowChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    fetch("borrow_data.php")
        .then(res => res.json())
        .then(data => {
            new Chart(document.getElementById("borrowChart"), {
                type: 'bar',
                data: {
                    labels: data.categories,
                    datasets: [{
                        label: "Books Borrowed",
                        data: data.counts,
                        backgroundColor: "skyblue"
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>