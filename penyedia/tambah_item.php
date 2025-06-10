<?php
include '../session.php';
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $tipe = $_POST['tipe'];
    $jumlah = $_POST['jumlah'];
    $lokasi = $_POST['lokasi'];
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO items (user_id, nama, tipe, jumlah, lokasi, status)
              VALUES ('$user_id', '$nama', '$tipe', '$jumlah', '$lokasi', 'tersedia')";
    mysqli_query($con, $query);
    header("Location: kelola_item.php");
    exit;
}
?>

<h2>Tambah Item</h2>
<form method="post">
    <input type="text" name="nama" placeholder="Nama Item" required><br>
    <input type="text" name="tipe" placeholder="Tipe" required><br>
    <input type="number" name="jumlah" placeholder="Jumlah" required><br>
    <input type="text" name="lokasi" placeholder="Lokasi" required><br>
    <button type="submit">Simpan</button>
</form>
