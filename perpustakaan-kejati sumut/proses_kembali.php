<<?php
require_once 'koneksi.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $today = date('Y-m-d');
    $status_kembali = 'Dikembalikan';

    // Query khusus mengupdate berdasarkan id_peminjaman
    $query = "UPDATE peminjaman 
              SET status = '$status_kembali', 
                  tanggal_kembali = '$today' 
              WHERE id_peminjaman = '$id'";

    if (mysqli_query($koneksi, $query)) {
        header("Location: dashboard.php?page=sirkulasi&status=sukses_kembali");
        exit();
    } else {
        echo "Gagal memperbarui status pengembalian: " . mysqli_error($koneksi);
    }
} else {
    echo "<script>
            alert('ID Transaksi Peminjaman tidak ditemukan!');
            window.location.href = 'dashboard.php?page=sirkulasi';
          </script>";
    exit();
}
?>