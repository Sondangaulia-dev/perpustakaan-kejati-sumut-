<?php
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Tangkap data dari form (bisa menggunakan nama 'book_id' atau 'judul_buku' sesuai name pada input HTML)
    $user_id = mysqli_real_escape_string($koneksi, $_POST['user_id']);
    $book_id = mysqli_real_escape_string($koneksi, $_POST['book_id']); // Berisi teks judul buku yang diketik
    $durasi  = (int)$_POST['durasi'];

    // Tanggal pinjam hari ini & hitung tanggal tenggat
    $tanggal_pinjam  = date('Y-m-d');
    $tanggal_tenggat = date('Y-m-d', strtotime("+$durasi days"));
    $status          = 'Dipinjam';

    // Simpan ke database
    $query = "INSERT INTO peminjaman (user_id, book_id, tanggal_pinjam, tanggal_tenggat, status) 
              VALUES ('$user_id', '$book_id', '$tanggal_pinjam', '$tanggal_tenggat', '$status')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: dashboard.php?page=sirkulasi&status=sukses_pinjam");
        exit();
    } else {
        echo "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
} else {
    header("Location: dashboard.php?page=sirkulasi");
    exit();
}
?>