<?php
include 'koneksi.php';

if (!isset($_SESSION['admin_login'])) {
    die("Akses ditolak.");
}

$data = mysqli_query($koneksi, "SELECT * FROM kunjungan ORDER BY waktu_kunjungan DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kunjungan Perpustakaan Kejati Sumut</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; }
    </style>
</head>
<body onload="window.print()"> <!-- Otomatis memicu dialog Print / Save to PDF browser -->

    <div class="header">
        <h2>KEJAKSAAN TINGGI SUMATERA UTARA</h2>
        <h3>LAPORAN KUNJUNGAN PERPUSTAKAAN DIGITAL</h3>
        <hr>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Waktu Kunjungan</th>
                <th>Nama Pengunjung</th>
                <th>NIP / NIK</th>
                <th>Satker / Bidang</th>
                <th>Keperluan</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; while($row = mysqli_fetch_assoc($data)): ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['waktu_kunjungan']; ?></td>
                <td><?= $row['nama_pengunjung']; ?></td>
                <td><?= $row['nip_nik'] ? $row['nip_nik'] : '-'; ?></td>
                <td><?= $row['satker_bidang']; ?></td>
                <td><?= $row['keperluan']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>