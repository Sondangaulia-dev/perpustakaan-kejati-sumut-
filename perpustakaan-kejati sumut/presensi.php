<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Tamu - Perpustakaan Kejati Sumut</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .card-header { background-color: #0b5345; color: white; }
        .logo-box {
            width: 85px;
            height: 85px;
            margin: 0 auto 10px auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0">
                <div class="card-header text-center py-4">
                   <div class="logo-box">
    <img src="logo_kejaksaan.png" alt="Logo Kejaksaan" style="max-width: 100%; height: auto;">
</div>   

                    <h4 class="mb-0 fw-bold">Buku Tamu Digital</h4>
                    <small>Perpustakaan Kejaksaan Tinggi Sumatera Utara</small>
                </div>
                <div class="card-body p-4">
                    <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Berhasil!</strong> Kunjungan Terverivikasi, Terimakasih.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="simpan_presensi.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama lengkap">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIP / NIK / NIM</label>
                            <input type="text" name="nip" class="form-control" placeholder="Isi jika pegawai/staf/mahasiswa">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Satuan Kerja / Bidang *</label>
                            <select name="satker" class="form-select" required>
                                <option value="">-- Pilih Bidang / Asal --</option>
                                <option value="Bidang Intelijen">Bidang Intelijen</option>
                                <option value="Bidang Pidum">Bidang Pidum (Tindak Pidana Umum)</option>
                                <option value="Bidang Pidsus">Bidang Pidsus (Tindak Pidana Khusus)</option>
                                <option value="Bidang Datun">Bidang Datun (Perdata & Tata Usaha Negara)</option>
                                <option value="Bidang Pembinaan">Bidang Pembinaan</option>
                                <option value="Bidang Pembinaan">Bidang Pidana Militer</option>
                                <option value="Bidang Pembinaan">Bidang Pengawasan</option>
                                <option value="Bidang Pembinaan">Bagian Tata Usaha</option>
                                <option value="Masyarakat / Umum">Mahasiswa / Umum</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Keperluan *</label>
                            <input type="text" name="keperluan" class="form-control" required placeholder="Contoh: Membaca, Riset Kasus, Pinjam Buku, Berkunjung">
                        </div>
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold">Submit Kehadiran</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>