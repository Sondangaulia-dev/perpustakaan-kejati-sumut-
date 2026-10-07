<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Sesuaikan koneksi database kamu
$koneksi = mysqli_connect("localhost", "root", "", "db_jdih_medan");

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

$pesan = "";

// Proses pengiriman form
if (isset($_POST['submit'])) {
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul_rancangan']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $tanggal  = date('Y-m-d');
    $status   = "Draf Diajukan";

    // Handling File Upload
    $filename = $_FILES['berkas']['name'];
    $tmp_name = $_FILES['berkas']['tmp_name'];
    $file_err = $_FILES['berkas']['error'];

    if ($file_err === 0) {
        $folder_tujuan = "uploads/";
        
        // Buat folder jika belum ada
        if (!file_exists($folder_tujuan)) {
            mkdir($folder_tujuan, 0777, true);
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $nama_file_baru = time() . '_' . uniqid() . '.' . $ext;
        $target_path = $folder_tujuan . $nama_file_baru;

        if (move_uploaded_file($tmp_name, $target_path)) {
            // Sesuaikan nama tabel & kolom di database kamu
            $query = "INSERT INTO produk_hukum (judul_rancangan, kategori, status, tanggal_pengajuan, berkas) 
                      VALUES ('$judul', '$kategori', '$status', '$tanggal', '$nama_file_baru')";
            
                echo "<script>alert('Draf berhasil diajukan!'); window.location='index.php';</script>";
                exit;
            } else {
                $pesan = "Gagal menyimpan ke database: " . mysqli_error($koneksi);
            }
        } else {
            $pesan = "Gagal mengunggah berkas ke server.";
        }
    } else {
        $pesan = "Terjadi kesalahan pada file yang diunggah. Kode error: " . $file_err;
    }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ajukan Draf Baru | Smart Legal Monitoring</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">+ Ajukan Draf Produk Hukum Baru</h4>
        </div>
        <div class="card-body">

            <?php if (!empty($pesan)): ?>
                <div class="alert alert-danger"><?= $pesan; ?></div>
            <?php endif; ?>

            <form action="tambah_produk.php" method="POST" enctype="multipart/form-data">
                
                <!-- Input Judul Rancangan -->
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Judul Rancangan</label>
                    <input type="text" name="judul_rancangan" class="form-control" placeholder="Masukkan judul Perwal / Perda..." required>
                </div>

                <!-- Input Kategori -->
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Kategori</label>
                    <select name="kategori" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Perwal">Perwal (Peraturan Walikota)</option>
                        <option value="Perda">Perda (Peraturan Daerah)</option>
                        <option value="SK Walikota">SK Walikota (Surat Keterangan Walikota)</option>
                    </select>
                </div>

                <!-- Input Berkas / Gambar -->
                <div class="mb-3">
                    <label class="form-label font-weight-bold">Unggah Berkas / Dokumen (PDF / Gambar)</label>
                    <input type="file" name="berkas" class="form-control" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan & Ajukan</button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>