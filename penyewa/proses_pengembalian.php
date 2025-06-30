<?php
include('../session.php');
include('../config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'penyewa') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = intval($_POST['booking_id']);
    $upload_dir = '../uploads/pengembalian/';
    $tanggal_kembali = date('Y-m-d');

    // Pastikan direktori upload tersedia
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Validasi dan upload file
    if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] !== 0) {
        die("❌ Bukti pengembalian tidak valid.");
    }

    $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
    $file_name = 'bukti_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    $file_path = $upload_dir . $file_name;

    if (!move_uploaded_file($_FILES['bukti']['tmp_name'], $file_path)) {
        die("❌ Gagal mengunggah file.");
    }

    // Ambil data booking beserta item_id
    $query = "
        SELECT b.tanggal, b.jumlah_hari, b.item_id, p.jumlah
        FROM booking b
        JOIN pembayaran p ON p.booking_id = b.booking_id
        WHERE b.booking_id = ?
    ";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("❌ Data booking tidak ditemukan.");
    }

    $data = $result->fetch_assoc();
    $item_id = $data['item_id'];
    $tanggal_seharusnya_kembali = date('Y-m-d', strtotime($data['tanggal'] . ' + ' . $data['jumlah_hari'] . ' days'));

    // Hitung status keterlambatan
    if ($tanggal_kembali <= $tanggal_seharusnya_kembali) {
        $status = 'tepat waktu';
        $denda = 0;
    } else {
        $status = 'terlambat';
        $denda = $data['jumlah'] * 0.10; // 10% dari total pembayaran
    }

    // Mulai transaksi database
    $con->begin_transaction();

    try {
        // 1. Simpan ke tabel pengembalian
        $insert_pengembalian = $con->prepare("INSERT INTO pengembalian (booking_id, tanggal_kembali, status, denda, bukti) VALUES (?, ?, ?, ?, ?)");
        $insert_pengembalian->bind_param("issds", $booking_id, $tanggal_kembali, $status, $denda, $file_name);
        
        if (!$insert_pengembalian->execute()) {
            throw new Exception("Gagal menyimpan data pengembalian: " . $con->error);
        }

        // 2. Update status item menjadi 'tersedia'
        $update_item = $con->prepare("UPDATE items SET status = 'tersedia' WHERE item_id = ?");
        $update_item->bind_param("i", $item_id);
        
        if (!$update_item->execute()) {
            throw new Exception("Gagal mengubah status item: " . $con->error);
        }

        // 3. Optional: Update status booking menjadi 'selesai' atau 'dikembalikan'
        $update_booking = $con->prepare("UPDATE booking SET status = 'selesai' WHERE booking_id = ?");
        $update_booking->bind_param("i", $booking_id);
        
        if (!$update_booking->execute()) {
            throw new Exception("Gagal mengubah status booking: " . $con->error);
        }

        // Commit transaksi jika semua berhasil
        $con->commit();
        
        // Redirect dengan pesan sukses
        header("Location: status_pemesanan.php?pengembalian=berhasil");
        exit;
        
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        $con->rollback();
        
        // Hapus file yang sudah diupload jika ada error
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        die("❌ " . $e->getMessage());
    }
}
?>