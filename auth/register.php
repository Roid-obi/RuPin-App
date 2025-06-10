<?php
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $alamat = $_POST['alamat'];

    $sql = "INSERT INTO users (nama, email, password, role, status, alamat) 
            VALUES (?, ?, ?, 'penyewa', 'aktif', ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssss", $nama, $email, $password, $alamat);

    if ($stmt->execute()) {
        header("Location: login.php?msg=daftar_berhasil");
        exit;
    } else {
        echo "Pendaftaran gagal!";
    }
}
?>

<!-- HTML Form -->
<h2>Registrasi</h2>
<form method="POST">
    <input type="text" name="nama" placeholder="Nama" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="text" name="alamat" placeholder="Alamat" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Daftar</button>
</form>
<a href="login.php">Sudah punya akun? Login</a>
