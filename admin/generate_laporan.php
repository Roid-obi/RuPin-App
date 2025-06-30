<?php
require "../config.php";
session_start();

// Cek akses admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Hapus laporan lama
mysqli_query($con, "DELETE FROM laporan_pembayaran");

// Ambil data pembayaran lunas dari tabel pembayaran
$sql = "
    SELECT 
        MONTH(tanggal_bayar) AS bulan_angka,
        YEAR(tanggal_bayar) AS tahun,
        COUNT(*) AS jumlah_transaksi,
        SUM(jumlah) AS total
    FROM pembayaran
    WHERE status = 'lunas' AND tanggal_bayar IS NOT NULL
    GROUP BY tahun, bulan_angka
    ORDER BY tahun DESC, bulan_angka DESC
";

$result = mysqli_query($con, $sql);

$bulan_nama = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$success = true;

// Simpan ke tabel laporan_pembayaran
while ($row = mysqli_fetch_assoc($result)) {
    $bulan = $bulan_nama[(int)$row['bulan_angka']];
    $tahun = $row['tahun'];
    $total = $row['total'];
    $jumlah = $row['jumlah_transaksi'];
    $waktu_dibuat = date('Y-m-d H:i:s');

    $insert = "INSERT INTO laporan_pembayaran (bulan, tahun, total, jumlah_transaksi, waktu_dibuat)
               VALUES ('$bulan', $tahun, $total, $jumlah, '$waktu_dibuat')";

    if (!mysqli_query($con, $insert)) {
        $success = false;
        echo "❌ Gagal menyimpan laporan untuk $bulan $tahun: " . mysqli_error($con) . "<br>";
    }
}

if ($success) {
    $_SESSION['flash'] = "✅ Laporan berhasil diperbarui.";
    header("Location: laporan_transaksi.php");
    exit;
} else {
    echo "❌ Beberapa laporan gagal diperbarui. Silakan cek kembali.";
}

mysqli_close($con);
