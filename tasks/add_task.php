<?php
include '../db/db_connect.php';
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user"]["id"];
    $title = trim($_POST["title"]);
    $description = trim($_POST["description"]);
    $category_id = $_POST["category_id"];

    if (!empty($title) && !empty($description)) {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, category_id, title, description, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iiss", $user_id, $category_id, $title, $description);

        if ($stmt->execute()) {
            header("Location: ../pages/dashboard.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$conn->close();
?>
