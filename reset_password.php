<?php
session_start();
require_once 'connect.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    die('Invalid token.');
}

$stmt = $conn->prepare("SELECT id, reset_expire FROM users WHERE reset_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows !== 1) {
    die('Invalid or expired token.');
}
$stmt->bind_result($id, $reset_expire);
$stmt->fetch();

if (strtotime($reset_expire) < time()) {
    die('Token expired. Request a new reset link.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    if (empty($password) || $password !== $confirm) {
        $error = "Passwords do not match or empty.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $upd = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expire = NULL WHERE id = ?");
        $upd->bind_param("si", $hash, $id);
        if ($upd->execute()) {
            $_SESSION['success'] = "Password reset. You can now login.";
            header("Location: index.php");
            exit;
        } else {
            $error = "Error resetting password.";
        }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Reset password</title></head><body>
<div class="container">
  <h2 class="form-title">Reset Password</h2>
  <?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <form method="post" action="">
    <div class="input-group">
      <input type="password" name="password" placeholder=" " required>
      <label>New Password</label>
    </div>
    <div class="input-group">
      <input type="password" name="confirm_password" placeholder=" " required>
      <label>Confirm New Password</label>
    </div>
    <button class="btn" name="reset">Reset Password</button>
  </form>
</div>
</body></html>
