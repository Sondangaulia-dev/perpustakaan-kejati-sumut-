<?php
include 'koneksi.php';
$buku = mysqli_query($koneksi, "SELECT * FROM buku");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Talking Book - Kejati Sumut</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <h3 class="mb-4">Koleksi Talking Book Digital</h3>
    <div class="row">
        <?php while($row = mysqli_fetch_assoc($buku)): ?>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?= $row['judul_buku']; ?></h5>
                        <p class="badge bg-info"><?= $row['kategori']; ?></p>
                        <br>
                        <!-- Audio player simulasi -->
                        <audio controls class="w-100 my-2" onplay="catatLog(<?= $row['id_buku']; ?>)">
                            <source src="sample.mp3" type="audio/mpeg">
                            Browser Anda tidak mendukung player audio.
                        </audio>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
function catatLog(idBuku) {
    // Sinyal AJAX ke catat_log.php secara transparan
    fetch('catat_log.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_buku=' + idBuku
    });
}
</script>
</body>
</html>