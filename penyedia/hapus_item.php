<?php
include '../session.php';
include '../config.php';

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Ambil semua booking_id dari item
$booking_result = mysqli_query($con, "SELECT booking_id FROM pemesanan WHERE item_id = $id");

while ($booking = mysqli_fetch_assoc($booking_result)) {
    $booking_id = $booking['booking_id'];

    // Ambil semua pembayaran_id dari booking
    $pembayaran_result = mysqli_query($con, "SELECT pembayaran_id FROM pembayaran WHERE booking_id = $booking_id");

    while ($pembayaran = mysqli_fetch_assoc($pembayaran_result)) {
        $pembayaran_id = $pembayaran['pembayaran_id'];

        // Hapus laporan pembayaran berdasarkan pembayaran_id
        mysqli_query($con, "DELETE FROM laporan_pembayaran WHERE pembayaran_id = $pembayaran_id");
    }

    // Hapus pembayaran setelah laporan dihapus
    mysqli_query($con, "DELETE FROM pembayaran WHERE booking_id = $booking_id");
}

// Hapus pemesanan setelah pembayaran dihapus
mysqli_query($con, "DELETE FROM pemesanan WHERE item_id = $id");

// Hapus gambar item (jika ada)
$result = mysqli_query($con, "SELECT gambar FROM items WHERE item_id = $id AND user_id = $user_id");
$item = mysqli_fetch_assoc($result);

if ($item && !empty($item['gambar'])) {
    $gambar_path = '../uploads/' . $item['gambar'];
    if (file_exists($gambar_path)) {
        unlink($gambar_path);
    }
}

// Terakhir: Hapus item itu sendiri
mysqli_query($con, "DELETE FROM items WHERE item_id = $id AND user_id = $user_id");

header("Location: kelola_item.php");
exit;
?>
