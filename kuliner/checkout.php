<?php
session_start();
require 'db.php';

if (!isset($_SESSION['username'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.php';</script>";
    exit;
}

$username = $_SESSION['username']; 
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart)) {
    echo "<script>alert('Keranjang kosong!'); window.location.href='cart.php';</script>";
    exit;
}

// menghitung total harga
$total_price = 0;
foreach ($cart as $item) {
    $total_price += $item['price'] * $item['qty'];
}

// menyimpan ke tabel orders
$query = "INSERT INTO orders (username, total_price) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $username, $total_price);
$stmt->execute();
$order_id = $stmt->insert_id; // mendapatkan ID pesanan terbaru

// menyimpan ke tabel order_items
foreach ($cart as $id => $item) {
    $query = "INSERT INTO order_items (order_id, food_id, qty, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiii", $order_id, $id, $item['qty'], $item['price']);
    $stmt->execute();
}

// mengapus keranjang setelah checkout
unset($_SESSION['cart']);

echo "<script>alert('Checkout berhasil!'); window.location.href='index.php';</script>";
?>