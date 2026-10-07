<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Perkara Hukum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body class="bg-light">
<div class="container my-4">
    <h3 class="fw-bold mb-4">Data Perkara & Sengketa Hukum</h3>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table id="tabelPerkara" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No. Perkara</th>
                        <th>Judul Kasus</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Sidang Berikutnya</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>01/Pdt.G/2026/PN-Mdn</td>
                        <td>Pemko Medan vs PT Sinar Aset</td>
                        <td>PERDATA</td>
                        <td><span class="badge bg-warning text-dark">Proses Sidang</span></td>
                        <td>
                            <!-- Penanda tanggal sidang terdekat -->
                            <span class="badge bg-danger">3 Hari Lagi (01-09-2026)</span>
                        </td>
                    </tr>
                    <tr>
                        <td>05/TUN/2026/PTUN-Mdn</td>
                        <td>Sengketa Lahan Kecamatan Medan Kota</td>
                        <td>TUN</td>
                        <td><span class="badge bg-info">Banding</span></td>
                        <td><span class="badge bg-secondary">15-09-2026</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document.getElementById('tabelPerkara')).ready(function() {
    $('#tabelPerkara').DataTable({
        "language": {
            "search": "Cari Berkas/Perkara:",
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
        }
    });
});
</script>
</body>
</html>