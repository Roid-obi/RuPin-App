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

// Ambil data booking
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
</head>
<body>
<div class="container mt-5">
    <a href="status_pemesanan.php" class="btn btn-secondary btn-sm mb-3"><i class="fa fa-chevron-left me-1"></i> Kembali ke Status</a>

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Form Pengembalian</h4>
        </div>
        <div class="card-body">
            <form action="proses_pengembalian.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="booking_id" value="<?= $data['booking_id'] ?>">

                <div class="mb-3">
                    <label class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" readonly>
                </div>

                <div class="mb-3">
                    <label for="bukti" class="form-label">Upload Bukti Pengembalian <span class="text-danger">*</span></label>
                    <input type="file" name="bukti" id="bukti" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                    <small class="text-muted">Format: .jpg, .jpeg, .png, atau .pdf. Max 2MB.</small>
                </div>

                <button type="submit" class="btn btn-success">Kirim Pengembalian</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
