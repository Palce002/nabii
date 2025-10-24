<?php
session_start();
require_once 'connect.php';


if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['lock_time'])) {
    $_SESSION['lock_time'] = 0;
}

$now = time();
$cooldown_seconds = 60; 
$max_attempts = 3;


if ($_SESSION['lock_time'] && $now < $_SESSION['lock_time']) {
    $remaining = $_SESSION['lock_time'] - $now;
    $error = "Account locked. Please wait {$remaining} seconds before trying again.";
} elseif ($_SESSION['lock_time'] && $now >= $_SESSION['lock_time']) {
    
    $_SESSION['login_attempts'] = 0;
    $_SESSION['lock_time'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    
    if ($_SESSION['lock_time'] && $now < $_SESSION['lock_time']) {
        $remaining = $_SESSION['lock_time'] - $now;
        $error = "Account locked. Wait {$remaining} seconds.";
    } else {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        
        if (empty($email) || empty($password)) {
            $error = "Enter email and password.";
        } else {
            $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $fullname, $hash);
                $stmt->fetch();
                if (password_verify($password, $hash)) {
                    
                    $_SESSION['user_id'] = $id;
                    $_SESSION['user_name'] = $fullname;
                    $_SESSION['login_attempts'] = 0;
                    $_SESSION['lock_time'] = 0;
                    header("Location: homepage.php");
                    exit;
                } else {
                    
                    $_SESSION['login_attempts'] += 1;
                    if ($_SESSION['login_attempts'] >= $max_attempts) {
                        $_SESSION['lock_time'] = time() + $cooldown_seconds;
                        $error = "Too many failed attempts. Locked for {$cooldown_seconds} seconds.";
                    } else {
                        $left = $max_attempts - $_SESSION['login_attempts'];
                        $error = "Wrong credentials. {$left} attempts remaining.";
                    }
                }
            } else {
                
                $_SESSION['login_attempts'] += 1;
                if ($_SESSION['login_attempts'] >= $max_attempts) {
                    $_SESSION['lock_time'] = time() + $cooldown_seconds;
                    $error = "Too many failed attempts. Locked for {$cooldown_seconds} seconds.";
                } else {
                    $left = $max_attempts - $_SESSION['login_attempts'];
                    $error = "Wrong credentials. {$left} attempts remaining.";
                }
            }
            $stmt->close();
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <link rel="stylesheet" href="style.css"> 
<body>
<div class="container">
  <h2 class="form-title">Sign in</h2>
  <?php
    if (!empty($_SESSION['success'])) { echo "<p style='color:green;'>".$_SESSION['success']."</p>"; unset($_SESSION['success']); }
    if (!empty($error)) echo "<p style='color:red;'>$error</p>";
  ?>
  <form method="post" action="">
    <div class="input-group">
      <input type="email" name="email" placeholder=" " required>
      <label>Email</label>
    </div>
    <div class="input-group">
      <input type="password" name="password" placeholder=" " required>
      <label>Password</label>
    </div>
    <div class="recover">
      <a href="forgot.php">Forgot password?</a>
    </div>
    <button class="btn" name="login">Sign In</button>
  </form>
  <p class="or">Or</p>
  <div class="links">
    <a href="register.php">Create account</a>
  </div>
</div>
</body>
</html>
