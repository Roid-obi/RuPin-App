<?php
include '../session.php';
include '../config.php';

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($con, $_POST['nama']);
    $tipe = mysqli_real_escape_string($con, $_POST['tipe']);
    $harga_sewa = floatval($_POST['harga_sewa']);
    $lokasi = mysqli_real_escape_string($con, $_POST['lokasi']);
    $status = mysqli_real_escape_string($con, $_POST['status']);

    // Ambil data lama
    $result = mysqli_query($con, "SELECT gambar FROM items WHERE item_id=$id AND user_id=$user_id");
    $old = mysqli_fetch_assoc($result);
    $gambar = $old['gambar'];

    // Jika ada file baru diunggah
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $filename = basename($_FILES['gambar']['name']);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $ext;
        $target_path = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_path)) {
            // Hapus gambar lama (jika ada dan tidak kosong)
            if (!empty($gambar) && file_exists($upload_dir . $gambar)) {
                unlink($upload_dir . $gambar);
            }
            $gambar = $new_filename;
        }
    }

    $query = "UPDATE items 
              SET nama='$nama', tipe='$tipe', harga_sewa='$harga_sewa', lokasi='$lokasi', status='$status', gambar='$gambar'
              WHERE item_id=$id AND user_id=$user_id";

    mysqli_query($con, $query);
    header("Location: kelola_item.php");
    exit;
}

$result = mysqli_query($con, "SELECT * FROM items WHERE item_id=$id AND user_id=$user_id");
$item = mysqli_fetch_assoc($result);

if (!$item) {
    die("Item tidak ditemukan atau bukan milik pengguna.");
}
?>

<h2>Edit Item</h2>
<form method="post" enctype="multipart/form-data">
    <label>Nama:</label><br>
    <input type="text" name="nama" value="<?= htmlspecialchars($item['nama']) ?>" required><br><br>

    <label>Tipe:</label><br>
    <select name="tipe" required>
        <option value="ruang" <?= $item['tipe'] === 'ruang' ? 'selected' : '' ?>>Ruang</option>
        <option value="alat" <?= $item['tipe'] === 'alat' ? 'selected' : '' ?>>Alat</option>
    </select><br><br>

    <label>Harga Sewa:</label><br>
    <input type="number" name="harga_sewa" value="<?= $item['harga_sewa'] ?>" required><br><br>

    <label>Lokasi:</label><br>
    <input type="text" name="lokasi" value="<?= htmlspecialchars($item['lokasi']) ?>" required><br><br>

    <label>Status:</label><br>
    <select name="status" required>
        <option value="tersedia" <?= $item['status'] === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
        <option value="tidak tersedia" <?= $item['status'] === 'tidak tersedia' ? 'selected' : '' ?>>Tidak Tersedia</option>
    </select><br><br>

    <label>Gambar Lama:</label><br>
    <?php if (!empty($item['gambar'])): ?>
        <img src="../uploads/<?= $item['gambar'] ?>" alt="Gambar Item" style="max-width:200px;"><br>
    <?php else: ?>
        <em>Tidak ada gambar</em><br>
    <?php endif; ?>
    <br>

    <label>Ganti Gambar (opsional):</label><br>
    <input type="file" name="gambar" accept="image/*" onchange="previewImage(event)"><br><br>
    <img id="preview" src="#" alt="Preview Gambar Baru" style="display:none; max-width:200px;"><br><br>

    <button type="submit">Update</button>
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
