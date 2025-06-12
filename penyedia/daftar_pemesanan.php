<?php
// session_start();
include '../session.php';
include '../config.php';

// Pastikan user adalah penyedia
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyedia') {
    header("Location: ../auth/login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$sql = "
SELECT p.booking_id, i.nama AS item, u.nama AS penyewa, p.status, p.tanggal 
FROM pemesanan p 
JOIN items i ON p.item_id=i.item_id 
JOIN users u ON p.user_id=u.user_id 
WHERE i.user_id=? ORDER BY p.tanggal DESC";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pemesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"  rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: row;
            margin: 0;
        }

        .sidebar {
            width: 250px;
            background-color: #675DFE;
            color: white;
            position: fixed; /* Sidebar tetap di tempat */
            top: 0;
            left: 0;
            height: 100vh; /* Penuh dari atas ke bawah */
            overflow-y: auto; /* Jika isi terlalu panjang */
        }

        .content {
            flex: 1;
            padding: 2rem;
            margin-left: 250px; /* Agar konten tidak tertutup sidebar */
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 1rem;
        }
        .sidebar a:hover,
        .sidebar .active {
            background-color: #574ee5;
        }
        .content {
            flex: 1;
            padding: 2rem;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center py-3">Rupin - Penyedia</h4>
    <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Dashboard</a>
    <a href="daftar_pemesanan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'daftar_pemesanan.php' ? 'active' : '' ?>">Daftar Pemesanan</a>
    <a href="kelola_item.php" class="<?= basename($_SERVER['PHP_SELF']) == 'tambah_item.php' ? 'active' : '' ?>">Kelola Item</a>
    <a href="profil.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'active' : '' ?>">Profil Saya</a>
    <a href="../logout.php" class="text-danger">Logout</a>
</div>

<!-- Konten Utama -->
<div class="content">
    <h2>Daftar Pemesanan</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Booking ID</th>
                    <th>Item</th>
                    <th>Penyewa</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($r = $res->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($r['booking_id']) ?></td>
                    <td><?= htmlspecialchars($r['item']) ?></td>
                    <td><?= htmlspecialchars($r['penyewa']) ?></td>
                    <td><?= htmlspecialchars($r['tanggal']) ?></td>
                    <td><?= htmlspecialchars($r['status']) ?></td>
                    <td>
                        <?php if ($r['status'] === 'menunggu') { ?>
                            <a href="verifikasi_pesanan.php?id=<?= $r['booking_id'] ?>&aksi=terima" class="btn btn-sm btn-success">Terima</a>
                            <a href="verifikasi_pesanan.php?id=<?= $r['booking_id'] ?>&aksi=tolak" class="btn btn-sm btn-danger">Tolak</a>
                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>