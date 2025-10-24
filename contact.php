<?php session_start(); require_once 'cart_lib.php'; ?>
<!doctype html><html><head><meta charset="utf-8"><title>HABI — Contact</title>
<style>body{margin:0;font-family:Inter,system-ui,Arial}.top{display:flex;gap:16px;padding:12px 18px;border-bottom:1px solid #e5e7eb}.brand{font-weight:900;color:#ee4d2d}.wrap{max-width:700px;margin:16px auto;padding:0 16px}input,textarea{width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:10px;margin:6px 0}</style>
</head><body>
<header class="top"><div class="brand">HABI</div><a href="homepage.php">Home</a><a href="shop.php">Shop</a><a href="about.php">About</a><a href="cart.php">Cart (<?php echo cart_count(); ?>)</a></header>
<div class="wrap">
  <h2>Contact Us</h2>
  <form onsubmit="alert('Demo only 🙂'); return false;">
    <input placeholder="Your Name" required>
    <input type="email" placeholder="Email" required>
    <textarea rows="5" placeholder="Message" required></textarea>
    <button style="padding:10px 14px;border-radius:10px;border:none;background:#ee4d2d;color:#fff;font-weight:800">Send</button>
  </form>
</div>
</body></html>
