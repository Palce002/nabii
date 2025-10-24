<?php
session_start();
require_once 'connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$userId = (int) $_SESSION['user_id'];

$u = $conn->prepare("SELECT fullname, email, phone, address, created_at FROM users WHERE id = ? LIMIT 1");
$u->bind_param("i", $userId);
$u->execute();
$user = $u->get_result()->fetch_assoc();
if (!$user) { echo "User not found."; exit; }

$o = $conn->prepare("SELECT id, total, shipping_name, shipping_phone, shipping_address, created_at
                     FROM orders WHERE user_id = ? ORDER BY id DESC");
$o->bind_param("i", $userId);
$o->execute();
$orders = $o->get_result();

function e($v){ return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Account</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body{background:#f7f7f7;font-family:Arial;margin:0;}
    .wrap{max-width:1000px;margin:30px auto;padding:0 16px;}
    .card{background:#fff;border:1px solid #eee;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,.06);margin-bottom:18px;}
    .card h2{margin:0;padding:14px 16px;border-bottom:1px solid #eee;}
    .card .body{padding:16px;}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;}
    .field{border:1px solid #eee;border-radius:8px;padding:10px;background:#fafafa;}
    .label{font-size:12px;color:#777;}
    .value{font-size:14px;}
    table{width:100%;border-collapse:collapse;}
    th,td{padding:10px;border-bottom:1px solid #eee;text-align:left;}
    .actions a{display:inline-block;margin-right:8px;}
    .btn{border:1px solid #ddd;border-radius:8px;padding:8px 10px;text-decoration:none;}
    .btn.primary{background:#111;color:#fff;border-color:#111;}
  </style>
</head>
<body>
  <div class="wrap">

    <div class="card">
      <h2>Profile</h2>
      <div class="body">
        <div class="grid">
          <div class="field"><div class="label">Full Name</div><div class="value"><?= e($user['fullname']) ?></div></div>
          <div class="field"><div class="label">Email</div><div class="value"><?= e($user['email']) ?></div></div>
          <div class="field"><div class="label">Phone</div><div class="value"><?= e($user['phone']) ?></div></div>
          <div class="field"><div class="label">Address</div><div class="value"><?= nl2br(e($user['address'])) ?></div></div>
          <div class="field"><div class="label">Member Since</div><div class="value"><?= e(date('M d, Y', strtotime($user['created_at']))) ?></div></div>
        </div>
        <div class="actions" style="margin-top:12px">
          <a class="btn primary" href="account_edit.php">Edit Info</a>
          <a class="btn" href="change_password.php">Change Password</a>
          <a class="btn" href="logout.php" style="border-color:#c33;color:#c33">Logout</a>
        </div>
      </div>
    </div>

    <div class="card">
      <h2>My Orders</h2>
      <div class="body">
        <?php if ($orders->num_rows === 0): ?>
          <p>Wala ka pang orders.</p>
        <?php else: ?>
          <table>
            <thead>
              <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Total</th>
                <th>Ship To</th>
                <th>Phone</th>
                <th>Address</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
            <?php while($row = $orders->fetch_assoc()): ?>
              <tr>
                <td>#<?= e($row['id']) ?></td>
                <td><?= e(date('M d, Y H:i', strtotime($row['created_at']))) ?></td>
                <td>₱<?= number_format((float)$row['total'], 2) ?></td>
                <td><?= e($row['shipping_name']) ?></td>
                <td><?= e($row['shipping_phone']) ?></td>
                <td><?= e($row['shipping_address']) ?></td>
                <td><a class="btn" href="order_details.php?id=<?= (int)$row['id'] ?>">View</a></td>
              </tr>
            <?php endwhile; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>

  </div>
</body>
</html>
