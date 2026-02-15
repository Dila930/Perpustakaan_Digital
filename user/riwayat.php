<?php
session_start();
include "../config/koneksi.php";

// Proteksi Halaman
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Logika Pengembalian Buku
if (isset($_GET['kembali'])) {
    $id_transaksi = $_GET['kembali'];
    $cek_transaksi = mysqli_query($conn, "SELECT buku_id FROM transaksi WHERE id='$id_transaksi' AND status='dipinjam'");
    $t = mysqli_fetch_assoc($cek_transaksi);

    if ($t) {
        $buku_id = $t['buku_id'];
        mysqli_query($conn, "UPDATE transaksi SET status='dikembalikan', tanggal_kembali=CURDATE() WHERE id='$id_transaksi'");
        mysqli_query($conn, "UPDATE buku SET stok = stok + 1 WHERE id='$buku_id'");
        header("Location: riwayat.php?msg=returned");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Saya | E-Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg shadow-sm mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold text-primary"><i class="fas fa-history me-2"></i>RIWAYAT AKTIVITAS</span>
        <div class="ms-auto d-flex align-items-center">
            <button id="theme-toggle" class="btn btn-outline-secondary border-0 rounded-circle me-3">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>
            <a href="dashboard.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">Kembali</a>
        </div>
    </div>
</nav>

<div class="container">
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'returned'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> Buku berhasil dikembalikan!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-uppercase small fw-bold" style="letter-spacing: 1px;">
                        <th class="px-4 py-3">Koleksi Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query mengambil kolom terakhir_baca untuk fitur riwayat baca
                    $data = mysqli_query($conn, "SELECT transaksi.*, buku.judul 
                                                FROM transaksi 
                                                JOIN buku ON transaksi.buku_id = buku.id 
                                                WHERE transaksi.user_id = '$user_id' 
                                                ORDER BY transaksi.id DESC");
                    while($d = mysqli_fetch_assoc($data)):
                    ?>
                    <tr>
                        <td class="px-4">
                            <span class="fw-bold"><?= htmlspecialchars($d['judul']) ?></span>
                            <?php if($d['status'] == 'dipinjam' && $d['terakhir_baca'] > 0): ?>
                                <br><small class="text-primary fw-bold"><i class="fas fa-bookmark me-1"></i> Terakhir: Part <?= $d['terakhir_baca'] ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small"><?= date('d/m/Y', strtotime($d['tanggal_pinjam'])) ?></td>
                        <td class="text-muted small"><?= ($d['tanggal_kembali']) ? date('d/m/Y', strtotime($d['tanggal_kembali'])) : '-' ?></td>
                        <td>
                            <?php if($d['status'] == 'dipinjam'): ?>
                                <span class="badge bg-warning text-dark px-3 rounded-pill">DIPINJAM</span>
                            <?php else: ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 rounded-pill">DIKEMBALIKAN</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if($d['status'] == 'dipinjam'): ?>
                                <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                                    <a href="baca.php?id=<?= $d['buku_id'] ?>&part=<?= ($d['terakhir_baca'] > 0) ? $d['terakhir_baca'] : 1 ?>" 
                                       class="btn btn-primary btn-sm px-3">
                                       <i class="fas fa-book-open me-1"></i> <?= ($d['terakhir_baca'] > 0) ? 'Lanjut' : 'Baca' ?>
                                    </a>
                                    <a href="?kembali=<?= $d['id'] ?>" class="btn btn-outline-warning btn-sm" onclick="return confirm('Kembalikan buku ini?')">Kembalikan</a>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small">Selesai</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
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
</body>
</html>