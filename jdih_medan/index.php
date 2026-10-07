<?php
// index.php
require_once 'database.php';
require_once 'produk_hukum.php';

$database = new Database();
$db = $database->getConnection();
$produk = new ProdukHukum($db);
$data = $produk->readAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>JDIH Plus & Monitoring - Bagian Hukum Pemko Medan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">Smart Legal Monitoring | Pemko Medan</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link active" href="index.php">Draf Produk Hukum</a></li>
        <li class="nav-item"><a class="nav-link" href="dashboard_pimpinan.php">Dashboard Pimpinan</a></li>
        <li class="nav-item"><a class="nav-link" href="manajemen_perkara.php">Manajemen Perkara</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-4">
    

<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Daftar Pelacakan Produk Hukum (Perwal / Perda)</h2>
        <a href="tambah_produk.php" class="btn btn-primary">+ Ajukan Draf Baru</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Judul Rancangan</th>
                        <th>Kategori</th>
                        <th>Status Saat Ini</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $data->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><strong><?= htmlspecialchars($row['judul_rancangan']); ?></strong></td>
                        <td><span class="badge bg-secondary"><?= $row['kategori']; ?></span></td>
                        <td>
                            <?php 
                                $badge = 'bg-warning text-dark';
                                if($row['status'] == 'Selesai/Publish') $badge = 'bg-success';
                                elseif($row['status'] == 'Revisi Legal') $badge = 'bg-danger';
                            ?>
                            <span class="badge <?= $badge; ?>"><?= $row['status']; ?></span>
                        </td>
                        <td><?= date('d-m-Y H:i', strtotime($row['created_at'])); ?></td>
                        <td>
<a href="tracker_view.php?id=<?= $row['id']; ?>" class="btn btn-info btn-sm text-white me-1">Stepper Visual</a>
<a href="detail_produk.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm text-dark">Update Progress</a>                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>