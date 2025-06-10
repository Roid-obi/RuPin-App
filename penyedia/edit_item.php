<?php
include '../session.php';
include '../config.php';

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $tipe = $_POST['tipe'];
    $jumlah = $_POST['jumlah'];
    $lokasi = $_POST['lokasi'];

    $query = "UPDATE items SET nama='$nama', tipe='$tipe', jumlah='$jumlah', lokasi='$lokasi'
              WHERE item_id=$id AND user_id=$user_id";
    mysqli_query($con, $query);
    header("Location: kelola_item.php");
    exit;
}

$result = mysqli_query($con, "SELECT * FROM items WHERE item_id=$id AND user_id=$user_id");
$item = mysqli_fetch_assoc($result);
?>

<h2>Edit Item</h2>
<form method="post">
    <input type="text" name="nama" value="<?= $item['nama'] ?>" required><br>
    <input type="text" name="tipe" value="<?= $item['tipe'] ?>" required><br>
    <input type="number" name="jumlah" value="<?= $item['jumlah'] ?>" required><br>
    <input type="text" name="lokasi" value="<?= $item['lokasi'] ?>" required><br>
    <button type="submit">Update</button>
</form>
