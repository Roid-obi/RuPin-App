<?php
include('../session.php');
include('../config.php');

// Pastikan user adalah penyewa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
        header("Location: ../auth/login.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $item_id = intval($_POST['item_id']);
    $jumlah_hari = intval($_POST['jumlah_hari']);
    $tanggal = date('Y-m-d');

    if ($jumlah_hari <= 0) {
        die("Jumlah hari tidak valid.");
    }

    // Ambil data item
    $query_item = $con->prepare("SELECT harga_sewa FROM items WHERE item_id = ?");
    $query_item->bind_param("i", $item_id);
    $query_item->execute();
    $result_item = $query_item->get_result();

    if ($result_item->num_rows === 0) {
        die("Item tidak ditemukan.");
    }

    $item = $result_item->fetch_assoc();
    $harga_sewa = $item['harga_sewa'];

    // Hitung biaya sewa
    $total_harga = $harga_sewa * $jumlah_hari;
    $biaya_admin = $total_harga * 0.05;
    $total_pembayaran = $total_harga + $biaya_admin;

    $metode = "transfer";

    // Simpan ke tabel booking
    $sql_booking = "INSERT INTO booking (item_id, user_id, status, tanggal, jumlah_hari) 
                    VALUES (?, ?, 'menunggu', ?, ?)";
    $stmt_booking = $con->prepare($sql_booking);
    $stmt_booking->bind_param("iisi", $item_id, $user_id, $tanggal, $jumlah_hari);

    if ($stmt_booking->execute()) {
        $booking_id = $stmt_booking->insert_id;

        // Simpan ke tabel pembayaran (tanpa tanggal_bayar dan bukti)
        $sql_pembayaran = "INSERT INTO pembayaran (booking_id, jumlah, status, metode, tanggal_bayar, bukti) 
                           VALUES (?, ?, 'belum bayar', ?, NULL, NULL)";
        $stmt_pembayaran = $con->prepare($sql_pembayaran);
        $stmt_pembayaran->bind_param("ids", $booking_id, $total_pembayaran, $metode);
        $stmt_pembayaran->execute();

        // Update status item jadi tidak tersedia
        $update_item = $con->prepare("UPDATE items SET status = 'tidak tersedia' WHERE item_id = ?");
        $update_item->bind_param("i", $item_id);
        $update_item->execute();

        // Redirect ke halaman konfirmasi pembayaran
        header("Location: konfirmasi_pembayaran.php?booking_id=" . $booking_id);
        exit;
    } else {
        echo "❌ Gagal memesan item. Silakan coba lagi.";
    }
} else {
    // Jika bukan POST, redirect ke homepage atau tampilkan error
    header("Location: ../index.php");
    exit;
}
?>
