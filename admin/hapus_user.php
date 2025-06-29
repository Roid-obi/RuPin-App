<?php
include('../session.php');
include('../config.php');

// Pastikan hanya admin yang bisa menghapus
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil ID user dari URL dan validasi
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid.");
}

$id = intval($_GET['id']);

// Cegah admin menghapus dirinya sendiri
if ($_SESSION['user_id'] == $id) {
    die("❌ Anda tidak dapat menghapus akun Anda sendiri.");
}

// Pastikan user dengan ID tersebut ada
$stmt = $con->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Pengguna tidak ditemukan.");
}

// Jalankan query DELETE
$stmt = $con->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Redirect kembali ke halaman kelola user
header("Location: kelola_user.php?success=hapus");
exit;
