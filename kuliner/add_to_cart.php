<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    $_SESSION['error'] = "Anda harus login terlebih dahulu!";
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // mengmbil data dari form
    $id = intval($_POST['id']);
    $name = htmlspecialchars($_POST['name']);
    $price = floatval($_POST['price']);
    $qty = intval($_POST['qty']);

    if (!$conn) {
        $_SESSION['error'] = "Koneksi ke database gagal!";
        header("Location: index.php");
        exit();
    }

    // Ambil stok dari database
    $query = "SELECT stok, image FROM foods WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        $_SESSION['error'] = "Terjadi kesalahan pada query SELECT.";
        header("Location: index.php");
        exit();
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $food = $result->fetch_assoc();

    if (!$food) {
        $_SESSION['error'] = "Makanan tidak ditemukan!";
        header("Location: index.php");
        exit();
    }

    // menyimpan ke dalam SESSION (keranjang)
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Jika makanan sudah ada di keranjang, update jumlahnya
    if (isset($_SESSION['cart'][$id])) {
        $totalQty = $_SESSION['cart'][$id]['qty'] + $qty;
        if ($totalQty > $stokTersedia) {
            $_SESSION['error'] = "Total jumlah pesanan melebihi stok yang tersedia!";
            header("Location: index.php");
            exit();
        }
        $_SESSION['cart'][$id]['qty'] = $totalQty;
    } else {
        // Jika makanan belum ada di keranjang, tambahkan sebagai item baru
        $_SESSION['cart'][$id] = [
            'name' => $name,
            'price' => $price,
            'image' => $image,
            'qty' => $qty
        ];
    }
    // memasukkan pesanan ke database
    $totalPrice = $price * $qty;
    $insertQuery = "INSERT INTO orders (username, food_name, qty, total_price, order_date) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($insertQuery);
    if (!$stmt) {
        $_SESSION['error'] = "Terjadi kesalahan pada query INSERT.";
        header("Location: index.php");
        exit();
    }
    $stmt->bind_param("siid", $username, $id, $qty, $totalPrice);
    if ($stmt->execute()) {
        // mengurangi stok makanan di database
        $updateStockQuery = "UPDATE foods SET stok = stok - ? WHERE id = ?";
        $stmt = $conn->prepare($updateStockQuery);
        if ($stmt) {
            $stmt->bind_param("ii", $qty, $id);
            $stmt->execute();
        }
        $_SESSION['success'] = "Pesanan berhasil ditambahkan ke keranjang!";
        header("Location: cart.php");
        exit();
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat menyimpan pesanan.";
        header("Location: index.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Permintaan tidak valid!";
    header("Location: index.php");
    exit();
}
?>
