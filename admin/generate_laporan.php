<?php
require "../config.php";
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil semua pembayaran lunas dan kelompokkan per bulan & tahun
$sql = "SELECT 
            MONTH(tanggal_bayar) AS bulan_angka,
            MONTHNAME(tanggal_bayar) AS bulan,
            YEAR(tanggal_bayar) AS tahun,
            COUNT(*) AS jumlah_transaksi,
            SUM(jumlah) AS total
        FROM pembayaran
        WHERE status = 'lunas' AND tanggal_bayar IS NOT NULL
        GROUP BY YEAR(tanggal_bayar), MONTH(tanggal_bayar)
        ORDER BY tahun DESC, bulan_angka DESC";

$result = mysqli_query($con, $sql);

// Hapus laporan lama agar tidak duplikat
mysqli_query($con, "DELETE FROM laporan_pembayaran");

// Hapus detail lama
mysqli_query($con, "DELETE FROM laporan_pembayaran_detail");

// Loop per bulan
while ($row = mysqli_fetch_assoc($result)) {
    $bulan = $row['bulan'];
    $bulan_angka = $row['bulan_angka'];
    $tahun = $row['tahun'];
    $total = $row['total'];
    $jumlah = $row['jumlah_transaksi'];

    // Simpan ke laporan_pembayaran
    $insertLaporan = "INSERT INTO laporan_pembayaran (bulan, tahun, total, jumlah_transaksi, waktu_dibuat)
                      VALUES ('$bulan', $tahun, $total, $jumlah, NOW())";
    mysqli_query($con, $insertLaporan) or die("❌ Gagal insert laporan: " . mysqli_error($con));
    $laporan_id = mysqli_insert_id($con);

    // Ambil semua pembayaran pada bulan & tahun tersebut
    $pembayaranResult = mysqli_query($con, "
        SELECT pembayaran_id FROM pembayaran
        WHERE status = 'lunas'
        AND MONTH(tanggal_bayar) = $bulan_angka
        AND YEAR(tanggal_bayar) = $tahun
    ");

    // Simpan ke laporan_pembayaran_detail
    while ($pembayaran = mysqli_fetch_assoc($pembayaranResult)) {
        $pembayaran_id = $pembayaran['pembayaran_id'];
        $insertDetail = "INSERT INTO laporan_pembayaran_detail (laporan_id, pembayaran_id)
                         VALUES ($laporan_id, $pembayaran_id)";
        mysqli_query($con, $insertDetail) or die("❌ Gagal insert detail: " . mysqli_error($con));
    }
}

echo "<script>
    alert('Laporan berhasil diperbarui.');
    window.location.href = 'laporan_transaksi.php';
</script>";
