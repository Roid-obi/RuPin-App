<?php
require "config.php";

// NONAKTIFKAN sementara cek foreign key agar bisa drop semua tabel tanpa error
mysqli_query($con, "SET FOREIGN_KEY_CHECKS=0");

// DROP tabel dari anak ke induk (urutan penting)
mysqli_query($con, "DROP TABLE IF EXISTS laporan_pembayaran_detail");
mysqli_query($con, "DROP TABLE IF EXISTS laporan_pembayaran");
mysqli_query($con, "DROP TABLE IF EXISTS pengembalian");
mysqli_query($con, "DROP TABLE IF EXISTS pembayaran");
mysqli_query($con, "DROP TABLE IF EXISTS booking");
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
    status ENUM('aktif', 'nonaktif'),
    alamat TEXT
)";
mysqli_query($con, $sql_users) or die("❌ Gagal membuat tabel users: " . mysqli_error($con));

// CREATE TABLE items
$sql_items = "CREATE TABLE items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    nama VARCHAR(100),
    tipe ENUM('ruang', 'alat'),
    harga_sewa DOUBLE,
    lokasi VARCHAR(100),
    status ENUM('tersedia', 'tidak tersedia'),
    gambar VARCHAR(255),
    deskripsi TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";
mysqli_query($con, $sql_items) or die("❌ Gagal membuat tabel items: " . mysqli_error($con));

// CREATE TABLE booking
$sql_booking = "CREATE TABLE booking (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    user_id INT,
    status ENUM('menunggu', 'disetujui', 'ditolak', 'selesai'),
    tanggal DATE,
    jumlah_hari INT,
    FOREIGN KEY (item_id) REFERENCES items(item_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)";
mysqli_query($con, $sql_booking) or die("❌ Gagal membuat tabel booking: " . mysqli_error($con));

// CREATE TABLE pembayaran
$sql_pembayaran = "CREATE TABLE pembayaran (
    pembayaran_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNIQUE,
    jumlah DOUBLE,
    status ENUM('belum bayar', 'menunggu', 'lunas'),
    metode VARCHAR(50),
    tanggal_bayar DATE NULL,
    bukti VARCHAR(255) NULL,
    FOREIGN KEY (booking_id) REFERENCES booking(booking_id)
)";
mysqli_query($con, $sql_pembayaran) or die("❌ Gagal membuat tabel pembayaran: " . mysqli_error($con));

// CREATE TABLE laporan_pembayaran (rekap bulanan)
$sql_laporan_pembayaran = "CREATE TABLE laporan_pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bulan VARCHAR(20),
    tahun INT,
    total DOUBLE,
    jumlah_transaksi INT,
    waktu_dibuat DATETIME DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($con, $sql_laporan_pembayaran) or die("❌ Gagal membuat tabel laporan_pembayaran: " . mysqli_error($con));

// CREATE TABLE laporan_pembayaran_detail (relasi ke pembayaran)
$sql_laporan_detail = "CREATE TABLE laporan_pembayaran_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    laporan_id INT,
    pembayaran_id INT,
    FOREIGN KEY (laporan_id) REFERENCES laporan_pembayaran(id) ON DELETE CASCADE,
    FOREIGN KEY (pembayaran_id) REFERENCES pembayaran(pembayaran_id) ON DELETE CASCADE
)";
mysqli_query($con, $sql_laporan_detail) or die("❌ Gagal membuat tabel laporan_pembayaran_detail: " . mysqli_error($con));

// CREATE TABLE pengembalian
$sql_pengembalian = "CREATE TABLE pengembalian (
    pengembalian_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNIQUE,
    tanggal_kembali DATE,
    status ENUM('tepat waktu', 'terlambat', 'hilang/rusak'),
    denda DOUBLE DEFAULT 0,
    bukti VARCHAR(255),
    FOREIGN KEY (booking_id) REFERENCES booking(booking_id)
)";
mysqli_query($con, $sql_pengembalian) or die("❌ Gagal membuat tabel pengembalian: " . mysqli_error($con));

echo "✅ SEMUA TABEL BERHASIL DIHAPUS DAN DIBUAT ULANG.<br>";
echo "✅ Struktur database siap digunakan.";

mysqli_close($con);
?>
