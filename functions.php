<?php
require 'db/db_connect.php';

function getTasks($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, title, description, status FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = [];

    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }

    $stmt->close();
    return $tasks;
}
?>
