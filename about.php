<?php session_start(); require_once 'cart_lib.php'; ?>
<!doctype html><html><head><meta charset="utf-8"><title>HABI — About</title>
<style>body{margin:0;font-family:Inter,system-ui,Arial}.top{display:flex;gap:16px;padding:12px 18px;border-bottom:1px solid #e5e7eb}.brand{font-weight:900;color:#ee4d2d}.wrap{max-width:900px;margin:16px auto;padding:0 16px}</style>
</head><body>
<header class="top"><div class="brand">HABI</div><a href="homepage.php">Home</a><a href="shop.php">Shop</a><a href="contact.php">Contact</a><a href="cart.php">Cart (<?php echo cart_count(); ?>)</a></header>
<div class="wrap"><h2>About HABI</h2><p>Simple store inspired by Wix/Shopee layout. PHP + MySQL for orders, sessions for cart.</p></div>
</body></html>
