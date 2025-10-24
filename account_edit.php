<?php
session_start();
require_once 'connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD']==='POST') {
  $fullname = trim($_POST['fullname'] ?? '');
  $email    = trim($_POST['email'] ?? '');
  $phone    = trim($_POST['phone'] ?? '');
  $address  = trim($_POST['address'] ?? '');

  if ($fullname === '') { $err = "Full name is required."; }
  if (empty($err)) {
    $upd = $conn->prepare("UPDATE users SET fullname=?, email=?, phone=?, address=? WHERE id=?");
    $upd->bind_param("ssssi", $fullname, $email, $phone, $address, $userId);
    $upd->execute();
    $_SESSION['fullname'] = $fullname;
    header("Location: account.php?updated=1"); exit;
  }
}

$st = $conn->prepare("SELECT fullname,email,phone,address FROM users WHERE id=? LIMIT 1");
$st->bind_param("i", $userId);
$st->execute();
$user = $st->get_result()->fetch_assoc();
function e($v){ return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8'); }
?>
<!doctype html><html><head><meta charset="utf-8"><title>Edit Account</title></head>
<body>
<div style="max-width:700px;margin:30px auto;font-family:Arial;">
  <h2>Edit Account</h2>
  <?php if(!empty($err)) echo "<p style='color:#c33'>".e($err)."</p>"; ?>
  <form method="post">
    <label>Full Name</label><br><input type="text" name="fullname" value="<?= e($user['fullname']) ?>" required><br><br>
    <label>Email</label><br><input type="email" name="email" value="<?= e($user['email']) ?>"><br><br>
    <label>Phone</label><br><input type="text" name="phone" value="<?= e($user['phone']) ?>"><br><br>
    <label>Address</label><br><input type="text" name="address" value="<?= e($user['address']) ?>"><br><br>
    <button type="submit">Save</button>
    <a href="account.php">Cancel</a>
  </form>
</div>
</body></html>
