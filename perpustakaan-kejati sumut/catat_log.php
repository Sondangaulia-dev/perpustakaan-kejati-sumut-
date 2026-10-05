<?php
include 'koneksi.php';

if (isset($_POST['id_buku'])) {
    $id_buku = intval($_POST['id_buku']);
    $query   = "INSERT INTO log_aktivitas_digital (id_buku, jenis_akses) VALUES ('$id_buku', 'Dengar Talking Book')";
    mysqli_query($koneksi, $query);
}
?>