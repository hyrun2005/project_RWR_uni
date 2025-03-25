<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

include '../db/db_connect.php';

$user_id = $_SESSION["user"]["id"];

// Fetch pending tasks with category name
$pending_tasks = $conn->query("
    SELECT tasks.*, categories.name AS category_name 
    FROM tasks 
    LEFT JOIN categories ON tasks.category_id = categories.id 
    WHERE tasks.user_id = $user_id AND tasks.status = 'pending'");

// Fetch completed tasks with category name
$completed_tasks = $conn->query("
    SELECT tasks.*, categories.name AS category_name 
    FROM tasks 
    LEFT JOIN categories ON tasks.category_id = categories.id 
    WHERE tasks.user_id = $user_id AND tasks.status = 'completed'");

// Fetch categories
$categories = $conn->query("SELECT * FROM categories");
$username = $_SESSION["user"]["username"] ?? 'User';

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <title>Dashboard</title>
    <link rel="stylesheet" href="../styles/dashboard.css">
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
<hr>
<h2>Task Manager Dashboard</h2>

<!-- Add Task Form -->
<div class="form-container">
    <h3>Add a New Task</h3>
    <form action="../tasks/add_task.php" method="post">
        <div class="form-group">
            <label for="title">Task Title:</label>
            <input type="text" name="title" required>
        </div>
        <div class="form-group">
            <label for="description" class="desc-label">Description</label>
            <textarea id="description" name="description"></textarea>
        </div>
        <div class="form-group">
            <label for="category">Category:</label>
            <select name="category_id" required>
                <?php while ($row = $categories->fetch_assoc()): ?>
                    <option value="<?= $row['id']; ?>"><?= htmlspecialchars($row['name']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit">Add Task</button>
    </form>
</div>

<!-- Task Display -->
<div class="task-container">
    <div class="column-pending">
        <h3>Pending Tasks</h3>
        <?php while ($task = $pending_tasks->fetch_assoc()): ?>
            <div class="title_category">
                <p class="task-title"><?= htmlspecialchars($task["title"]);?></p>
                <span><?= htmlspecialchars($task["category_name"]); ?></span> 
            </div>
            <p class="task-description"><?= htmlspecialchars($task["description"]); ?></p>
            <div class="buttons_cmplt_del">
                <form action="../tasks/update_task.php" method="post">
                    <input type="hidden" name="task_id" value="<?= $task["id"]; ?>">
                    <button type="submit">Mark as Completed</button>
                </form>
                <form action="../tasks/delete_task.php" method="post">
                    <input type="hidden" name="task_id" value="<?= $task["id"]; ?>">
                    <button type="submit" class="del_btn" style="background-color: red;">Delete</button>
                </form>
            </div>
            <hr>
        <?php endwhile; ?>
    </div>

    <div class="column-completed">
        <h3>Completed Tasks</h3>
        <?php while ($task = $completed_tasks->fetch_assoc()): ?>
            <div class="title_category">
                <p class="task-title">✅<?= htmlspecialchars($task["title"]);?></p>
                <span><?= htmlspecialchars($task["category_name"]); ?></span> 
            </div>
            <p class="task-description"><?= htmlspecialchars($task["description"]); ?></p>
            <form action="../tasks/delete_task.php" method="post">
                <input type="hidden" name="task_id" value="<?= $task["id"]; ?>">
                <button type="submit" class="del_btn" style="background-color: red;">Delete</button>
            </form>
            <hr>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
