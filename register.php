<?php
session_start();
require_once 'connect.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    if (empty($fullname) || empty($email) || empty($phone) || empty($password) || empty($confirm)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^\+?\d{10,15}$/', $phone)) {
        $error = "Invalid phone number format. Use +63 or 09 format.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $ins = $conn->prepare("INSERT INTO users (fullname, email, phone, address, password) VALUES (?, ?, ?, ?, ?)");
            $ins->bind_param("sssss", $fullname, $email, $phone, $address, $hash);
            if ($ins->execute()) {
                $_SESSION['success'] = "Registration successful! You can now log in.";
                header("Location: index.php");
                exit;
            } else {
                $error = "Error creating account. Please try again.";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2 class="form-title">Sign Up</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    
    <form method="post" action="">
        <div class="input-group">
            <input type="text" name="fullname" placeholder=" " required>
            <label>Full Name</label>
        </div>

        <div class="input-group">
            <input type="email" name="email" placeholder=" " required>
            <label>Email</label>
        </div>

        <div class="input-group">
            <input type="text" name="phone" placeholder=" " required>
            <label>Phone Number</label>
        </div>

        <div class="input-group">
            <input type="text" name="address" placeholder=" " required>
            <label>Address</label>
        </div>

        <div class="input-group">
            <input type="password" name="password" placeholder=" " required>
            <label>Password</label>
        </div>

        <div class="input-group">
            <input type="password" name="confirm_password" placeholder=" " required>
            <label>Confirm Password</label>
        </div>

        <button type="submit" class="btn" name="register">Sign Up</button>
    </form>

    <p class="or">Or</p>
    <div class="links">
        <a href="index.php">Already have an account? Sign in</a>
    </div>
</div>
</body>
</html>
