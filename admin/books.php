<?php
session_start();
include '../includes/db.php';

if ($_SESSION["role"] != "admin") {
    header("Location: ../login.php");
    exit();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $title = $_POST['title'];
                $author = $_POST['author'];
                $category = $_POST['category'];
                $stock = $_POST['stock'];
                $imglink = $_POST['imglink'];
                
                $stmt = $conn->prepare("INSERT INTO books (title, author, category, stock, imglink) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssis", $title, $author, $category, $stock, $imglink);
                $stmt->execute();
                break;
                
            case 'edit':
                $id = $_POST['id'];
                $title = $_POST['title'];
                $author = $_POST['author'];
                $category = $_POST['category'];
                $stock = $_POST['stock'];
                $imglink = $_POST['imglink'];
                
                $stmt = $conn->prepare("UPDATE books SET title=?, author=?, category=?, stock=?, imglink=? WHERE id=?");
                $stmt->bind_param("sssisi", $title, $author, $category, $stock, $imglink, $id);
                $stmt->execute();
                break;
                
            case 'delete':
                $id = $_POST['id'];
                $stmt = $conn->prepare("DELETE FROM books WHERE id=?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                break;
        }
    }
}

// Get all books
$books = $conn->query("SELECT * FROM books ORDER BY title");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Library System</title>
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
            <h2>Manage Books</h2>
        </div>

        <?php if (isset($_GET['action']) && $_GET['action'] == 'add'): ?>
            <div class="form-container">
                <h3>Add New Book</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Title:</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Author:</label>
                        <input type="text" name="author" required>
                    </div>
                    <div class="form-group">
                        <label>Category:</label>
                        <select name="category" required>
                            <option value="Fiction">Fiction</option>
                            <option value="Non-Fiction">Non-Fiction</option>
                            <option value="Science">Science</option>
                            <option value="History">History</option>
                            <option value="Biography">Biography</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Stock:</label>
                        <input type="number" name="stock" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Image Link:</label>
                        <input type="url" name="imglink" placeholder="https://example.com/image.jpg">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Book</button>
                </form>
            </div>
        <?php endif; ?>

        <table>
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Actions</th>
            </tr>
            <?php while ($book = $books->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if (!empty($book['imglink'])): ?>
                            <img src="<?= htmlspecialchars($book['imglink']) ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-image">
                        <?php else: ?>
                            <img src="../images/no-image.jpg" alt="No image" class="book-image">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($book['title']) ?></td>
                    <td><?= htmlspecialchars($book['author']) ?></td>
                    <td><?= htmlspecialchars($book['category']) ?></td>
                    <td><?= htmlspecialchars($book['stock']) ?></td>
                    <td>
                        <form method="POST" class="action-form">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $book['id'] ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this book?')">Delete</button>
                        </form>
                        <button class="btn btn-warning" onclick="editBook(<?= htmlspecialchars(json_encode($book)) ?>)">Edit</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <script>
    function editBook(book) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="${book.id}">
            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="title" value="${book.title}" required>
            </div>
            <div class="form-group">
                <label>Author:</label>
                <input type="text" name="author" value="${book.author}" required>
            </div>
            <div class="form-group">
                <label>Category:</label>
                <select name="category" required>
                    <option value="Fiction" ${book.category === 'Fiction' ? 'selected' : ''}>Fiction</option>
                    <option value="Non-Fiction" ${book.category === 'Non-Fiction' ? 'selected' : ''}>Non-Fiction</option>
                    <option value="Science" ${book.category === 'Science' ? 'selected' : ''}>Science</option>
                    <option value="History" ${book.category === 'History' ? 'selected' : ''}>History</option>
                    <option value="Biography" ${book.category === 'Biography' ? 'selected' : ''}>Biography</option>
                </select>
            </div>
            <div class="form-group">
                <label>Stock:</label>
                <input type="number" name="stock" value="${book.stock}" min="0" required>
            </div>
            <div class="form-group">
                <label>Image Link:</label>
                <input type="url" name="imglink" value="${book.imglink || ''}" placeholder="https://example.com/image.jpg">
            </div>
            <button type="submit" class="btn btn-primary">Update Book</button>
        `;
        
        const container = document.createElement('div');
        container.className = 'form-container';
        container.appendChild(form);
        
        // Remove any existing form container
        const existingContainer = document.querySelector('.form-container');
        if (existingContainer) {
            existingContainer.remove();
        }
        
        // Insert the new form container after the header
        const header = document.querySelector('.dashboard-header');
        header.after(container);
    }
    </script>
</body>
</html>