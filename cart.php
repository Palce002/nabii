<?php
session_start();
require_once 'connect.php';
require_once 'products.php';
require_once 'cart_lib.php';

if ($_SERVER['REQUEST_METHOD']==='POST'){
  $action = $_POST['action'] ?? '';
  $id = (int)($_POST['id'] ?? 0);
  $qty = (int)($_POST['qty'] ?? 1);
  if ($action==='set' && $id) cart_set($id,$qty);
  if ($action==='remove' && $id) cart_remove($id);
  if ($action==='clear') cart_clear();
  header('Location: cart.php'); exit;
}
$total = cart_total($products);
?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><title>HABI — Cart</title><meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{margin:0;font-family:Inter,system-ui,Arial}
.top{display:flex;align-items:center;gap:16px;padding:12px 18px;border-bottom:1px solid #e5e7eb;background:#fff}
.brand{font-weight:900;color:#ee4d2d}
.wrap{max-width:1000px;margin:16px auto;padding:0 16px}
.row{display:grid;grid-template-columns:1fr 120px 120px 120px;gap:10px;padding:12px;border-bottom:1px solid #eee}
.head{font-weight:800;background:#fafafa}
.qty{width:72px}.right{text-align:right}
.clear{background:#f3f4f6;border:1px solid #e5e7eb;padding:10px 14px;border-radius:10px}
.checkout{background:#16a34a;color:#fff;border:none;padding:10px 14px;border-radius:10px;font-weight:800}
</style></head><body>
<header class="top">
  <div class="brand">HABI</div>
  <a href="shop.php">Shop</a>
</header>
<div class="wrap">
  <div class="row head"><div>Item</div><div class="right">Price</div><div>Qty</div><div class="right">Subtotal</div></div>
  <?php if(empty($_SESSION['cart'])): ?>
    <p>Your cart is empty. <a href="shop.php">Continue shopping</a>.</p>
  <?php else: foreach($_SESSION['cart'] as $id=>$q): if(!isset($products[$id])) continue; $p=$products[$id]; ?>
    <div class="row">
      <div><?php echo htmlspecialchars($p['name']); ?></div>
      <div class="right">₱<?php echo number_format($p['price'],2); ?></div>
      <div>
        <form method="post" style="display:flex;gap:8px">
          <input type="hidden" name="action" value="set">
          <input type="hidden" name="id" value="<?php echo $id; ?>">
          <input class="qty" type="number" name="qty" value="<?php echo (int)$q; ?>" min="1">
          <button>Update</button>
        </form>
      </div>
      <div class="right">
        ₱<?php echo number_format($p['price']*$q,2); ?>
        <form method="post" style="display:inline">
          <input type="hidden" name="action" value="remove">
          <input type="hidden" name="id" value="<?php echo $id; ?>">
          <button style="margin-left:8px">Remove</button>
        </form>
      </div>
    </div>
  <?php endforeach; endif; ?>

  <div style="display:flex;justify-content:space-between;align-items:center;margin-top:14px">
    <form method="post"><input type="hidden" name="action" value="clear"><button class="clear" type="submit">Clear Cart</button></form>
    <div style="font-weight:800;font-size:18px">Total: ₱<?php echo number_format($total,2); ?></div>
  </div>

  <div style="margin-top:12px;text-align:right">
    <a class="checkout" href="checkout.php">Proceed to Checkout</a>
  </div>
</div>
</body></html>
