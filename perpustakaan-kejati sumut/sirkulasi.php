<?php
if (!isset($koneksi)) {
    include 'koneksi.php';
}

$today = date('Y-m-d');

// 1. Ambil data peminjaman aktif + JOIN ke tabel buku
$query_pinjam = mysqli_query($koneksi, "
    SELECT peminjaman.*, buku.judul_buku 
    FROM peminjaman 
    LEFT JOIN buku ON peminjaman.book_id = buku.id_buku
    WHERE LOWER(peminjaman.status) LIKE '%pinjam%'
    ORDER BY peminjaman.tanggal_tenggat DESC
") or die("Error Query Pinjam: " . mysqli_error($koneksi));

// 2. Ambil riwayat pengembalian + JOIN ke tabel buku
$query_riwayat = mysqli_query($koneksi, "
    SELECT peminjaman.*, buku.judul_buku 
    FROM peminjaman 
    LEFT JOIN buku ON peminjaman.book_id = buku.id_buku
    WHERE LOWER(peminjaman.status) LIKE '%kembali%'
    ORDER BY peminjaman.tanggal_kembali DESC LIMIT 10
") or die("Error Query Riwayat: " . mysqli_error($koneksi));
?>

<div class="container-fluid">
    <h3 class="mb-4">Sirkulasi Peminjaman & Pengembalian Buku</h3>

    <!-- Alert Notifikasi -->
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'sukses_pinjam'): ?>
            <div class="alert alert-success alert-dismissible fade show">Peminjaman berhasil dicatat!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php elseif ($_GET['status'] == 'sukses_kembali'): ?>
            <div class="alert alert-info alert-dismissible fade show">Buku berhasil dikembalikan!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="row">
        <!-- Form Transaksi Peminjaman -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Input Peminjaman</h5>
                </div>
                <div class="card-body">
                    <form action="proses_pinjam.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Peminjam / Anggota</label>
                            <input type="text" name="user_id" class="form-control" placeholder="Ketik nama peminjam..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ID Buku / Judul Buku</label>
                            <input type="text" name="book_id" class="form-control" placeholder="Masukkan ID Buku..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lama Pinjam (Hari)</label>
                            <input type="number" name="durasi" class="form-control" value="7" min="1" max="30" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Simpan Transaksi</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel Peminjaman Aktif -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Daftar Peminjaman Aktif</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Peminjam</th>
                                    <th>Judul Buku</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Tenggat</th>
                                    <th>Tgl Kembali</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($query_pinjam && mysqli_num_rows($query_pinjam) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($query_pinjam)): ?>
                                        <?php $is_overdue = ($today > $row['tanggal_tenggat']); ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($row['user_id'] ?? $row['peminjam'] ?? ''); ?></strong></td>
                                            <!-- Menampilkan judul dari tabel buku, jika tidak ketemu tampilkan isi book_id -->
                                            <td><?= htmlspecialchars($row['judul_buku'] ?? $row['book_id'] ?? '-'); ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['tanggal_tenggat'])); ?></td>
                                            <td class="text-center">-</td>
                                            <td>
                                                <?php if ($is_overdue): ?>
                                                    <span class="badge bg-danger">Terlambat</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Dipinjam</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                $id_transaksi = $row['id'] 
                                                             ?? $row['id_peminjaman'] 
                                                             ?? $row['id_pinjam'] 
                                                             ?? $row['id_transaksi'] 
                                                             ?? $row['id_sirkulasi'] 
                                                             ?? '';
                                                ?>
                                                <a href="proses_kembali.php?id=<?= $id_transaksi; ?>" 
                                                   class="btn btn-sm btn-outline-success"
                                                   onclick="return confirm('Konfirmasi pengembalian buku ini?')">
                                                    Kembalikan
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">Tidak ada transaksi peminjaman aktif.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Riwayat Pengembalian -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">Riwayat Pengembalian Buku</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Peminjam</th>
                                    <th>Judul Buku</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Tenggat</th>
                                    <th>Tgl Kembali</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($query_riwayat && mysqli_num_rows($query_riwayat) > 0): ?>
                                    <?php while ($r = mysqli_fetch_assoc($query_riwayat)): ?>
                                        <tr>
                                            <td><strong><?= htmlspecialchars($r['user_id'] ?? $r['peminjam'] ?? ''); ?></strong></td>
                                            <td><?= htmlspecialchars($r['judul_buku'] ?? $r['book_id'] ?? '-'); ?></td>
                                            <td><?= date('d/m/Y', strtotime($r['tanggal_pinjam'])); ?></td>
                                            <td><?= date('d/m/Y', strtotime($r['tanggal_tenggat'])); ?></td>
                                            <td>
                                                <?= (!empty($r['tanggal_kembali']) && $r['tanggal_kembali'] != '0000-00-00') 
                                                    ? date('d/m/Y', strtotime($r['tanggal_kembali'])) 
                                                    : '-'; ?>
                                            </td>
                                            <td><span class="badge bg-secondary">Dikembalikan</span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">Belum ada riwayat pengembalian buku.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>