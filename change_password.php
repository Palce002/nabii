<?php
session_start();
require_once 'connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$msg = $err = null;
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $cur = $_POST['current_password'] ?? '';
  $n1  = $_POST['new_password'] ?? '';
  $n2  = $_POST['confirm_password'] ?? '';

  if ($n1 !== $n2) { $err = "New passwords do not match."; }
  if (!$err) {
    $st = $conn->prepare("SELECT password FROM users WHERE id=?");
    $st->bind_param("i", $_SESSION['user_id']);
    $st->execute();
    $row = $st->get_result()->fetch_assoc();

    if (!$row || !password_verify($cur, $row['password'])) {
      $err = "Current password is incorrect.";
    } else {
      $hash = password_hash($n1, PASSWORD_DEFAULT);
      $upd  = $conn->prepare("UPDATE users SET password=? WHERE id=?");
      $upd->bind_param("si", $hash, $_SESSION['user_id']);
      $upd->execute();
      $msg = "Password updated.";
    }
  }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Change Password</title></head>
<body>
<div style="max-width:600px;margin:30px auto;font-family:Arial;">
  <h2>Change Password</h2>
  <?php if($err) echo "<p style='color:#c33'>$err</p>"; ?>
  <?php if($msg) echo "<p style='color:green'>$msg</p>"; ?>
  <form method="post">
    <label>Current Password</label><br><input type="password" name="current_password" required><br><br>
    <label>New Password</label><br><input type="password" name="new_password" required><br><br>
    <label>Confirm New Password</label><br><input type="password" name="confirm_password" required><br><br>
    <button type="submit">Update Password</button>
    <a href="account.php">Cancel</a>
  </form>
</div>
</body></html>
