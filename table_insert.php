<?php
require "config.php";

// Insert data ke tabel users
$sql_users = "INSERT INTO users (nama, email, password, role, status, alamat) VALUES
    ('admin', 'admin@gmail.com', '".password_hash("1", PASSWORD_DEFAULT)."', 'admin', 'active', 'Jl. Merdeka No.1'),
    ('penyewa', 'penyewa@gmail.com', '".password_hash("1", PASSWORD_DEFAULT)."', 'penyewa', 'active', 'Jl. Sudirman No.45'),
    ('penyedia', 'penyedia@gmail.com', '".password_hash("1", PASSWORD_DEFAULT)."', 'penyedia', 'active', 'Jl. Sudirman No.45')
";
mysqli_query($con, $sql_users) or die("❌ Gagal insert users: " . mysqli_error($con));

// Ambil user_id penyedia
$result_penyedia = mysqli_query($con, "SELECT user_id FROM users WHERE role = 'penyedia' LIMIT 1");
if (!$result_penyedia) {
    die("❌ Gagal mengambil user_id penyedia: " . mysqli_error($con));
}
$row_penyedia = mysqli_fetch_assoc($result_penyedia);
$penyedia_id = $row_penyedia['user_id'];

// Insert data ke tabel items dengan user_id penyedia
$sql_items = "INSERT INTO items (nama, tipe, jumlah, lokasi, status, user_id) VALUES
    ('Laptop Lenovo', 'Elektronik', 10, 'Gudang A', 'tersedia', $penyedia_id),
    ('Meja Kantor', 'Furniture', 5, 'Gudang B', 'tersedia', $penyedia_id)
";
mysqli_query($con, $sql_items) or die("❌ Gagal insert items: " . mysqli_error($con));

// Ambil user_id penyewa untuk insert pemesanan
$result_penyewa = mysqli_query($con, "SELECT user_id FROM users WHERE role = 'penyewa' LIMIT 1");
if (!$result_penyewa) {
    die("❌ Gagal mengambil user_id penyewa: " . mysqli_error($con));
}
$row_penyewa = mysqli_fetch_assoc($result_penyewa);
$penyewa_id = $row_penyewa['user_id'];

// Ambil item_id pertama untuk pemesanan
$itemResult = mysqli_query($con, "SELECT item_id FROM items LIMIT 1");
$itemRow = mysqli_fetch_assoc($itemResult);
$item_id = $itemRow['item_id'];

// Insert data ke tabel pemesanan
$sql_pemesanan = "INSERT INTO pemesanan (item_id, user_id, status, tanggal) VALUES
    ($item_id, $penyewa_id, 'pending', '2025-06-01')
";
mysqli_query($con, $sql_pemesanan) or die("❌ Gagal insert pemesanan: " . mysqli_error($con));

// Ambil booking_id dari pemesanan terakhir
$booking_id = mysqli_insert_id($con);

// Insert data ke tabel pembayaran
$sql_pembayaran = "INSERT INTO pembayaran (booking_id, jumlah, status, metode, tanggal_bayar) VALUES
    ($booking_id, 1500000, 'unpaid', 'transfer', '2025-06-01')
";
mysqli_query($con, $sql_pembayaran) or die("❌ Gagal insert pembayaran: " . mysqli_error($con));

// Insert data ke tabel laporan_bulanan
$sql_laporan_bulanan = "INSERT INTO laporan_bulanan (bulan) VALUES
    ('Juni 2025')
";
mysqli_query($con, $sql_laporan_bulanan) or die("❌ Gagal insert laporan_bulanan: " . mysqli_error($con));

// Ambil pembayaran_id untuk laporan_pembayaran
$pembayaranResult = mysqli_query($con, "SELECT pembayaran_id FROM pembayaran LIMIT 1");
$pembayaranRow = mysqli_fetch_assoc($pembayaranResult);
$pembayaran_id = $pembayaranRow['pembayaran_id'];

// Insert data ke tabel laporan_pembayaran
$sql_laporan_pembayaran = "INSERT INTO laporan_pembayaran (bulan, pembayaran_id) VALUES
    ('Juni 2025', $pembayaran_id)
";
mysqli_query($con, $sql_laporan_pembayaran) or die("❌ Gagal insert laporan_pembayaran: " . mysqli_error($con));

// Insert data ke tabel sessions
$sql_sessions = "INSERT INTO sessions (session_id, user_id, role) VALUES
    ('sess123456', $penyedia_id, 'admin')
";
mysqli_query($con, $sql_sessions) or die("❌ Gagal insert sessions: " . mysqli_error($con));

echo "✅ Data contoh berhasil dimasukkan ke semua tabel.";

mysqli_close($con);
?>
