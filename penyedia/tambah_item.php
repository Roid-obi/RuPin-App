<?php
include '../session.php';
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($con, $_POST['nama']);
    $tipe = mysqli_real_escape_string($con, $_POST['tipe']);
    $harga_sewa = floatval($_POST['harga_sewa']);
    $lokasi = mysqli_real_escape_string($con, $_POST['lokasi']);
    $user_id = $_SESSION['user_id'];

    // Upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $filename = basename($_FILES['gambar']['name']);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $ext;
        $target_path = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_path)) {
            $gambar = $new_filename;
        }
    }

    $query = "INSERT INTO items (user_id, nama, tipe, harga_sewa, lokasi, status, gambar)
              VALUES ('$user_id', '$nama', '$tipe', '$harga_sewa', '$lokasi', 'tersedia', '$gambar')";
    mysqli_query($con, $query);
    header("Location: kelola_item.php");
    exit;
}
?>

<h2>Tambah Item</h2>
<form method="post" enctype="multipart/form-data">
    <label>Nama Item:</label><br>
    <input type="text" name="nama" placeholder="Nama Item" required><br><br>

    <label>Tipe:</label><br>
    <select name="tipe" required>
        <option value="">-- Pilih Tipe --</option>
        <option value="ruang">Ruang</option>
        <option value="alat">Alat</option>
    </select><br><br>

    <label>Harga Sewa:</label><br>
    <input type="number" name="harga_sewa" placeholder="Harga Sewa" required><br><br>

    <label>Lokasi:</label><br>
    <input type="text" name="lokasi" placeholder="Lokasi" required><br><br>

    <label>Gambar:</label><br>
    <input type="file" name="gambar" accept="image/*" onchange="previewImage(event)"><br><br>
    <img id="preview" src="#" alt="Preview Gambar" style="display:none; max-width:200px;"><br><br>

    <button type="submit">Simpan</button>
</form>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const img = document.getElementById('preview');
        img.src = reader.result;
        img.style.display = 'block';
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>
