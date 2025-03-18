<?php
session_start();
include "db.php"; // Koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT); 

    // mengecek apakah username atau email sudah ada
    $checkUser = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $checkUser->bind_param("ss", $username, $email);
    $checkUser->execute();
    $checkUser->store_result();

    if ($checkUser->num_rows > 0) {
        echo "Username atau Email sudah digunakan!";
        $checkUser->close(); // untuk memastikan statement ini ditutup sebelum keluar
    } else {
        // menyimpan data ke database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        
        if ($stmt) { // mengecek apakah statement berhasil dibuat
            $stmt->bind_param("sss", $username, $email, $password);
            if ($stmt->execute()) {
                echo "Registrasi berhasil! Silakan <a href='login.php'>Login</a>";
            } else {
                echo "Terjadi kesalahan saat menyimpan data.";
            }
            $stmt->close(); // menutup statement setelah selesai
        } else {
            echo "Terjadi kesalahan dalam query SQL.";
        }
    }
    $conn->close(); // menutup koneksi setelah selesai
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
  <link rel="stylesheet" href="stL.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
</head>
<body>
  <div class="wrapper">
    <div class="title"><span>Form Register</span></div>
    <form action="" method="POST">
      <?php if (!empty($error_message)): ?>
        <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
      <?php endif; ?>

      <div class="row">
        <i class="fas fa-user"></i>
        <input type="text" name="username" placeholder="Username" required autocomplete="off" />
      </div>
      <div class="row">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" placeholder="Email" required autocomplete="off" />
      </div>
      <div class="row">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" placeholder="Password" required autocomplete="off" />
      </div>
      <div class="row button">
        <input type="submit" name="register" value="Register" />
      </div>
      <div class="signup-link">Sudah ada akun? kembali ke halaman <a href="login.php">Login</a></div>
    </form>
  </div>
</body>
</html>
