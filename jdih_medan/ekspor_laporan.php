<?php
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

$format = $_GET['format'] ?? 'excel';
$query = $db->query("SELECT * FROM produk_hukum ORDER BY id DESC");
$data = $query->fetchAll(PDO::FETCH_ASSOC);

if ($format == 'excel') {
    // Header HTTP untuk memaksa unduh file Excel (.xls)
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_Rekapitulasi_Bagian_Hukum.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    ?>
    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Judul Rancangan</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Tanggal Pengajuan</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $index => $row): ?>
            <tr>
                <td><?= $index + 1; ?></td>
                <td><?= $row['judul_rancangan']; ?></td>
                <td><?= $row['kategori']; ?></td>
                <td><?= $row['status']; ?></td>
                <td><?= $row['created_at']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    exit;
} elseif ($format == 'pdf') {
    // Mode siap cetak browser ke PDF (Window Print)
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Laporan Rekapitulasi Produk Hukum</title>
        <style>
            body { font-family: Arial, sans-serif; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .header { text-align: center; margin-bottom: 20px; }
        </style>
    </head>
    <body onload="window.print()">
        <div class="header">
            <h2>BAGIAN HUKUM KANTOR WALIKOTA MEDAN</h2>
            <p>Laporan Rekapitulasi Status Rancangan Produk Hukum</p>
            <hr>
        </div>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Judul Rancangan</th>
                    <th>Kategori</th>
                    <th>Status</th>
                    <th>Tanggal Pengajuan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $index => $row): ?>
                <tr>
                    <td><?= $index + 1; ?></td>
                    <td><?= htmlspecialchars($row['judul_rancangan']); ?></td>
                    <td><?= $row['kategori']; ?></td>
                    <td><?= $row['status']; ?></td>
                    <td><?= date('d-m-Y', strtotime($row['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit;
}
?>