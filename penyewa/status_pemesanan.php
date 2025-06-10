<?php
include('../session.php');
include('../config.php');

$user_id = $_SESSION['user_id'];
$sql = "SELECT p.booking_id, i.nama, p.status AS status_pesan, b.status AS status_bayar
        FROM pemesanan p
        JOIN items i ON i.item_id = p.item_id
        LEFT JOIN pembayaran b ON b.booking_id = p.booking_id
        WHERE p.user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Status Pemesanan</h2>
<table border="1">
    <tr>
        <th>Nama Barang</th>
        <th>Status Pemesanan</th>
        <th>Status Pembayaran</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['nama'] ?></td>
        <td><?= $row['status_pesan'] ?></td>
        <td><?= $row['status_bayar'] ?></td>
        <td>
            <?php if ($row['status_pesan'] == 'menunggu') { ?>
            <a href="batal_pemesanan.php?id=<?= $row['booking_id'] ?>">Batalkan</a>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
</table>
