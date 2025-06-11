<?php
include('../session.php');
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $item_id = $_POST['item_id'];
    $tanggal = date('Y-m-d');

    // Ambil harga_sewa dari item
    $query_item = $con->prepare("SELECT harga_sewa FROM items WHERE item_id = ?");
    $query_item->bind_param("i", $item_id);
    $query_item->execute();
    $result_item = $query_item->get_result();

    if ($result_item->num_rows === 0) {
        die("Item tidak ditemukan.");
    }

    $item = $result_item->fetch_assoc();
    $harga_sewa = $item['harga_sewa'];
    $biaya_admin = $harga_sewa * 0.05;
    $total_pembayaran = $harga_sewa + $biaya_admin;
    $metode = "transfer";

    // Masukkan ke tabel pemesanan
    $sql = "INSERT INTO pemesanan (item_id, user_id, status, tanggal) VALUES (?, ?, 'menunggu', ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iis", $item_id, $user_id, $tanggal);

    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;

        // Masukkan ke tabel pembayaran
        $sql2 = "INSERT INTO pembayaran (booking_id, jumlah, status, metode) VALUES (?, ?, 'menunggu', ?)";
        $stmt2 = $con->prepare($sql2);
        $stmt2->bind_param("ids", $booking_id, $total_pembayaran, $metode);
        $stmt2->execute();

        header("Location: konfirmasi_pembayaran.php?booking_id=" . $booking_id);
        exit();
    } else {
        echo "❌ Gagal memesan item. Silakan coba lagi.";
    }
}
?>
