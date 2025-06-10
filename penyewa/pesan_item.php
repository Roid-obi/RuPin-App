<?php
include('../session.php');
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $item_id = $_POST['item_id'];
    $tanggal = date('Y-m-d');

    $sql = "INSERT INTO pemesanan (item_id, user_id, status, tanggal) VALUES (?, ?, 'menunggu', ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("iis", $item_id, $user_id, $tanggal);

    if ($stmt->execute()) {
        $booking_id = $stmt->insert_id;

        // Simulasi pembayaran langsung
        $jumlah = 100000; // harga tetap untuk simulasi
        $metode = "transfer";

        $sql2 = "INSERT INTO pembayaran (booking_id, jumlah, status, metode) VALUES (?, ?, 'menunggu', ?)";
        $stmt2 = $con->prepare($sql2);
        $stmt2->bind_param("ids", $booking_id, $jumlah, $metode);
        $stmt2->execute();

        header("Location: status_pemesanan.php?msg=berhasil");
    } else {
        echo "Gagal memesan";
    }
}
