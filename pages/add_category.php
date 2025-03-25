<?php
include '../db/db_connect.php';
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = trim($_POST["category_name"]);

    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $category_name);

        if ($stmt->execute()) {
            $message = "Category added successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Category name cannot be empty!";
    }
}

$username = $_SESSION["user"]["username"] ?? 'User';
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link rel="stylesheet" href="../styles/add_category.css">
</head>
<body>
    
<!-- Navigation Menu -->
<div class="navbar">
    <a href="../index.html">Main Page</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="add_category.php">Add Category</a>
    <a href="logout.php">Logout</a>
    <span class="divider"></span> 
    <a class="username">Welcome, <?= htmlspecialchars($username); ?> 👋</a>
</div>

<!-- Add Category Form -->
<div class="form-container">
    <h2>Add New Category</h2>

    <?php if (!empty($message)): ?>
        <p class="message"><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form action="add_category.php" method="post">
        <input type="text" name="category_name" placeholder="Enter category name" required>
        <button type="submit">Add Category</button>
    </form>

    <a class="back-link" href="dashboard.php">⬅ Back to Dashboard</a>
</div>

</body>
</html>
