<?php
require "config.php";

// NONAKTIFKAN sementara cek foreign key agar bisa drop semua tabel tanpa error
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=0");

// DROP tabel dari anak ke induk (urutan penting)
mysqli_query($con, "DROP TABLE IF EXISTS laporan_pembayaran");
mysqli_query($con, "DROP TABLE IF EXISTS laporan_bulanan");
mysqli_query($con, "DROP TABLE IF EXISTS pembayaran");
mysqli_query($con, "DROP TABLE IF EXISTS pemesanan");
mysqli_query($con, "DROP TABLE IF EXISTS sessions");
mysqli_query($con, "DROP TABLE IF EXISTS items");
mysqli_query($con, "DROP TABLE IF EXISTS users");

// AKTIFKAN kembali cek foreign key
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");

// CREATE TABLE users
$sql_users = "CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(50),
    status VARCHAR(50),
    alamat TEXT
)";
mysqli_query($con, $sql_users) or die("❌ Gagal membuat tabel users: " . mysqli_error($con));

// CREATE TABLE items — sekarang dengan user_id (penyedia)
$sql_items = "CREATE TABLE items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    nama VARCHAR(100),
    tipe VARCHAR(50),
    jumlah INT,
    lokasi VARCHAR(100),
    status VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";
mysqli_query($con, $sql_items) or die("❌ Gagal membuat tabel items: " . mysqli_error($con));

// CREATE TABLE pemesanan
$sql_pemesanan = "CREATE TABLE pemesanan (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    user_id INT,
    status VARCHAR(50),
    tanggal DATE,
    FOREIGN KEY (item_id) REFERENCES items(item_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";
mysqli_query($con, $sql_pemesanan) or die("❌ Gagal membuat tabel pemesanan: " . mysqli_error($con));

// CREATE TABLE pembayaran
$sql_pembayaran = "CREATE TABLE pembayaran (
    pembayaran_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNIQUE,
    jumlah DOUBLE,
    status VARCHAR(50),
    metode VARCHAR(50),
    tanggal_bayar DATE,
    FOREIGN KEY (booking_id) REFERENCES pemesanan(booking_id)
)";
mysqli_query($con, $sql_pembayaran) or die("❌ Gagal membuat tabel pembayaran: " . mysqli_error($con));

// CREATE TABLE laporan_bulanan
$sql_laporan_bulanan = "CREATE TABLE laporan_bulanan (
    bulan VARCHAR(20) PRIMARY KEY
)";
mysqli_query($con, $sql_laporan_bulanan) or die("❌ Gagal membuat tabel laporan_bulanan: " . mysqli_error($con));

// CREATE TABLE laporan_pembayaran (junction table)
$sql_laporan_pembayaran = "CREATE TABLE laporan_pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bulan VARCHAR(20),
    pembayaran_id INT UNIQUE,
    FOREIGN KEY (bulan) REFERENCES laporan_bulanan(bulan),
    FOREIGN KEY (pembayaran_id) REFERENCES pembayaran(pembayaran_id)
)";
mysqli_query($con, $sql_laporan_pembayaran) or die("❌ Gagal membuat tabel laporan_pembayaran: " . mysqli_error($con));

// CREATE TABLE sessions
$sql_sessions = "CREATE TABLE sessions (
    session_id VARCHAR(100) PRIMARY KEY,
    user_id INT,
    role VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";
mysqli_query($con, $sql_sessions) or die("❌ Gagal membuat tabel sessions: " . mysqli_error($con));

echo "✅ SEMUA TABEL BERHASIL DIHAPUS DAN DIBUAT ULANG.<br>";
echo "✅ Struktur database siap digunakan.";

mysqli_close($con);
?>
