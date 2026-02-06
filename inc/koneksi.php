<?php
$host = getenv('DB_HOST') ?: "database_all";   // NAMA CONTAINER MySQL
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') ?: "root123";
$db = getenv('DB_NAME') ?: "etms_db";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");
?>