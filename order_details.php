<?php
session_start();
require_once 'connect.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId  = (int) $_SESSION['user_id'];
if ($orderId <= 0) { echo "Invalid order."; exit; }

$chk = $conn->prepare("SELECT id, total, shipping_name, shipping_phone, shipping_address, created_at
                       FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
$chk->bind_param("ii", $orderId, $userId);
$chk->execute();
$order = $chk->get_result()->fetch_assoc();
if (!$order) { echo "Order not found."; exit; }

$it = $conn->prepare("SELECT product_id, product_name, price, qty FROM order_items WHERE order_id = ?");
$it->bind_param("i", $orderId);
$it->execute();
$items = $it->get_result();

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Order #<?= e($orderId) ?></title>
  <link rel="stylesheet" href="style.css">
  <style>
    body{background:#f7f7f7;font-family:Arial;margin:0;}
    .wrap{max-width:900px;margin:30px auto;padding:0 16px;}
    .card{background:#fff;border:1px solid #eee;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,.06);margin-bottom:18px;}
    .card h2{margin:0;padding:14px 16px;border-bottom:1px solid #eee;}
    .card .body{padding:16px;}
    table{width:100%;border-collapse:collapse;}
    th,td{padding:10px;border-bottom:1px solid #eee;text-align:left;}
    .btn{border:1px solid #ddd;border-radius:8px;padding:8px 10px;text-decoration:none;}
  </style>
</head>
<body>
  <div class="wrap">
    <a class="btn" href="account.php">&larr; Back to My Account</a>

    <div class="card">
      <h2>Order #<?= e($orderId) ?></h2>
      <div class="body">
        <p><strong>Date:</strong> <?= e(date('M d, Y H:i', strtotime($order['created_at']))) ?></p>
        <p><strong>Total:</strong> ₱<?= number_format((float)$order['total'], 2) ?></p>
        <p><strong>Ship To:</strong> <?= e($order['shipping_name']) ?>, <?= e($order['shipping_phone']) ?></p>
        <p><strong>Address:</strong> <?= e($order['shipping_address']) ?></p>
      </div>
    </div>

    <div class="card">
      <h2>Items</h2>
      <div class="body">
        <table>
          <thead>
            <tr>
              <th>Product</th>
              <th>Price</th>
              <th>Qty</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php $grand = 0.0; while($itRow = $items->fetch_assoc()):
              $sub = (float)$itRow['price'] * (int)$itRow['qty']; $grand += $sub; ?>
              <tr>
                <td>#<?= e($itRow['product_id']) ?> — <?= e($itRow['product_name']) ?></td>
                <td>₱<?= number_format((float)$itRow['price'], 2) ?></td>
                <td><?= (int)$itRow['qty'] ?></td>
                <td>₱<?= number_format($sub, 2) ?></td>
              </tr>
            <?php endwhile; ?>
            <tr>
              <td colspan="3" style="text-align:right"><strong>Total:</strong></td>
              <td><strong>₱<?= number_format($grand, 2) ?></strong></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</body>
</html>
