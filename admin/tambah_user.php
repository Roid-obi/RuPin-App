<?php
include('../session.php');
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $con->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $email, $password, $role);
    $stmt->execute();
    header("Location: kelola_user.php");
}
?>

<h2>Tambah Pengguna</h2>
<form method="post">
    <input name="nama" placeholder="Nama"><br>
    <input name="email" placeholder="Email"><br>
    <input name="password" type="password" placeholder="Password"><br>
    <select name="role">
        <option value="penyewa">Penyewa</option>
        <option value="penyedia">Penyedia</option>
        <option value="admin">Admin</option>
    </select><br>
    <button type="submit">Simpan</button>
</form>
