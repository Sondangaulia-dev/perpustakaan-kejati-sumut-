<?php
session_start();
include 'koneksi.php';

// Proteksi Halaman Admin / Login
if (!isset($_SESSION['admin_login'])) {
    header("Location: login.php");
    exit;
}

// Ambil Nama Petugas yang sedang login dari Session
$nama_petugas = $_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Petugas Perpustakaan Kejati';

// Path file logo lokal yang sudah didownload
$path_logo = "logo_kejaksaan.png"; 

// 1. Data Metrics Dashboard Ringkasan
$q_today = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kunjungan WHERE DATE(waktu_kunjungan) = CURDATE()");
$total_today = mysqli_fetch_assoc($q_today)['total'] ?? 0;

$q_month = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kunjungan WHERE MONTH(waktu_kunjungan) = MONTH(CURDATE()) AND YEAR(waktu_kunjungan) = YEAR(CURDATE())");
$total_month = mysqli_fetch_assoc($q_month)['total'] ?? 0;

// 2. Menentukan halaman aktif dari URL (?page=...)
$page = $_GET['page'] ?? 'dashboard';

// 3. Proses Tambah Pengguna Baru (Khusus Halaman Daftar Pengguna)
if (isset($_POST['tambah_pengguna'])) {
    $username     = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $password_raw = $_POST['password'];
    $role         = mysqli_real_escape_string($koneksi, $_POST['role'] ?? 'Petugas');

    // Cek apakah username sudah terdaftar
    $cek_user = mysqli_query($koneksi, "SELECT username FROM users WHERE username = '$username'");

    if ($cek_user && mysqli_num_rows($cek_user) > 0) {
        echo "<script>
                alert('Gagal! Username \"$username\" sudah digunakan. Gunakan username lain.');
                window.location.href='dashboard.php?page=pengguna';
              </script>";
    } else {
        // Hash password
        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        // Cek struktur kolom tabel users untuk menentukan query yang cocok
        $cek_kolom_role = mysqli_query($koneksi, "SHOW COLUMNS FROM users LIKE 'role'");

        if ($cek_kolom_role && mysqli_num_rows($cek_kolom_role) > 0) {
            $query = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$password', '$nama_lengkap', '$role')";
        } else {
            $query = "INSERT INTO users (username, password, nama_lengkap) VALUES ('$username', '$password', '$nama_lengkap')";
        }

        $q_add = mysqli_query($koneksi, $query);

        if ($q_add) {
            echo "<script>
                    alert('Pengguna baru berhasil ditambahkan!');
                    window.location.href='dashboard.php?page=pengguna';
                  </script>";
        } else {
            $error_msg = mysqli_error($koneksi);
            echo "<script>
                    alert('Gagal menambah pengguna. Error: " . addslashes($error_msg) . "');
                    window.location.href='dashboard.php?page=pengguna';
                  </script>";
        }
    }
}

// 4. Proses Hapus Pengguna
if (isset($_GET['hapus'])) {
    $id_hapus = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    
    // Query hapus menggunakan id_user
    $q_del = mysqli_query($koneksi, "DELETE FROM users WHERE id_user = '$id_hapus'");
    if ($q_del) {
        echo "<script>alert('Pengguna berhasil dihapus!'); window.location.href='dashboard.php?page=pengguna';</script>";
    } else {
        echo "<script>alert('Gagal menghapus pengguna: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Kejati Sumut - Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- FITUR GRAFIK: Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { 
            min-height: 100vh; 
            background-color: #f8f9fa; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* PERBAIKAN SIDEBAR */
        .sidebar {
            transition: all 0.3s ease;
            width: 260px; 
            height: fit-content;
            min-height: auto;
            position: sticky;
            top: 0;
            background-color: #0b3b24; /* Hijau Khas Kejati */
            z-index: 1000;
            flex-shrink: 0;
        }

        /* Class saat Sidebar Tersembunyi */
        .sidebar.collapsed {
            margin-left: -260px;
        }

        /* Menu Navigation Item */
        .sidebar .nav-link { 
            color: rgba(255, 255, 255, 0.8); 
            padding: 12px 16px; 
            font-size: 0.95rem;
            border-radius: 8px;
            margin-bottom: 6px;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.2s ease-in-out;
        }

        .sidebar .nav-link i {
            font-size: 1.25rem;
            margin-right: 12px;
        }

        .sidebar .nav-link:hover { 
            color: #ffffff; 
            background-color: rgba(255, 255, 255, 0.1); 
        }

        .sidebar .nav-link.active { 
            color: #ffffff; 
            background-color: #0d6efd; 
            font-weight: 600;
        }

        .main-content { 
            flex: 1; 
            overflow-x: hidden;
        }
        
        /* Header Brand Section */
        .brand-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        .brand-logo-img {
            width: 70px;
            height: auto;
            margin-bottom: 10px;
        }

        .brand-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            margin-top: 3px;
        }

        .topbar-header {
            border-color: #e9ecef !important;
        }

        /* Tampilan Khusus Cetak/Print */
        @media print {
            body * {
                visibility: hidden !important;
            }
            #area-laporan, #area-laporan * {
                visibility: visible !important;
            }
            #area-laporan {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
            }
            .sidebar, .btn-export, .topbar-header {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- MENU SIDEBAR -->
    <div class="sidebar d-flex flex-column p-3 text-white" id="sidebar">
        
        <!-- HEADER SIDEBAR: LOGO DI ATAS & NAMA DI BAWAH -->
        <div class="brand-section mb-3 pt-2">
            <img src="<?= $path_logo; ?>" alt="Logo Kejaksaan" class="brand-logo-img">
            <div class="brand-title">Perpustakaan Kejaksaan Tinggi</div>
            <div class="brand-subtitle">Sumatera Utara</div>
        </div>
        
        <ul class="nav nav-pills flex-column mb-auto">
            <!-- 1. Menu Dashboard -->
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link <?= ($page == 'dashboard') ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            
            <!-- 2. Menu Buku Tamu / Presensi -->
            <li class="nav-item">
                <a href="dashboard.php?page=presensi" class="nav-link <?= ($page == 'presensi') ? 'active' : ''; ?>">
                    <i class="bi bi-person-check me-2"></i> Form Presensi
                </a>
            </li>
            
            <!-- 3. Menu Sirkulasi Buku -->
            <li class="nav-item">
                <a href="dashboard.php?page=sirkulasi" class="nav-link <?= ($page == 'sirkulasi') ? 'active' : ''; ?>">
                    <i class="bi bi-journal-arrow-up me-2"></i> Sirkulasi Buku
                </a>
            </li>

            <!-- 4. Menu Daftar Pengguna/Petugas -->
            <li class="nav-item">
                <a href="dashboard.php?page=pengguna" class="nav-link <?= ($page == 'pengguna') ? 'active' : ''; ?>">
                    <i class="bi bi-people me-2"></i> Daftar Pengguna
                </a>
            </li>

            <!-- 5. Menu Informasi Aplikasi -->
            <li class="nav-item">
                <a href="dashboard.php?page=info" class="nav-link <?= ($page == 'info') ? 'active' : ''; ?>">
                    <i class="bi bi-info-circle me-2"></i> Informasi Aplikasi
                </a>
            </li>
        </ul>
        
        <hr class="border-light">
        <div>
            <a href="logout.php" class="btn btn-outline-light w-100"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
        </div>
    </div>

    <!-- AREA KONTEN UTAMA -->
    <div class="main-content p-4">

        <!-- TOPBAR NAVIGATION -->
        <div class="topbar-header d-flex justify-content-between align-items-center bg-white p-3 mb-4 rounded-3 shadow-sm border">
            
            <!-- SISI KIRI: TOMBOL TOGGLE & JUDUL -->
            <div class="d-flex align-items-center gap-3">
                <!-- Tombol Toggle Hamburger Sidebar -->
                <button class="btn btn-light border-0 shadow-sm px-3 py-2 rounded-3" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                
                <div>
                    <!-- Judul Utama -->
                    <h5 class="fw-bold mb-0 text-success" style="color: #0b3b24 !important; letter-spacing: 0.5px;">
                        BUKU TAMU DIGITAL PERPUSTAKAAN KEJATI SUMUT
                    </h5>
                    <!-- Tanggal Otomatis Dinamis -->
                    <small class="text-muted fw-medium">
                        <?= date('l, d F Y'); ?>
                    </small>
                </div>
            </div>

            <!-- SISI KANAN: PROFIL USER -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle hide-arrow" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="text-end leading-tight">
                        <div class="fw-bold text-dark lh-1" style="font-size: 0.95rem;">
                            <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Petugas'); ?>
                        </div>
                        <small class="text-muted" style="font-size: 0.8rem;">
                            <?= htmlspecialchars($_SESSION['role'] ?? 'Admin'); ?>
                        </small>
                    </div>
                    
                    <!-- Avatar Circle -->
                    <div class="avatar-circle rounded-circle d-flex align-items-center justify-content-center border border-2 border-success text-success" style="width: 42px; height: 42px; background-color: #f8f9fa;">
                        <i class="bi bi-person-fill fs-4"></i>
                    </div>
                </a>

                <!-- Menu Dropdown saat Profil Diklik -->
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2" aria-labelledby="dropdownUser">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="dashboard.php?page=pengguna">
                            <i class="bi bi-person-gear text-primary"></i>
                            <span>Kelola Profil</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <?php
        switch ($page) {
            case 'sirkulasi':
                if (file_exists('sirkulasi.php')) {
                    include 'sirkulasi.php';
                } else {
                    echo "<div class='alert alert-danger'>File sirkulasi.php tidak ditemukan di root folder.</div>";
                }
                break;

            case 'presensi':
                if (file_exists('presensi.php')) {
                    include 'presensi.php';
                } else {
                    echo "<h3 class='mb-4'>Form Presensi Digital</h3><p class='text-muted'>Halaman presensi dipanggil di sini.</p>";
                }
                break;

            case 'pengguna':
                ?>
                <!-- HALAMAN DAFTAR PENGGUNA -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">Daftar Pengguna / Petugas</h3>
                        <p class="text-muted mb-0">Kelola akun petugas perpustakaan Kejati</p>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser">
                        <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
                    </button>
                </div>

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Lengkap</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $q_users = mysqli_query($koneksi, "SELECT * FROM users WHERE username != '' ORDER BY id_user DESC");
                                    
                                    if ($q_users && mysqli_num_rows($q_users) > 0) {
                                        while ($u = mysqli_fetch_assoc($q_users)) {
                                            ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= htmlspecialchars($u['nama_lengkap']); ?></td>
                                                <td><?= htmlspecialchars($u['username']); ?></td>
                                                <td><?= htmlspecialchars($u['role'] ?? 'Petugas'); ?></td>
                                                <td>
                                                    <a href="dashboard.php?page=pengguna&hapus=<?= $u['id_user']; ?>" onclick="return confirm('Yakin ingin menghapus pengguna ini?')" class="btn btn-sm btn-danger">Hapus</a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center text-muted py-3'>Belum ada data pengguna.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- MODAL TAMBAH PENGGUNA -->
                <div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="POST" action="dashboard.php?page=pengguna">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Tambah Pengguna Baru</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" name="nama_lengkap" class="form-control" required placeholder="Contoh: Ahmad Subagja">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" required placeholder="Contoh: ahmad123">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Role / Jabatan</label>
                                        <select name="role" class="form-select">
                                            <option value="Petugas">Petugas</option>
                                            <option value="Admin">Admin</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" name="tambah_pengguna" class="btn btn-primary">Simpan Pengguna</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php
                break;

            case 'info':
                ?>
               <!-- HALAMAN INFORMASI APLIKASI -->
                <div class="mb-4">
                    <h3 class="fw-bold mb-1">Informasi Aplikasi</h3>
                    <p class="text-muted mb-0">Detail spesifikasi dan identitas sistem Perpustakaan Digital</p>
                </div>

                <!-- GRID 2 KOLOM KARTU BERDAMPINGAN -->
                <div class="row g-4 mb-4">
                    
                    <!-- KARTU KIRI: IDENTITAS APLIKASI -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                            <div class="card-header text-white py-3 px-4 d-flex align-items-center gap-2" style="background-color: #0b3b24 !important;">
                                <i class="bi bi-info-circle-fill fs-5"></i>
                                <h6 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 0.8px;">Identitas Aplikasi</h6>
                            </div>
                            <div class="card-body p-4 bg-white">
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted fw-semibold py-2 ps-0" style="width: 35%;">Nama Aplikasi</td>
                                                <td class="fw-bold text-dark py-2">
                                                    BUKU TAMU DIGITAL PERPUSTAKAAN KEJAKSAAN TINGGI SUMATERA UTARA
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold py-2 ps-0">Instansi</td>
                                                <td class="fw-semibold text-dark py-2">Kejaksaan Tinggi Sumatera Utara (Kejati Sumut)</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold py-2 ps-0">Developer</td>
                                                <td class="fw-semibold text-dark py-2">
                                                    <span class="badge bg-light text-dark border px-2 py-1">Tim Magang AMIK MEDICOM</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold py-2 ps-0">Versi Sistem</td>
                                                <td class="py-2">
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold rounded-pill px-3 py-1">
                                                        v2.0.0
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted fw-semibold py-2 ps-0">Teknologi</td>
                                                <td class="py-2">
                                                    <div class="d-flex flex-wrap gap-1">
                                                        <span class="badge bg-primary">PHP 8</span>
                                                        <span class="badge bg-warning text-dark">MySQL</span>
                                                        <span class="badge bg-info text-white">Bootstrap 5</span>
                                                        <span class="badge bg-danger">Chart.js</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KARTU KANAN: FITUR UTAMA & LISENSI -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                            <div class="card-header text-white py-3 px-4 d-flex align-items-center gap-2" style="background-color: #0b3b24 !important;">
                                <i class="bi bi-stars fs-5"></i>
                                <h6 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 0.8px;">Fitur & Hak Cipta</h6>
                            </div>
                            <div class="card-body p-4 bg-white d-flex flex-column justify-content-between">
                                <!-- Fitur Utama -->
                                <div class="mb-3">
                                    <h6 class="fw-bold text-dark mb-3">Fitur Utama Sistem</h6>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center bg-light p-2.5 rounded border">
                                            <i class="bi bi-check-circle-fill text-success me-2 fs-6"></i>
                                            <span class="small fw-semibold">Presensi Buku Tamu Digital</span>
                                        </div>
                                        <div class="d-flex align-items-center bg-light p-2.5 rounded border">
                                            <i class="bi bi-check-circle-fill text-success me-2 fs-6"></i>
                                            <span class="small fw-semibold">Sirkulasi Peminjaman & Pengembalian Buku</span>
                                        </div>
                                        <div class="d-flex align-items-center bg-light p-2.5 rounded border">
                                            <i class="bi bi-check-circle-fill text-success me-2 fs-6"></i>
                                            <span class="small fw-semibold">Manajemen Akun Pengguna / Petugas</span>
                                        </div>
                                        <div class="d-flex align-items-center bg-light p-2.5 rounded border">
                                            <i class="bi bi-check-circle-fill text-success me-2 fs-6"></i>
                                            <span class="small fw-semibold">Laporan Analitik & Ekspor Laporan (Word/Print)</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hak Cipta & Lisensi -->
                                <div class="p-3 bg-light rounded border border-warning-subtle">
                                    <h6 class="fw-bold text-dark small mb-1">
                                        <i class="bi bi-shield-lock-fill text-warning me-1"></i> Lisensi & Penggunaan
                                    </h6>
                                    <p class="text-muted small mb-0" style="font-size: 0.825rem;">
                                        Sistem ini dikembangkan khusus untuk operasional internal Perpustakaan Digital Kejaksaan Tinggi Sumatera Utara. Dilarang menggandakan atau mendistribusikan ulang tanpa izin resmi.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- FOOTER HALAMAN -->
                <div class="text-center py-2 mb-3">
                    <small class="text-muted">
                        &copy; <?= date('Y'); ?> Perpustakaan Digital Kejaksaan Tinggi Sumatera Utara. All Rights Reserved.
                    </small>
                </div>

                <?php
                break;

            case 'dashboard':
            default:
                // Query untuk mengambil 5 data kunjungan terbaru
                $q_riwayat = mysqli_query($koneksi, "SELECT * FROM kunjungan ORDER BY waktu_kunjungan DESC LIMIT 5");

                // QUERY DATA GRAFIK 7 HARI TERAKHIR
                $dates = [];
                $counts = [];
                for ($i = 6; $i >= 0; $i--) {
                    $d = date('Y-m-d', strtotime("-$i days"));
                    $dates[] = date('d M', strtotime($d));
                    
                    $q_chart = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kunjungan WHERE DATE(waktu_kunjungan) = '$d'");
                    $counts[] = mysqli_fetch_assoc($q_chart)['total'] ?? 0;
                }
                ?>
                <div class="mb-4">
                    <h3 class="fw-bold mb-1">Dashboard Analitik Pengguna</h3>
                    <p class="text-muted mb-0">Selamat datang, <strong><?= htmlspecialchars($nama_petugas); ?></strong></p>
                </div>

                <!-- WADAH KONTEN YANG AKAN DI-EKSPOR KE WORD/PRINT -->
                <div id="area-laporan">
                    <!-- CARDS METRICS -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="card bg-success text-white p-3 shadow-sm border-0 rounded-3">
                                <h6 class="text-white-50">Pengunjung Hari Ini</h6>
                                <h2 class="fw-bold mb-0"><?= $total_today; ?> Orang</h2>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card bg-primary text-white p-3 shadow-sm border-0 rounded-3">
                                <h6 class="text-white-50">Pengunjung Bulan Ini</h6>
                                <h2 class="fw-bold mb-0"><?= $total_month; ?> Orang</h2>
                            </div>
                        </div>
                    </div>

                    <!-- CARD GRAFIK KUNJUNGAN -->
                    <div class="card shadow-sm border-0 rounded-3 mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title fw-bold mb-0"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Grafik Statistik Kunjungan (7 Hari Terakhir)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="grafikKunjungan" style="max-height: 300px;"></canvas>
                        </div>
                    </div>

                    <!-- TABEL RIWAYAT KUNJUNGAN TERBARU -->
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0">Riwayat Kunjungan Terbaru</h5>
                            
                            <div class="btn-export d-flex gap-2">
                                <button onclick="window.print()" class="btn btn-secondary btn-sm">
                                    <i class="bi bi-printer me-1"></i> Cetak / Print
                                </button>
                                <button onclick="exportToWord('area-laporan', 'Laporan_Kunjungan_Kejati')" class="btn btn-primary btn-sm">
                                    <i class="bi bi-file-earmark-word me-1"></i> Ekspor Word
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama</th>
                                            <th>NIP / NIK</th>
                                            <th>Satker / Bidang</th>
                                            <th>Keperluan</th>
                                            <th>Waktu</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($q_riwayat && mysqli_num_rows($q_riwayat) > 0): ?>
                                            <?php while ($r = mysqli_fetch_assoc($q_riwayat)): ?>
                                                <tr>
                                                    <td><strong><?= htmlspecialchars($r['nama_pengunjung']); ?></strong></td>
                                                    <td><?= htmlspecialchars($r['nip_nik']); ?></td>
                                                    <td><?= htmlspecialchars($r['satker_bidang']); ?></td>
                                                    <td><?= htmlspecialchars($r['keperluan']); ?></td>
                                                    <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($r['waktu_kunjungan'])); ?></small></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">Belum ada data kunjungan terbaru.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                break;
        }
        ?>

    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript Toggle Sidebar & Chart.js -->
<script>
    // 1. Script Toggle Sidebar Hamburger
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }

    // 2. Script Render Chart.js (Khusus Halaman Dashboard)
    <?php if ($page == 'dashboard'): ?>
    const ctx = document.getElementById('grafikKunjungan');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($dates); ?>,
                datasets: [{
                    label: 'Jumlah Pengunjung',
                    data: <?= json_encode($counts); ?>,
                    backgroundColor: '#0b3b24',
                    borderColor: '#0b3b24',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>

    // 3. Script Ekspor ke Word (HTML to Doc)
    function exportToWord(elementId, filename = '') {
        var html = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>" +
            "<head><meta charset='utf-8'><title>Export Word</title><style>table {border-collapse: collapse; width: 100%;} table, th, td {border: 1px solid black; padding: 8px;}</style></head><body>";
        var content = document.getElementById(elementId).cloneNode(true);
        
        // Hapus elemen tombol dari ekspor Word
        var buttons = content.querySelectorAll('.btn-export, button');
        buttons.forEach(btn => btn.remove());

        html += content.innerHTML;
        html += "</body></html>";

        var blob = new Blob(['\ufeff', html], {
            type: 'application/msword'
        });
        
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url;
        a.download = filename ? filename + '.doc' : 'Laporan.doc';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
</script>

</body>
</html>