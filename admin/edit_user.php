<?php
include('../session.php');
include('../config.php');

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $con->prepare("UPDATE users SET nama=?, email=?, role=? WHERE user_id=?");
    $stmt->bind_param("sssi", $nama, $email, $role, $id);
    $stmt->execute();

    header("Location: kelola_user.php");
} else {
    $stmt = $con->prepare("SELECT * FROM users WHERE user_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
}
?>

<h2>Edit Pengguna</h2>
<form method="post">
    <input name="nama" value="<?= $user['nama'] ?>"><br>
    <input name="email" value="<?= $user['email'] ?>"><br>
    <select name="role">
        <option value="penyewa" <?= $user['role']=='penyewa'?'selected':'' ?>>Penyewa</option>
        <option value="penyedia" <?= $user['role']=='penyedia'?'selected':'' ?>>Penyedia</option>
        <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
    </select><br>
    <button type="submit">Simpan</button>
</form>
