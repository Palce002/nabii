<?php
session_start();
require_once 'connect.php';
require_once 'products.php';
require_once 'cart_lib.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit;
}

$userId = (int) $_SESSION['user_id'];

if (!empty($_SESSION['fullname'])) {
  $fullName = htmlspecialchars($_SESSION['fullname'], ENT_QUOTES, 'UTF-8');
} else {

  $st = $conn->prepare("SELECT fullname FROM users WHERE id = ? LIMIT 1");
  $st->bind_param("i", $userId);
  $st->execute();
  $row = $st->get_result()->fetch_assoc();
  $fullName = htmlspecialchars($row['fullname'] ?? 'My Account', ENT_QUOTES, 'UTF-8');
  
  $_SESSION['fullname'] = $row['fullname'] ?? null;
}

$cartItems = cart_count();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>HABI | Homepage</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      margin: 0;
      font-family: "Poppins", sans-serif;
      background: #f8f8f8;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #fff;
      border-bottom: 2px solid #e5e7eb;
      padding: 10px 20px;
      position: sticky;
      top: 0;
      z-index: 50;
    }

    .brand {
      font-size: 1.6rem;
      font-weight: 900;
      color: #ee4d2d;
      letter-spacing: 1px;
      margin: 0;
    }

    nav a {
      margin: 0 12px;
      color: #333;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.2s ease;
    }

    nav a:hover {
      color: #ee4d2d;
    }

    .right {
      display: flex;
      align-items: center;
      gap: 15px;
      font-size: 14px;
    }

    .logout {
      color: #ee4d2d;
      font-weight: 600;
      text-decoration: none;
    }

    .badge {
      background: #ee4d2d;
      color: #fff;
      border-radius: 50%;
      padding: 2px 8px;
      font-size: 12px;
      font-weight: 700;
      margin-left: 4px;
    }

    .hero {
      display: grid;
      grid-template-columns: 1.2fr 0.8fr;
      align-items: center;
      max-width: 1100px;
      margin: 40px auto;
      gap: 20px;
      padding: 0 20px;
    }

    .hero-text h1 {
      font-size: 2.8rem;
      color: #111;
      margin: 0 0 10px;
    }

    .hero-text p {
      color: #555;
      font-size: 1rem;
      margin-bottom: 20px;
    }

    .cta-btn {
      background: #ee4d2d;
      color: #fff;
      border: none;
      padding: 12px 18px;
      border-radius: 8px;
      font-weight: 700;
      text-decoration: none;
      transition: background 0.2s;
    }

    .cta-btn:hover {
      background: #d63b1f;
    }

    .hero-banner {
      background: linear-gradient(135deg, #ffe5d9, #ffd6d6);
      height: 280px;
      border-radius: 16px;
      border: 1px solid #e5e7eb;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      font-weight: 800;
      color: #b91c1c;
    }

    .grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 16px;
      max-width: 1100px;
      margin: 30px auto;
      padding: 0 20px;
    }

    .card {
      background: #fff;
      border-radius: 14px;
      border: 1px solid #e5e7eb;
      overflow: hidden;
      transition: transform 0.2s;
    }

    .card:hover {
      transform: translateY(-3px);
    }

    .card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }

    .card-body {
      padding: 14px;
    }

    .card-body h3 {
      margin: 0;
      font-size: 1rem;
      color: #111;
    }

    .price {
      color: #ee4d2d;
      font-weight: 800;
      margin-top: 6px;
    }

    .footer {
      text-align: center;
      padding: 20px;
      font-size: 0.9rem;
      color: #777;
      border-top: 1px solid #e5e7eb;
      margin-top: 40px;
    }

    .right .account-link {
      color: #333;
      font-weight: 600;
      text-decoration: none;
    }
    .right .account-link:hover {
      color: #ee4d2d;
    }
  </style>
</head>
<body>
  
  <header>
    <h1 class="brand">HABI</h1>
    <nav>
      <a href="homepage.php">Home</a>
      <a href="shop.php">Shop</a>
      <a href="about.php">About</a>
      <a href="contact.php">Contact</a>
      <a href="cart.php">
        Cart<?php if($cartItems > 0) echo "<span class='badge'>$cartItems</span>"; ?>
      </a>
    </nav>
    <div class="right">
      <span>Welcome,</span>
      <a class="account-link" href="account.php"><?php echo $fullName; ?></a>
      <a class="logout" href="logout.php">Logout</a>
    </div>
  </header>

  <section class="hero">
    <div class="hero-text">
      <h1>Welcome to HABI</h1>
      <p>Your one-stop shop for simple yet stylish essentials. Browse our latest drops and enjoy seamless shopping.</p>
      <a href="shop.php" class="cta-btn">Shop Now</a>
    </div>
    <div class="hero-banner">Big Sale • Up to 50% Off</div>
  </section>

  <section class="grid">
    <?php foreach(array_slice($products,0,4) as $p): ?>
      <div class="card">
        <a href="product.php?id=<?php echo $p['id']; ?>">
          <img src="<?php echo htmlspecialchars($p['img']); ?>" alt="">
        </a>
        <div class="card-body">
          <h3><?php echo htmlspecialchars($p['name']); ?></h3>
          <div class="price">₱<?php echo number_format($p['price'], 2); ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </section>

  <footer class="footer">
    © <?php echo date("Y"); ?> HABI — All Rights Reserved.
  </footer>
</body>
</html>
