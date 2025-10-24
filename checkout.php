<?php
session_start();
require_once 'connect.php';
require_once 'products.php';
require_once 'cart_lib.php';

if (empty($_SESSION['cart'])) { header('Location: cart.php'); exit; }
if (!isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }

$uid = (int)$_SESSION['user_id'];
$u = ['fullname'=>'','phone'=>'','address'=>''];
if ($stmt = $conn->prepare("SELECT fullname, phone, address FROM users WHERE id=?")){
  $stmt->bind_param("i",$uid); $stmt->execute(); $stmt->bind_result($u['fullname'],$u['phone'],$u['address']); $stmt->fetch(); $stmt->close();
}

$placed=false; $orderId=0;
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $name = trim($_POST['shipping_name']);
  $phone = trim($_POST['shipping_phone']);
  $addr = trim($_POST['shipping_address']);
  $total = cart_total($products);
  if ($name && $phone && $addr && $total>0){
    $ins=$conn->prepare("INSERT INTO orders (user_id,total,shipping_name,shipping_phone,shipping_address) VALUES (?,?,?,?,?)");
    $ins->bind_param("idsss",$uid,$total,$name,$phone,$addr);
    if($ins->execute()){
      $orderId=$ins->insert_id;
      foreach($_SESSION['cart'] as $id=>$qty){ if(!isset($products[$id])) continue; $p=$products[$id];
        $oi=$conn->prepare("INSERT INTO order_items (order_id,product_id,product_name,price,qty) VALUES (?,?,?,?,?)");
        $oi->bind_param("iisdi",$orderId,$id,$p['name'],$p['price'],$qty); $oi->execute(); $oi->close();
      }
      cart_clear(); $placed=true;
    }
    $ins->close();
  } else $error="Please complete all shipping fields.";
}
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><title>HABI — Checkout</title><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{margin:0;font-family:Inter,system-ui,Arial}
.top{display:flex;align-items:center;gap:16px;padding:12px 18px;border-bottom:1px solid #e5e7eb;background:#fff}
.brand{font-weight:900;color:#ee4d2d}
.wrap{max-width:900px;margin:16px auto;padding:0 16px;display:grid;grid-template-columns:1fr 320px;gap:20px}
.card{border:1px solid #e5e7eb;border-radius:14px;background:#fff;padding:14px}
.label{font-size:12px;color:#666}
input,textarea{width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:10px;margin-top:6px}
.btn{background:#16a34a;color:#fff;border:none;padding:12px;border-radius:10px;font-weight:800;width:100%}
.line{display:flex;justify-content:space-between;margin:6px 0}.total{font-weight:800}
</style></head><body>
<header class="top"><div class="brand">HABI</div><a href="cart.php">← Cart</a></header>
<div class="wrap">
  <div class="card">
    <h3>Shipping Details</h3>
    <?php if(!empty($error)) echo "<p style='color:#ef4444'>$error</p>"; ?>
    <?php if($placed): ?>
      <p>✅ Order placed! Your Order ID is <strong>#<?php echo $orderId; ?></strong>.</p>
      <p><a href="shop.php">Continue shopping</a></p>
    <?php else: ?>
      <form method="post">
        <label class="label">Full Name</label><input name="shipping_name" value="<?php echo htmlspecialchars($u['fullname']); ?>" required>
        <label class="label">Phone</label><input name="shipping_phone" value="<?php echo htmlspecialchars($u['phone']); ?>" required>
        <label class="label">Address</label><textarea name="shipping_address" rows="4" required><?php echo htmlspecialchars($u['address']); ?></textarea>
        <button class="btn" type="submit" style="margin-top:10px">Place Order</button>
      </form>
    <?php endif; ?>
  </div>
  <div class="card">
    <h3>Order Summary</h3>
    <?php $t=0; foreach($_SESSION['cart'] as $id=>$q): if(!isset($products[$id])) continue; $p=$products[$id]; $sub=$p['price']*$q; $t+=$sub; ?>
      <div class="line"><span><?php echo htmlspecialchars($p['name']); ?> × <?php echo (int)$q; ?></span><span>₱<?php echo number_format($sub,2); ?></span></div>
    <?php endforeach; ?>
    <hr><div class="line total"><span>Total</span><span>₱<?php echo number_format($t,2); ?></span></div>
  </div>
</div>
</body></html>
