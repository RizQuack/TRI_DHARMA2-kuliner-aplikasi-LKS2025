<?php
session_start(); 
if (!isset($_SESSION['username'])) {
    die("Error: Anda belum login. Silakan <a href='login.php'>login</a> terlebih dahulu.");
}
require 'db.php';

$username = $_SESSION['username']; 
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : []; 
$total = 0;

// menghapus item dari keranjang jika ada parameter `remove`
if (isset($_GET['remove'])) {
    $id = $_GET['remove'];
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]); // menghapus item dari session
    }
    header("Location: cart.php"); // Refresh halaman
    exit;
}
// Proses checkout
if (isset($_POST['checkout'])) {
    require 'db.php';
        if (!empty($cart)) {
            foreach ($cart as $id => $item) {
                $name = $item['name'];
                $qty = $item['qty'];
                $price = $item['price'];
                $total_price = $price * $qty;            
            // simpan pesanan ke database
                $stmt = $conn->prepare("INSERT INTO orders (username, food_name, qty, price, total_price) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                die("Error dalam query: " . $conn->error);
            }           
            $stmt->bind_param("ssiii", $username, $name, $qty, $price, $total_price);
            $stmt->execute();
        }
    }
    $_SESSION['cart'] = []; // mengosongkan keranjang setelah checkout
    $_SESSION['checkout_success'] = "Berhasil checkout! Pesanan telah tersimpan di database.";
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .Keranjang-Belanja {
            text-align: center;
        }
        body {
            background: linear-gradient(to bottom, #ADD8E6, #FFFFFF);
            background-attachment: fixed;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .logo {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 80px;
            height: auto;
        }
        .navbar {
            width: 100%;
            display: flex;
            justify-content: space-around;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.8);
        }
        .navbar a {
            text-decoration: none;
            font-size: 18px;
            font-weight: bold;
            color: black;
        }
        .main {
            width: 80%;
            max-width: 600px;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .cart-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .cart-item {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            background: #f9f9f9;
            width: 100%;
            box-sizing: border-box;
        }
        .remove-btn {
            background: red;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }
        .remove-btn:hover {
            background: darkred;
        }
        .checkout-btn {
            background: green;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .checkout-btn:hover {
            background: darkgreen;
        }
        .success-message {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<img src="assets/images/logo_kota_bogor.png" alt="Logo Kota Bogor" class="logo">
    <div class="navbar">
        <ul>
            <li><a href="index.php">Daftar Makanan</a></li>
            <li><a href="tentang-website.html">Tentang Website</a></li>
            <li><a href="cart.php">Keranjang</a></li>
            <li style="float:right;">Halo Selamat datang, <b><?php echo htmlspecialchars($username); ?></b>!</li>
        </ul>
    </div>
    <div class="Keranjang-Belanja">
        <h2>Keranjang Belanja</h2>
    </div>
    <div class="main">
    <form method="POST" action="add_to_cart.php">
        <?php if (isset($_SESSION['checkout_success'])): ?>
            <p class="success-message"> <?php echo $_SESSION['checkout_success']; unset($_SESSION['checkout_success']); ?> </p>
        <?php endif; ?>
        </form>
        <div class="cart-list">
            <?php if (!empty($cart)): ?>
                <?php foreach ($cart as $id => $item): ?>
                    <div class="cart-item">
                        <p><strong><?php echo htmlspecialchars($item['name']); ?></strong> (<?php echo $item['qty']; ?>x)</p>
                        <p>Rp. <?php echo number_format($item['price'] * $item['qty'], 0, ',', '.'); ?></p>
                        <p><small>Pesanan oleh: <b><?php echo htmlspecialchars($username); ?></b></small></p>
                        <a href="cart.php?remove=<?php echo $id; ?>" class="remove-btn" onclick="return confirm('Anda yakin ingin menghapus pesanan ini?')">Hapus</a>
                    </div>
                    <?php $total += $item['price'] * $item['qty']; ?>
                <?php endforeach; ?>
                <h3>Total: Rp. <?php echo number_format($total, 0, ',', '.'); ?></h3>
        <form method="POST">
                    <button type="submit" name="checkout" class="checkout-btn" onclick="return confirm('Anda yakin ingin checkout?')">Checkout</button>
        </form>
            <?php else: ?>
                <p>Keranjang masih kosong.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>