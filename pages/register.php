<?php
include '../db/db_connect.php';

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
        $toastClass = "bg-warning";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkStmt->bind_param("ss", $username, $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $message = "Username or Email already exists!";
            $toastClass = "bg-warning";
        } else {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $message = "Account created successfully";
                $toastClass = "bg-success";
                header("Location: login.php");
                exit();
            } else {
                $message = "Error: " . $stmt->error;
                $toastClass = "bg-danger";
            }
            $stmt->close();
        }

        $checkStmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Task Manager</title>
    <link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet'>
    <link rel="stylesheet" href="../styles/register.css">
</head>
<body>

<div class="container">
    <h2>Task Manager</h2>
    <p>Create a new account</p>

    <!-- Display Error Message -->
    <?php if (!empty($message)): ?>
        <p class="error-message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <!-- Registration Form -->
    <form action="register.php" method="post" onsubmit="return validateForm()">
        <input type="text" name="username" id="username" placeholder="Enter your username" required>
        <input type="email" name="email" id="email" placeholder="Enter your email" required>
        <input type="password" name="password" id="password" placeholder="Enter your password" required>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your password" required>
        <button type="submit">Register</button>
    </form>

    <div class="links">
        <a href="login.php">Already have an account? Login</a>
    </div>
</div>

<script>
    function validateForm() {
        let password = document.getElementById("password").value;
        let confirm_password = document.getElementById("confirm_password").value;

        if (password !== confirm_password) {
            alert("Passwords do not match!");
            return false;
        }
        return true;
    }
</script>

</body>
</html>
