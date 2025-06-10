<?php
include('session.php');

switch ($_SESSION['role']) {
    case 'penyewa':
        header("Location: penyewa/cari_item.php");
        break;
    case 'penyedia':
        header("Location: penyedia/daftar_pemesanan.php");
        break;
    case 'admin':
        header("Location: admin/konfirmasi_pembayaran.php");
        break;
    default:
        echo "Role tidak dikenali.";
}
