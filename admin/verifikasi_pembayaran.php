<?php
include('../session.php');
include('../config.php');

$id = $_GET['id'];
$aksi = $_GET['aksi'];

if ($aksi == 'konfirmasi') {
    $stmt = $con->prepare("UPDATE pembayaran SET status = 'dikonfirmasi' WHERE pembayaran_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: konfirmasi_pembayaran.php");
