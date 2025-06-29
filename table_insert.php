<?php
require "config.php";

// Fungsi hash password
function hashPassword($plainText) {
    return password_hash($plainText, PASSWORD_DEFAULT);
}

// ========== INSERT INTO users ==========
$users = [
    [
        'nama'      => 'Admin Utama',
        'email'     => 'admin@gmail.com',
        'password'  => hashPassword('admin123'),
        'role'      => 'admin',
        'status'    => 'aktif',
        'alamat'    => 'Jl. Admin No. 1'
    ],
    [
        'nama'      => 'Orang Penyedia',
        'email'     => 'penyedia@gmail.com',
        'password'  => hashPassword('penyedia123'),
        'role'      => 'penyedia',
        'status'    => 'aktif',
        'alamat'    => 'Jl. Penyedia No. 5'
    ],
    [
        'nama'      => 'Orang Penyewa',
        'email'     => 'penyewa@gmail.com',
        'password'  => hashPassword('penyewa123'),
        'role'      => 'penyewa',
        'status'    => 'aktif',
        'alamat'    => 'Jl. Penyewa No. 10'
    ],
    [
        'nama'      => 'Super Admin',
        'email'     => 'superadmin@gmail.com',
        'password'  => hashPassword('super123'),
        'role'      => 'super-admin',
        'status'    => 'aktif',
        'alamat'    => 'Isekai No. 1'
    ]
];

foreach ($users as $user) {
    $stmt = $con->prepare("INSERT INTO users (nama, email, password, role, status, alamat) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $user['nama'], $user['email'], $user['password'], $user['role'], $user['status'], $user['alamat']);
    $stmt->execute();
}
echo "✅ Data users dimasukkan.<br>";

// ========== INSERT INTO items ==========
$stmt = $con->prepare("INSERT INTO items (user_id, nama, tipe, harga_sewa, lokasi, status, gambar, deskripsi) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issdssss", $user_id, $nama_item, $tipe, $harga, $lokasi, $status_item, $gambar, $deskripsi);

$user_id = 2; // id penyedia
$nama_item = "Proyektor Epson X300";
$tipe = "alat";
$harga = 150000;
$lokasi = "Gedung B Lantai 2";
$status_item = "tersedia";
$gambar = "proyektor.jpg";
$deskripsi = "Proyektor dengan resolusi tinggi, cocok untuk presentasi.";

$stmt->execute();
echo "✅ Data items dimasukkan.<br>";

// ========== INSERT INTO booking ==========
$stmt = $con->prepare("INSERT INTO booking (item_id, user_id, status, tanggal, jumlah_hari) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iissi", $item_id, $penyewa_id, $status_booking, $tanggal_booking, $jumlah_hari);

$item_id = 1; // id dari item yang dimasukkan
$penyewa_id = 3; // id penyewa
$status_booking = "disetujui";
$tanggal_booking = date('Y-m-d');
$jumlah_hari = 2;

$stmt->execute();
echo "✅ Data booking dimasukkan.<br>";

// ========== INSERT INTO pembayaran ==========
$stmt = $con->prepare("INSERT INTO pembayaran (booking_id, jumlah, status, metode, tanggal_bayar) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("idsss", $booking_id, $jumlah_bayar, $status_bayar, $metode_bayar, $tanggal_bayar);

$booking_id = 1;
$jumlah_bayar = 300000; // 2 hari x 150000
$status_bayar = "lunas";
$metode_bayar = "transfer";
$tanggal_bayar = date('Y-m-d');

$stmt->execute();
echo "✅ Data pembayaran dimasukkan.<br>";

// ========== INSERT INTO laporan_pembayaran ==========
$stmt = $con->prepare("INSERT INTO laporan_pembayaran (bulan, pembayaran_id) VALUES (?, ?)");
$stmt->bind_param("si", $bulan_laporan, $pembayaran_id);

$bulan_laporan = date('F'); // Contoh: June
$pembayaran_id = 1;

$stmt->execute();
echo "✅ Data laporan_pembayaran dimasukkan.<br>";

// ========== INSERT INTO pengembalian ==========
$stmt = $con->prepare("INSERT INTO pengembalian (booking_id, tanggal_kembali, status, denda, bukti) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issds", $booking_id_pengembalian, $tanggal_kembali, $status_kembali, $denda, $bukti);

$booking_id_pengembalian = 1;
$tanggal_kembali = date('Y-m-d', strtotime('+2 days'));
$status_kembali = "tepat waktu";
$denda = 0;
$bukti = "foto_pengembalian.jpg";

$stmt->execute();
echo "✅ Data pengembalian dimasukkan.<br>";

$con->close();
?>
