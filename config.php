<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'rupin';

$con = new mysqli($host, $user, $pass, $dbname);
if ($con->connect_error) {
    die("Koneksi gagal: " . $con->connect_error);
}
?>
