<?php
require "config.php";

// Fungsi hash password sederhana
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// ===== INSERT KE TABEL USERS =====
$users = [
    [
        'nama' => 'Admin Utama',
        'email' => 'admin@gmail.com',
        'password' => hashPassword('admin123'),
        'role' => 'admin',
        'status' => 'aktif',
        'alamat' => 'Jl. Admin No. 1'
    ],
    [
        'nama' => 'Orang Penyedia',
        'email' => 'penyedia@gmail.com',
        'password' => hashPassword('penyedia123'),
        'role' => 'penyedia',
        'status' => 'aktif',
        'alamat' => 'Jl. Penyedia No. 5'
    ],
    [
        'nama' => 'Orang Penyewa',
        'email' => 'penyewa@gmail.com',
        'password' => hashPassword('penyewa123'),
        'role' => 'penyewa',
        'status' => 'aktif',
        'alamat' => 'Jl. Penyewa No. 10'
    ],
    [
        'nama' => 'Super Admin',
        'email' => 'superadmin@gmail.com',
        'password' => hashPassword('super123'),
        'role' => 'super-admin',
        'status' => 'aktif',
        'alamat' => 'Isekai No. 1'
    ]
];

foreach ($users as $u) {
    $stmt = $con->prepare("INSERT INTO users (nama, email, password, role, status, alamat) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $u['nama'], $u['email'], $u['password'], $u['role'], $u['status'], $u['alamat']);
    $stmt->execute();
}
echo "✅ Data users berhasil dimasukkan.<br>";

// Ambil user penyedia dan penyewa
$penyedia_id = $con->query("SELECT user_id FROM users WHERE role = 'penyedia' LIMIT 1")->fetch_assoc()['user_id'];
$penyewa_id = $con->query("SELECT user_id FROM users WHERE role = 'penyewa' LIMIT 1")->fetch_assoc()['user_id'];

// ===== INSERT KE TABEL ITEMS =====
$stmt = $con->prepare("INSERT INTO items (user_id, nama, tipe, harga_sewa, lokasi, status, gambar, deskripsi) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$namaItem = "Proyektor Epson";
$tipe = "alat";
$harga = 75000;
$lokasi = "Ruang AVA";
$status = "tersedia";
$gambar = "proyektor.jpg";
$deskripsi = "Proyektor berkualitas HD untuk presentasi.";
$stmt->bind_param("issdssss", $penyedia_id, $namaItem, $tipe, $harga, $lokasi, $status, $gambar, $deskripsi);
$stmt->execute();
echo "✅ Data item berhasil dimasukkan.<br>";

$item_id = $con->insert_id;

// ===== INSERT KE TABEL BOOKING =====
$tanggal = date('Y-m-d');
$jumlah_hari = 2;
$status_booking = "disetujui";
$stmt = $con->prepare("INSERT INTO booking (item_id, user_id, status, tanggal, jumlah_hari) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iissi", $item_id, $penyewa_id, $status_booking, $tanggal, $jumlah_hari);
$stmt->execute();
$booking_id = $con->insert_id;
echo "✅ Data booking berhasil dimasukkan.<br>";

// ===== INSERT KE TABEL PEMBAYARAN =====
$jumlah = 150000;
$status_pembayaran = "lunas";
$metode = "transfer";
$tanggal_bayar = date('Y-m-d');
$bukti = "bukti.jpg";
$stmt = $con->prepare("INSERT INTO pembayaran (booking_id, jumlah, status, metode, tanggal_bayar, bukti) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("idssss", $booking_id, $jumlah, $status_pembayaran, $metode, $tanggal_bayar, $bukti);
$stmt->execute();
$pembayaran_id = $con->insert_id;
echo "✅ Data pembayaran berhasil dimasukkan.<br>";

// ===== INSERT KE TABEL PENGEMBALIAN =====
$tanggal_kembali = date('Y-m-d', strtotime('+2 days'));
$status_kembali = 'tepat waktu';
$denda = 0;
$bukti_kembali = "bukti_kembali.jpg";
$stmt = $con->prepare("INSERT INTO pengembalian (booking_id, tanggal_kembali, status, denda, bukti) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issds", $booking_id, $tanggal_kembali, $status_kembali, $denda, $bukti_kembali);
$stmt->execute();
echo "✅ Data pengembalian berhasil dimasukkan.<br>";

// ===== INSERT KE TABEL LAPORAN_PEMBAYARAN =====
$bulan = date('F');
$tahun = date('Y');
$jumlah_transaksi = 1;
$total = $jumlah;
$stmt = $con->prepare("INSERT INTO laporan_pembayaran (bulan, tahun, total, jumlah_transaksi) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sidi", $bulan, $tahun, $total, $jumlah_transaksi);
$stmt->execute();
$laporan_id = $con->insert_id;
echo "✅ Data laporan_pembayaran berhasil dimasukkan.<br>";

// ===== INSERT KE TABEL LAPORAN_PEMBAYARAN_DETAIL =====
$stmt = $con->prepare("INSERT INTO laporan_pembayaran_detail (laporan_id, pembayaran_id) VALUES (?, ?)");
$stmt->bind_param("ii", $laporan_id, $pembayaran_id);
$stmt->execute();
echo "✅ Data laporan_pembayaran_detail berhasil dimasukkan.<br>";

$con->close();
?>
