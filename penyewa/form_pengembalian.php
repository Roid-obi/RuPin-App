<?php
include('../session.php');
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['booking_id'])) {
    die("❌ Booking ID tidak ditemukan.");
}

$booking_id = intval($_GET['booking_id']);
$user_id = $_SESSION['user_id'];

$sql = "SELECT i.nama, b.booking_id
        FROM booking b
        JOIN items i ON b.item_id = i.item_id
        WHERE b.booking_id = ? AND b.user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Data booking tidak ditemukan.");
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pengembalian</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> 
    <link href="../styles/dashboard.css" rel="stylesheet">
    <style>
        .card-custom {
            border-radius: 0.75rem;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-weight: 500;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="header-sidebar text-center py-3">Rupin Dashboard</h4>
    <a href="index.php">Dashboard</a>
    <a href="status_pemesanan.php" class="active">Status Pemesanan</a>
    <a href="profil.php">Profil Saya</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <!-- Navbar Atas -->
    <div class="top-nav rounded shadow-sm mb-4 d-flex justify-content-between align-items-center">
        <a href="status_pemesanan.php" class="go-home btn btn-outline-secondary btn-sm"><i class="fa-solid fa-chevron-left me-2"></i>Kembali ke Status</a>
        <div class="text-end">
            <small>Halo, <?= ucfirst($_SESSION['role']) ?></small>
        </div>
    </div>

    <h2 class="mb-3">Form Pengembalian</h2>

    <div class="card card-custom p-4">
        <form action="proses_pengembalian.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="booking_id" value="<?= $data['booking_id'] ?>">

            <div class="mb-3">
                <label class="form-label">Nama Barang</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status Pengembalian <span class="text-danger">*</span></label>
                <select name="status" id="status" class="form-select" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="tepat waktu">Tepat Waktu</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="hilang/rusak">Hilang/Rusak</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="bukti" class="form-label">Upload Bukti Pengembalian <span class="text-danger">*</span></label>
                <input type="file" name="bukti" id="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required onchange="previewBukti(this)">
                <small class="text-muted">Format: .jpg, .jpeg, .png, atau .pdf. Max 2MB.</small>

                <!-- Preview image -->
                <div id="preview-container" class="mt-3" style="display: none;">
                    <label class="form-label">Preview Bukti Gambar:</label>
                    <img id="preview-image" src="#" alt="Preview" class="img-thumbnail" style="max-height: 300px;">
                </div>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane me-1"></i>Kirim Pengembalian</button>
            </div>
        </form>
    </div>
</div>
<script>
function previewBukti(input) {
    const file = input.files[0];
    const previewContainer = document.getElementById("preview-container");
    const previewImage = document.getElementById("preview-image");

    if (!file) {
        previewContainer.style.display = "none";
        return;
    }

    const allowedImageTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (allowedImageTypes.includes(file.type)) {
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            previewContainer.style.display = "block";
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = "none";
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
