<?php
session_start();
include "db.php";

$error_message = "";
$success_message = "";
$show_password_field = false;
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["forgot"])) {
        // untuk mengecek email
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        if (empty($email)) {
            $error_message = "Harap masukkan email Anda!";
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->close();
                $success_message = "Email ditemukan! Silakan masukkan password baru.";
                $show_password_field = true;
            } else {
                $error_message = "Email tidak ditemukan!";
          }
        }
    } elseif (isset($_POST["reset_password"])) {
        // untuk update password
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $new_password = $_POST["new_password"] ?? "";

        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed_password, $email);

            if ($stmt->execute()) {
                $success_message = "Password berhasil diperbarui! Silakan login.";
                $show_password_field = false;
            } else {
                $error_message = "Gagal memperbarui password!";
            }
            $stmt->close();
        } else {
                $error_message = "Password tidak boleh kosong!";
      }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot Password</title>
  <link rel="stylesheet" href="stL.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>
<body>
  <div class="wrapper">
  <div class="title"><span>Lupa password</span></div>
    <form action="" method="POST">
      <?php if (!empty($error_message)): ?>
        <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
      <?php elseif (!empty($success_message)): ?>
        <p style="color: green; text-align: center;"><?php echo $success_message; ?></p>
      <?php endif; ?>
        <div class="row">
          <i class="fas fa-envelope"></i>
    <input type="email" name="email" placeholder="Masukkan email anda" value="<?php echo htmlspecialchars($email); ?>" required />
        </div>
      <?php if ($show_password_field): ?>
        <div class="row">
    <i class="fas fa-lock"></i>
    <input type="password" name="new_password" placeholder="Masukan Password baru anda" required />
        </div>
        <div class="row button">
    <input type="submit" name="reset_password" value="Reset Password"/>
        </div>
      <?php else: ?>
        <div class="row button">
          <input type="submit" name="forgot" value="Reset Password"/>
        </div>
      <?php endif; ?>
        <div class="signup-link">Kembali ke halaman <a href="login.php">Login</a></div>
    </form>
  </div>
</body>
</html>
