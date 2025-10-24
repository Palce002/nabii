<?php
session_start();
require_once 'connect.php';
require_once 'products.php';  // meron ka na nito
require_once 'cart_lib.php';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add'])) {
  cart_add((int)$_POST['id'], (int)($_POST['qty'] ?? 1));
  header('Location: shop.php'); exit;
}
$cartItems = cart_count();
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><title>HABI — Shop</title><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{margin:0;font-family:Inter,system-ui,Arial}
.top{display:flex;align-items:center;gap:16px;padding:12px 18px;border-bottom:1px solid #e5e7eb;background:#fff}
.brand{font-weight:900;color:#ee4d2d}
.right{margin-left:auto}
.badge{background:#ee4d2d;color:#fff;border-radius:999px;padding:2px 7px;font-size:12px;font-weight:800}
.wrap{max-width:1100px;margin:16px auto;padding:0 16px}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.card{border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;background:#fff}
.card img{width:100%;height:180px;object-fit:cover}
.p{padding:12px}.price{font-weight:800}
.add{margin-top:8px;background:#ee4d2d;color:#fff;border:none;padding:8px 12px;border-radius:10px;font-weight:800}
</style></head><body>
<header class="top">
  <div class="brand">HABI</div>
  <nav><a href="homepage.php">Home</a> • <a href="shop.php">Shop</a> • <a href="about.php">About</a> • <a href="contact.php">Contact</a></nav>
  <div class="right"><a href="cart.php">🛒 Cart <?php if($cartItems): ?><span class="badge"><?php echo $cartItems; ?></span><?php endif; ?></a></div>
</header>
<div class="wrap">
  <div class="grid">
    <?php foreach($products as $p): ?>
      <div class="card">
        <a href="product.php?id=<?php echo $p['id']; ?>"><img src="<?php echo htmlspecialchars($p['img']); ?>" alt=""></a>
        <div class="p">
          <div><?php echo htmlspecialchars($p['name']); ?></div>
          <div class="price">₱<?php echo number_format($p['price'],2); ?></div>
          <form method="post">
            <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
            <input type="number" name="qty" value="1" min="1" style="width:60px">
            <button class="add" name="add">Add to Cart</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body></html>
