<?php
require "config.php";

// Fungsi untuk hashing password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Data user dummy
$users = [
    [
        'nama'      => 'Admin Utama',
        'email'     => 'admin@gmail.com',
        'password'  => hashPassword('admin123'),
        'role'      => 'admin',
        'status'    => 'aktif',
        'alamat'    => 'Jl. Admin No. 1'
    ],
    [
        'nama'      => 'Penyedia Alat',
        'email'     => 'penyedia@gmail.com',
        'password'  => hashPassword('penyedia123'),
        'role'      => 'penyedia',
        'status'    => 'aktif',
        'alamat'    => 'Jl. Penyedia No. 5'
    ],
    [
        'nama'      => 'Penyewa',
        'email'     => 'penyewa@gmail.com',
        'password'  => hashPassword('penyewa123'),
        'role'      => 'penyewa',
        'status'    => 'aktif',
        'alamat'    => 'Jl. Penyewa No. 10'
    ]
];

// Insert ke database
foreach ($users as $user) {
    $stmt = $con->prepare("INSERT INTO users (nama, email, password, role, status, alamat) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssss",
        $user['nama'],
        $user['email'],
        $user['password'],
        $user['role'],
        $user['status'],
        $user['alamat']
    );

    if ($stmt->execute()) {
        echo "✅ User '{$user['nama']}' berhasil ditambahkan.<br>";
    } else {
        echo "❌ Gagal menambahkan user '{$user['nama']}': " . $stmt->error . "<br>";
    }
}

mysqli_close($con);
?>