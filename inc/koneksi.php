<?php
$host = getenv('DB_HOST') ?;   // NAMA CONTAINER menggunakan variabel yang sudah di seting di dalam docker-compose.yml
$user = getenv('DB_USER') ?;
$pass = getenv('DB_PASS') ?;
$db = getenv('DB_NAME') ?;

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");
?>
