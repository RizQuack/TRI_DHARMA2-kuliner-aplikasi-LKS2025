<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require 'db.php';
$query = "SELECT * FROM foods";
$result = $conn->query($query);
    if (!$result) {
        die("Error pada query: " . $conn->error);
    }
$food = [];
while ($row = $result->fetch_assoc()) {
    $food[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .daftar-makanan {
            text-align: center;
        }
       body {
            background: linear-gradient(to bottom, #ADD8E6, #FFFFFF);
            background-attachment: fixed;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .logo {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 80px;
            height: auto;
        }
        html {
          height: 100%;
        }
        button {
            background-color: green;
            color: white;
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: darkgreen;
        }
        .navbar {
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
        .logout-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }
        .logout-btn {
            background-color: red;
            color: white;
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .content {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            width: 250px;
            background-color: white;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
        }
        .logout-btn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInputs = document.querySelectorAll('input[name="qty"]');

        quantityInputs.forEach(input => {
            input.removeAttribute('max');
            input.removeAttribute('min');
        });
    });
</script>
<img src="assets/images/logo_kota_bogor.png" alt="Logo Kota Bogor" class="logo">
    <div class="navbar">
        <ul>
            <li><a href="index.php">Daftar Makanan</a></li>
            <li><a href="tentang-website.html">Tentang Website</a></li>
            <li><a href="cart.php">Keranjang</a></li>
            <li style="float:right;">Halo selamat datang, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b>!</li>
        </ul>
    </div>
    <div class="daftar-makanan">
    <h2>Daftar Makanan</h2>
</div>

<div class="main">
    <div class="content" id="foodList">
        <?php 
        // menampilkan data nama, harga,dan gambar
        $food = [
            ["id" => 1, "name" => "Asinan Bogor", "harga" => 15000, "image" => "asinan_bogor.png"],
            ["id" => 2, "name" => "Bapatong", "harga" => 20000, "image" => "bapatong.png"],
            ["id" => 3, "name" => "Doclang", "harga" => 12000, "image" => "doclang.png"],
            ["id" => 4, "name" => "Laksa Bogor", "harga" => 18000, "image" => "laksa_bogor.png"],
            ["id" => 5, "name" => "Toge Goreng", "harga" => 14000, "image" => "toge_goreng.png"]
        ];
        foreach ($food as $item): 
            $imagePath = "assets/images/" . $item['image'];
        ?>
            <div class="card">
                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                <p><strong><?php echo htmlspecialchars($item['name']); ?></strong></p>
                <p>Rp. <?php echo number_format($item['harga'], 0, ',', '.'); ?></p>
                <form method="POST" action="add_to_cart.php">
                    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                    <input type="hidden" name="name" value="<?php echo htmlspecialchars($item['name']); ?>">
                    <input type="hidden" name="price" value="<?php echo $item['harga']; ?>">
                    <input type="hidden" name="image" value="<?php echo $item['image']; ?>">
                    <input type="hidden" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>">
                    <label for="qty">Jumlah:</label>
                    <input type="number" name="qty" min="1" value="1" required>
                    <button type="submit">Pesan</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>
    <div class="logout-container">
        <form method="POST" action="logout.php">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</body>
</html>
