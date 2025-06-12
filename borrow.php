<?php
session_start();
include 'includes/db.php';

// Check if user is logged in and is a regular user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    $user_id = $_SESSION['user_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Check if book is available
        $stmt = $conn->prepare("SELECT stock FROM books WHERE id = ? AND stock > 0");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            throw new Exception("Book is not available for borrowing.");
        }
        
        // Check if user already has this book borrowed
        $stmt = $conn->prepare("SELECT id FROM borrow_records WHERE user_id = ? AND book_id = ? AND status = 'borrowed'");
        $stmt->bind_param("ii", $user_id, $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            throw new Exception("You have already borrowed this book.");
        }
        
        // Create borrow record
        $borrow_date = date('Y-m-d');
        $return_date = date('Y-m-d', strtotime('+14 days')); // 2 weeks borrowing period
        
        $stmt = $conn->prepare("INSERT INTO borrow_records (user_id, book_id, borrow_date, return_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $book_id, $borrow_date, $return_date);
        $stmt->execute();
        
        // Update book stock
        $stmt = $conn->prepare("UPDATE books SET stock = stock - 1 WHERE id = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success'] = "Book borrowed successfully! Please return by " . $return_date;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
    }
    
    header("Location: index.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?> 