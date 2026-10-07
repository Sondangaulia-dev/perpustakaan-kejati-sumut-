<?php
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

$id = $_GET['id'] ?? 1;
$stmt = $db->prepare("SELECT * FROM produk_hukum WHERE id = :id");
$stmt->execute([':id' => $id]);
$produk = $stmt->fetch(PDO::FETCH_ASSOC);

// Urutan tahapan
$tahapan = ['Drafting', 'Revisi Legal', 'Paraf Asisten', 'Tanda Tangan Walikota', 'Selesai/Publish'];
$currentIndex = array_search($produk['status'], $tahapan);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Timeline Tracker - <?= htmlspecialchars($produk['judul_rancangan']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stepper { display: flex; justify-content: space-between; position: relative; margin-bottom: 30px; }
        .stepper::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 4px; background: #e0e0e0; z-index: 1; transform: translateY(-50%); }
        .step-item { z-index: 2; background: #fff; padding: 0 10px; text-align: center; }
        .step-icon { width: 40px; height: 40px; border-radius: 50%; background: #e0e0e0; color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px; font-weight: bold; }
        .step-item.active .step-icon { background: #0d6efd; }
        .step-item.completed .step-icon { background: #198754; }
    </style>
</head>
<body class="bg-light">
<div class="container my-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="mb-0">Pelacakan Berkas: <strong><?= htmlspecialchars($produk['judul_rancangan']); ?></strong></h5>
        </div>
        <div class="card-body my-4">
            
            <!-- Stepper Visual Component -->
            <div class="stepper">
                <?php foreach ($tahapan as $index => $step): 
                    $class = '';
                    if ($index < $currentIndex) $class = 'completed';
                    elseif ($index == $currentIndex) $class = 'active';
                ?>
                <div class="step-item <?= $class; ?>">
                    <div class="step-icon"><?= $index + 1; ?></div>
                    <div class="small fw-bold"><?= $step; ?></div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
</div>
</body>
</html>