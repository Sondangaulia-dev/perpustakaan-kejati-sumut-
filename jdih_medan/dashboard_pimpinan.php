<?php
require_once 'database.php';

$database = new Database();
$db = $database->getConnection();

// 1. Hitung total draf dalam proses
$queryDraf = $db->query("SELECT COUNT(*) as total FROM produk_hukum WHERE status != 'Selesai/Publish'");
$totalDraf = $queryDraf->fetch(PDO::FETCH_ASSOC)['total'];

// 2. Hitung statistik untuk grafik
$queryGrafik = $db->query("SELECT status, COUNT(*) as jumlah FROM produk_hukum GROUP BY status");
$grafikData = $queryGrafik->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$counts = [];
foreach ($grafikData as $g) {
    $labels[] = $g['status'];
    $counts[] = $g['jumlah'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Pimpinan - Bagian Hukum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container my-4">
    <h3 class="fw-bold mb-4"><i class="bi bi-speedometer2"></i> Executive Dashboard - Bagian Hukum</h3>

    <!-- Widget Ringkasan Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Draf Dalam Proses</h6>
                        <h2 class="display-6 fw-bold mb-0"><?= $totalDraf; ?></h2>
                    </div>
                    <i class="bi bi-file-earmark-text fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-dark shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Perkara Aktif Sidang</h6>
                        <h2 class="display-6 fw-bold mb-0">5</h2>
                    </div>
                    <i class="bi bi-gavel fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white shadow-sm border-0">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase mb-1">Produk Hukum Selesai</h6>
                        <h2 class="display-6 fw-bold mb-0">12</h2>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Status Produk Hukum -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold">Statistik Status Perwal / Perda</div>
                <div class="card-body">
                    <canvas id="statusChart" height="130"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white fw-bold">Aksi Cepat</div>
                <div class="card-body d-grid gap-2">
                    <a href="ekspor_laporan.php?format=pdf" class="btn btn-outline-danger"><i class="bi bi-file-pdf"></i> Unduh Laporan PDF</a>
                    <a href="ekspor_laporan.php?format=excel" class="btn btn-outline-success"><i class="bi bi-file-excel"></i> Unduh Laporan Excel</a>
                    <a href="manajemen_perkara.php" class="btn btn-outline-primary"><i class="bi bi-journal-text"></i> Kelola Perkara Hukum</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('statusChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels); ?>,
        datasets: [{
            label: 'Jumlah Dokumen',
            data: <?= json_encode($counts); ?>,
            backgroundColor: ['#0d6efd', '#ffc107', '#fd7e14', '#198754']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});
</script>
</body>
</html>