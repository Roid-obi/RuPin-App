-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Jul 2025 pada 10.50
-- Versi server: 10.4.27-MariaDB
-- Versi PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rupin`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` enum('menunggu','disetujui','ditolak','selesai') DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jumlah_hari` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `booking`
--

INSERT INTO `booking` (`booking_id`, `item_id`, `user_id`, `status`, `tanggal`, `jumlah_hari`) VALUES
(1, 1, 3, 'disetujui', '2025-06-30', 2),
(2, 2, 3, 'ditolak', '2025-07-01', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `tipe` enum('ruang','alat') DEFAULT NULL,
  `harga_sewa` double DEFAULT NULL,
  `lokasi` varchar(100) DEFAULT NULL,
  `status` enum('tersedia','tidak tersedia') DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `items`
--

INSERT INTO `items` (`item_id`, `user_id`, `nama`, `tipe`, `harga_sewa`, `lokasi`, `status`, `gambar`, `deskripsi`) VALUES
(1, 2, 'Proyektor Epson', 'alat', 75000, 'Ruang AVA', 'tersedia', '686273666c818.jpg', 'Proyektor berkualitas HD untuk presentasi.'),
(2, 2, 'Laptop Lenovo', 'alat', 12000, 'Sukiharjo Mesen', 'tersedia', '6862739294f88.webp', 'Sangat bagus\\r\\n\\r\\nSesifikasi dewa'),
(3, 2, 'Rumah Hantu', 'ruang', 1000, 'Belakang Kamus Mesen', 'tersedia', '686273c5e1c8b.jpg', 'Ruma yang sudah lama tidak dihuni');

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_pembayaran`
--

CREATE TABLE `laporan_pembayaran` (
  `id` int(11) NOT NULL,
  `bulan` varchar(20) DEFAULT NULL,
  `tahun` int(11) DEFAULT NULL,
  `total` double DEFAULT NULL,
  `jumlah_transaksi` int(11) DEFAULT NULL,
  `waktu_dibuat` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `laporan_pembayaran`
--

INSERT INTO `laporan_pembayaran` (`id`, `bulan`, `tahun`, `total`, `jumlah_transaksi`, `waktu_dibuat`) VALUES
(9, 'June', 2025, 150000, 1, '2025-07-01 22:27:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_pembayaran_detail`
--

CREATE TABLE `laporan_pembayaran_detail` (
  `id` int(11) NOT NULL,
  `laporan_id` int(11) DEFAULT NULL,
  `pembayaran_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `laporan_pembayaran_detail`
--

INSERT INTO `laporan_pembayaran_detail` (`id`, `laporan_id`, `pembayaran_id`) VALUES
(5, 9, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `pembayaran_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `jumlah` double DEFAULT NULL,
  `status` enum('belum bayar','menunggu','lunas') DEFAULT NULL,
  `metode` varchar(50) DEFAULT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `bukti` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`pembayaran_id`, `booking_id`, `jumlah`, `status`, `metode`, `tanggal_bayar`, `bukti`) VALUES
(1, 1, 150000, 'lunas', 'transfer', '2025-06-30', 'bukti.jpg'),
(2, 2, 37800, 'menunggu', 'transfer', '2025-07-01', 'bukti_1751382536.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengembalian`
--

CREATE TABLE `pengembalian` (
  `pengembalian_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` enum('tepat waktu','terlambat','hilang/rusak') DEFAULT NULL,
  `denda` double DEFAULT 0,
  `bukti` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengembalian`
--

INSERT INTO `pengembalian` (`pengembalian_id`, `booking_id`, `tanggal_kembali`, `status`, `denda`, `bukti`) VALUES
(2, 1, '2025-07-02', 'hilang/rusak', 12000, 'hantu_full.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT NULL,
  `alamat` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`user_id`, `nama`, `email`, `password`, `role`, `status`, `alamat`) VALUES
(1, 'Admin Utama', 'admin@gmail.com', '$2y$10$AaC6uJNlIEzeLFrYALn57u0tMYCD5SWNDcYD7F4JCTbSk7ZsWQnjW', 'admin', 'aktif', 'Jl. Admin No. 1'),
(2, 'Orang Penyedia', 'penyedia@gmail.com', '$2y$10$GPyZx64MCA.n/.nyyuU5j.63F/LPWZqg8Cfq.mklnGqNZpjSTVVBu', 'penyedia', 'aktif', 'Jl. Penyedia No. 5'),
(3, 'Orang Penyewa', 'penyewa@gmail.com', '$2y$10$lJaM5Rra8bikykLlr2dQneLqBMVsYXxgGKfSUgbCxWxDI/03hXBFm', 'penyewa', 'aktif', 'Jl. Penyewa No. 10'),
(4, 'Super Admin', 'superadmin@gmail.com', '$2y$10$vpzeesiKif407msUqwJ02uipSN4yKEKJflDIV2lHT/goUD2JpkHvC', 'super-admin', 'aktif', 'Isekai No. 1');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `laporan_pembayaran`
--
ALTER TABLE `laporan_pembayaran`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `laporan_pembayaran_detail`
--
ALTER TABLE `laporan_pembayaran_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `laporan_id` (`laporan_id`),
  ADD KEY `pembayaran_id` (`pembayaran_id`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`pembayaran_id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`);

--
-- Indeks untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`pengembalian_id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `laporan_pembayaran`
--
ALTER TABLE `laporan_pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `laporan_pembayaran_detail`
--
ALTER TABLE `laporan_pembayaran_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `pembayaran_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `pengembalian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`),
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Ketidakleluasaan untuk tabel `laporan_pembayaran_detail`
--
ALTER TABLE `laporan_pembayaran_detail`
  ADD CONSTRAINT `laporan_pembayaran_detail_ibfk_1` FOREIGN KEY (`laporan_id`) REFERENCES `laporan_pembayaran` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `laporan_pembayaran_detail_ibfk_2` FOREIGN KEY (`pembayaran_id`) REFERENCES `pembayaran` (`pembayaran_id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`);

--
-- Ketidakleluasaan untuk tabel `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `pengembalian_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
