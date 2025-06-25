<?php
header('Content-Type: application/json');

require "../config.php"; // Sesuaikan path sesuai struktur direktori

$data = json_decode(file_get_contents('php://input'), true);

$booking_id = $data['booking_id'];
$hari_keterlambatan = $data['hari_keterlambatan'];

// Ambil data dari database
$sql = "SELECT 
            p.user_id,
            u.nama AS nama_penyewa,
            i.harga_sewa,
            i.tipe AS jenis_item
        FROM pemesanan p
        JOIN users u ON p.user_id = u.user_id
        JOIN items i ON p.item_id = i.item_id
        WHERE p.booking_id = ?";

$stmt = $con->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'ID Pemesanan tidak ditemukan']);
    exit;
}

$pemesanan = $result->fetch_assoc();

$harga_sewa = $pemesanan['harga_sewa'];
$denda_per_hari = $harga_sewa * 0.10; // 10%
$total_denda = $denda_per_hari * $hari_keterlambatan;

$response = [
    'booking_id' => $booking_id,
    'nama_penyewa' => $pemesanan['nama_penyewa'],
    'jenis_item' => $pemesanan['jenis_item'],
    'harga_sewa' => $harga_sewa,
    'total_denda' => $total_denda
];

echo json_encode($response);
?>