<?php
// detail_produk.php
require_once 'config/database.php';
require_once 'classes/ProdukHukum.php';

$id = $_GET['id'];
$database = new Database();
$db = $database->getConnection();
$produk = new ProdukHukum($db);

$detail = $produk->readOne($id);
$logs = $produk->getLogs($id);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail & Tracking Process</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4">
    <a href="index.php" class="btn btn-outline-secondary mb-3">&laquo; Kembali ke Dashboard</a>

    <div class="row">
        <!-- Info Detail & Update Form -->
        <div class="col-md-5">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h4><?= htmlspecialchars($detail['judul_rancangan']); ?></h4>
                    <p><strong>Kategori:</strong> <?= $detail['kategori']; ?></p>
                    <p><strong>Status Saat Ini:</strong> <span class="badge bg-primary"><?= $detail['status']; ?></span></p>
                    <p><strong>Berkas:</strong> <a href="uploads/<?= $detail['file_draft']; ?>" target="_blank">Unduh Draf</a></p>
                </div>
            </div>

            <!-- Form Update Status (Untuk Staf/Pimpinan) -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">Update Status Progress</div>
                <div class="card-body">
                    <form action="update_status.php" method="POST">
                        <input type="hidden" name="id" value="<?= $detail['id']; ?>">
                        <div class="mb-3">
                            <label class="form-label">Tahapan Status Baru</label>
                            <select name="status_baru" class="form-select">
                                <option value="Drafting">1. Drafting</option>
                                <option value="Revisi Legal">2. Revisi Legal Bagian Hukum</option>
                                <option value="Paraf Asisten">3. Paraf Asisten / Sekda</option>
                                <option value="Tanda Tangan Walikota">4. Tanda Tangan Walikota</option>
                                <option value="Selesai/Publish">5. Selesai / Publish</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan Revisi / Keterangan</label>
                            <textarea name="catatan" class="form-control" rows="3" required placeholder="Ketik catatan revisi atau keterangan berkas..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 fw-bold">Perbarui Progress Status</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Timeline Log Riwayat -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">Riwayat Progress Tracking (Audit Log)</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php if(!empty($logs)): foreach($logs as $log): ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <strong>Status diubah: <span class="text-primary"><?= $log['status_baru']; ?></span></strong>
                                <small class="text-muted"><?= date('d-m-Y H:i', strtotime($log['created_at'])); ?></small>
                            </div>
                            <p class="mb-0 mt-1 text-secondary">"<?= htmlspecialchars($log['catatan']); ?>"</p>
                            <small class="text-muted">Status sebelumnya: <i><?= $log['status_lama']; ?></i></small>
                        </li>
                        <?php endforeach; else: ?>
                            <li class="list-group-item">Belum ada riwayat perbaikan.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>