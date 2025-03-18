<?php
session_start();

$host = "localhost";
$dbname = "food_ordering"; 
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error_message = "Email dan password harus diisi!";
    } else {

        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; 
          
            if (!isset($_SESSION['user_id'])) {
                die("Session tidak tersimpan!");
            }

            header("Location: index.php");
            exit();
        } else {
            $error_message = "Login gagal! Username atau password salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link rel="stylesheet" href="stL.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
  <style>
    .register, .error-message { text-align: center; margin-top: 15px; }
    .error-message { color: red; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="title"><span>Form Login</span></div>
    <form action="" method="POST">
      <?php if (!empty($error_message)): ?>
        <p class="error-message"><?php echo $error_message; ?></p>
      <?php endif; ?>
      <div class="row">
          <i class="fas fa-user"></i>
          <input type="text" name="email" placeholder="Username" required />
      </div>
      <div class="row">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" placeholder="Password" required />
      </div>
      <div class="pass"><a href="forgot.php">Lupa password?</a></div>
      <div class="row button">
          <input type="submit" name="login" value="Login" />
      </div>
      <div class="register">Tidak punya akun? silahkan buat akun di <a href="register.php"> Register</a></div>
    </form>
  </div>
</body>
</html>
