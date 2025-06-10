<?php
include('../session.php');
include('../config.php');

$result = $con->query("SELECT * FROM users ORDER BY role");

?>

<h2>Data Pengguna</h2>
<a href="tambah_user.php">Tambah Pengguna</a>
<table border="1">
    <tr>
        <th>Nama</th>
        <th>Email</th>
        <th>Role</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['nama'] ?></td>
        <td><?= $row['email'] ?></td>
        <td><?= $row['role'] ?></td>
        <td>
            <a href="edit_user.php?id=<?= $row['user_id'] ?>">Edit</a> |
            <a href="hapus_user.php?id=<?= $row['user_id'] ?>" onclick="return confirm('Hapus user?')">Hapus</a>
        </td>
    </tr>
    <?php } ?>
</table>
