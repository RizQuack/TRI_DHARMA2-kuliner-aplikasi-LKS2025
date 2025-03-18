<?php
$host = "localhost";
$user = "root"; 
$pass = ""; 
$dbname = "food_ordering"; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
