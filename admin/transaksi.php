<?php
session_start();
include "../config/koneksi.php";

// Proteksi Admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg shadow-sm mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold text-primary"><i class="fas fa-exchange-alt me-2"></i>LOG TRANSAKSI</span>
        <div class="ms-auto d-flex align-items-center">
            <button id="theme-toggle" class="btn btn-outline-secondary border-0 rounded-circle me-3">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>
            <a href="dashboard.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Data Peminjaman</h3>
            <p class="text-secondary small">Riwayat peminjaman dan pengembalian buku seluruh anggota</p>
        </div>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-print me-1"></i> Cetak Laporan
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary bg-opacity-10">
                    <tr>
                        <th class="px-4 py-3" width="5%">No</th>
                        <th>Peminjam</th>
                        <th>Informasi Buku</th>
                        <th>Tanggal Pinjam</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no=1;
                    $data = mysqli_query($conn,"
                        SELECT transaksi.*, users.nama, buku.judul 
                        FROM transaksi 
                        JOIN users ON transaksi.user_id=users.id
                        JOIN buku ON transaksi.buku_id=buku.id
                        ORDER BY transaksi.id DESC
                    ");

                    if(mysqli_num_rows($data) > 0):
                        while($d = mysqli_fetch_assoc($data)):
                    ?>
                    <tr>
                        <td class="px-4 text-muted small"><?= $no++ ?></td>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($d['nama']) ?></div>
                            <div class="text-muted small">ID User: #<?= $d['user_id'] ?></div>
                        </td>
                        <td>
                            <div class="text-primary fw-semibold"><?= htmlspecialchars($d['judul']) ?></div>
                        </td>
                        <td>
                            <div class="small"><i class="far fa-calendar-alt me-1 text-secondary"></i> <?= date('d M Y', strtotime($d['tanggal_pinjam'])) ?></div>
                        </td>
                        <td class="text-center">
                            <?php if($d['status'] == 'dipinjam'): ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 rounded-pill">
                                    <i class="fas fa-clock me-1"></i> Dipinjam
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 rounded-pill">
                                    <i class="fas fa-check-circle me-1"></i> Kembali
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data transaksi.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;

    function applyTheme(theme) {
        if (theme === 'light') {
            body.classList.add('light-mode');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            body.classList.remove('light-mode');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        }
    }

    const savedTheme = localStorage.getItem('theme') || 'dark';
    applyTheme(savedTheme);

    themeToggle.addEventListener('click', () => {
        const isLight = body.classList.toggle('light-mode');
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        applyTheme(isLight ? 'light' : 'dark');
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>