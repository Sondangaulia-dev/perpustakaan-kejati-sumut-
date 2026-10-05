<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama      = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nip       = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $satker    = mysqli_real_escape_string($koneksi, $_POST['satker']);
    $keperluan = mysqli_real_escape_string($koneksi, $_POST['keperluan']);

    $query = "INSERT INTO kunjungan (nama_pengunjung, nip_nik, satker_bidang, keperluan) 
              VALUES ('$nama', '$nip', '$satker', '$keperluan')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: presensi.php?status=sukses");
    } else {
        echo "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}
?>