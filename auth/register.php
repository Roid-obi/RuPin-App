<?php
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $alamat = $_POST['alamat'];
    $role = $_POST['role'];

    $sql = "INSERT INTO users (nama, email, password, role, status, alamat) 
            VALUES (?, ?, ?, ?, 'aktif', ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssss", $nama, $email, $password, $role, $alamat);

    if ($stmt->execute()) {
        header("Location: login.php?msg=daftar_berhasil");
        exit;
    } else {
        $error = "Pendaftaran gagal! Mungkin email sudah digunakan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Rupin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../styles/auth.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="../index.php">Rupin</a>
  </div>
</nav>

<!-- Register Form -->
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm p-4" style="width: 100%; max-width: 500px;">
        <h3 class="mb-4 text-center text-primary">Registrasi Akun</h3>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" name="nama" required placeholder="Masukkan nama lengkap">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required placeholder="Masukkan email">
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <input type="text" class="form-control" name="alamat" required placeholder="Masukkan alamat">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required placeholder="Masukkan password">
            </div>
            <div class="mb-3">
                <label class="form-label">Pilih Role</label>
                <select name="role" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="penyewa">Penyewa</option>
                    <option value="penyedia">Penyedia</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Daftar</button>
        </form>

        <div class="mt-3 text-center">
            <small>Sudah punya akun? <a href="login.php">Login</a></small>
        </div>
    </div>
</div>

</body>
</html>
