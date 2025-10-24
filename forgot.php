<?php
session_start();
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_reset'])) {
    $email = trim($_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows != 1) {
            $error = "No account with that email.";
        } else {
            $stmt->bind_result($id);
            $stmt->fetch();
           
            $token = bin2hex(random_bytes(32));
            $expire = date("Y-m-d H:i:s", time() + 3600); 

            $upd = $conn->prepare("UPDATE users SET reset_token = ?, reset_expire = ? WHERE id = ?");
            $upd->bind_param("ssi", $token, $expire, $id);
            if ($upd->execute()) {
                
                $reset_link = "https://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['REQUEST_URI']) . "/reset_password.php?token=$token";
                
                $subject = "Password reset request";
                $message = "Hi,\n\nClick this link to reset your password (valid for 1 hour):\n\n$reset_link\n\nIf you didn't request this, ignore.";
                $headers = "From: noreply@" . $_SERVER['HTTP_HOST'];

                
                if (mail($email, $subject, $message, $headers)) {
                    $_SESSION['success'] = "Reset link sent to your email.";
                } else {
                    
                    $_SESSION['success'] = "If your server is configured to send mail, you will receive a reset link. (Configure SMTP if not.)";
                }
                header("Location: index.php");
                exit;
            } else {
                $error = "Error creating reset link.";
            }
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Forgot password</title></head><body>
<div class="container">
  <h2 class="form-title">Forgot Password</h2>
  <?php if(!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
  <form method="post" action="">
    <div class="input-group">
      <input type="email" name="email" placeholder=" " required>
      <label>Email</label>
    </div>
    <button class="btn" name="send_reset">Send Reset Link</button>
  </form>
  <div class="links">
    <a href="index.php">Back to login</a>
  </div>
</div>
</body></html>
