<?php
session_start();
include "../config/koneksi.php";

// 1. Proteksi Halaman
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// --- LOGIKA TRANSAKSI (PINJAM/KEMBALI) ---
if (isset($_GET['pinjam'])) {
    $buku_id = $_GET['pinjam'];
    $tanggal_pinjam = date('Y-m-d');
    $tanggal_kembali = date('Y-m-d', strtotime('+7 days'));

    $cek = $conn->prepare("SELECT id FROM transaksi WHERE user_id = ? AND buku_id = ? AND status = 'dipinjam'");
    $cek->bind_param("ii", $user_id, $buku_id);
    $cek->execute();
    
    if ($cek->get_result()->num_rows > 0) {
        echo "<script>alert('Buku sudah ada di daftar pinjam!'); window.location='dashboard.php';</script>";
    } else {
        $stok_query = $conn->prepare("SELECT stok FROM buku WHERE id = ?");
        $stok_query->bind_param("i", $buku_id);
        $stok_query->execute();
        $b_data = $stok_query->get_result()->fetch_assoc();

        if ($b_data && $b_data['stok'] > 0) {
            $ins = $conn->prepare("INSERT INTO transaksi (user_id, buku_id, tanggal_pinjam, tanggal_kembali, status) VALUES (?, ?, ?, ?, 'dipinjam')");
            $ins->bind_param("iiss", $user_id, $buku_id, $tanggal_pinjam, $tanggal_kembali);
            
            if ($ins->execute()) {
                $conn->query("UPDATE buku SET stok = stok - 1 WHERE id = '$buku_id'");
                echo "<script>alert('Buku berhasil dipinjam!'); window.location='dashboard.php';</script>";
            }
        }
    }
}

if (isset($_GET['kembali'])) {
    $buku_id = $_GET['kembali'];
    $upd_trx = $conn->prepare("UPDATE transaksi SET status = 'kembali' WHERE user_id = ? AND buku_id = ? AND status = 'dipinjam'");
    $upd_trx->bind_param("ii", $user_id, $buku_id);
    
    if ($upd_trx->execute()) {
        $conn->query("UPDATE buku SET stok = stok + 1 WHERE id = '$buku_id'");
        echo "<script>alert('Buku telah dikembalikan!'); window.location='dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | E-Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg shadow-sm mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold text-primary" href="dashboard.php"><i class="fas fa-user-shield me-2"></i>ADMIN PANEL</a>
        
        <div class="ms-auto d-flex align-items-center">
            <button id="theme-toggle" class="btn btn-outline-secondary border-0 rounded-circle me-3" style="width: 40px; height: 40px;">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>
            
            <a href="riwayat.php" class="btn btn-outline-primary border-0 rounded-circle me-2" title="Riwayat Saya" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-history"></i>
            </a>

            <div class="d-none d-md-block me-3">
                <span class="text-secondary small d-block">Selamat Datang,</span>
                <span class="fw-bold"><?= htmlspecialchars($_SESSION['user']['nama']) ?></span>
            </div>
            <a href="../logout.php" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    
    <div class="row g-4 mb-5">
        <?php
        $total_buku = $conn->query("SELECT SUM(stok) as total FROM buku")->fetch_assoc()['total'] ?? 0;
        $total_user = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='user'")->fetch_assoc()['total'];
        $total_trx = $conn->query("SELECT COUNT(*) as total FROM transaksi WHERE status='dipinjam'")->fetch_assoc()['total'];
        ?>
        <div class="col-md-4">
            <div class="card stat-card p-4 shadow-sm h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-book text-primary fa-2x"></i>
                    </div>
                    <div>
                        <p class="text-secondary mb-0 small fw-bold text-uppercase">Stok Buku</p>
                        <h2 class="fw-bold mb-0 counter"><?= $total_buku ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-4 shadow-sm h-100" style="border-left-color: #198754 !important;">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-users text-success fa-2x"></i>
                    </div>
                    <div>
                        <p class="text-secondary mb-0 small fw-bold text-uppercase">Anggota</p>
                        <h2 class="fw-bold mb-0 counter"><?= $total_user ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-4 shadow-sm h-100" style="border-left-color: #ffc107 !important;">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="fas fa-exchange-alt text-warning fa-2x"></i>
                    </div>
                    <div>
                        <p class="text-secondary mb-0 small fw-bold text-uppercase">Peminjaman Aktif</p>
                        <h2 class="fw-bold mb-0 counter"><?= $total_trx ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-4 mb-5 shadow-sm">
        <h5 class="mb-4 fw-bold"><i class="fas fa-th-large me-2 text-primary"></i> Akses Menu</h5>
        <div class="row g-3">
            <div class="col-md-4">
                <a href="buku.php" class="btn btn-primary w-100 py-3 fw-bold shadow-sm transition-hover">
                    <i class="fas fa-edit mb-2 d-block fa-lg"></i> Kelola Buku
                </a>
            </div>
            <div class="col-md-4">
                <a href="anggota.php" class="btn btn-outline-primary w-100 py-3 fw-bold shadow-sm transition-hover">
                    <i class="fas fa-user-friends mb-2 d-block fa-lg"></i> Data Anggota
                </a>
            </div>
            <div class="col-md-4">
                <a href="transaksi.php" class="btn btn-outline-primary w-100 py-3 fw-bold shadow-sm transition-hover">
                    <i class="fas fa-history mb-2 d-block fa-lg"></i> Laporan Transaksi
                </a>
            </div>
        </div>
    </div>

    <div class="card p-4 mb-5 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0"><i class="fas fa-list me-2 text-primary"></i> Monitoring Stok Buku</h5>
            <span class="badge bg-secondary bg-opacity-10 text-secondary border px-3 py-2">Total: <?= $total_buku ?> Unit</span>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="px-4">Judul Koleksi</th>
                        <th>Pengarang</th>
                        <th>Status Stok</th>
                        <th class="text-center">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $buku = $conn->query("SELECT * FROM buku ORDER BY id DESC");
                    while ($d = $buku->fetch_assoc()) :
                        $id_buku = $d['id'];
                        $cek_status = $conn->query("SELECT id FROM transaksi WHERE user_id='$user_id' AND buku_id='$id_buku' AND status='dipinjam'");
                        $is_borrowed = $cek_status->num_rows > 0;
                    ?>
                    <tr>
                        <td class="px-4 fw-bold text-truncate" style="max-width: 250px;">
                            <?= htmlspecialchars($d['judul']) ?>
                        </td>
                        <td class="text-secondary small">
                            <?= htmlspecialchars($d['pengarang']) ?>
                        </td>
                        <td>
                            <?php if ($d['stok'] > 0): ?>
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i> <?= $d['stok'] ?> Unit
                                </span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                                    <i class="fas fa-times-circle me-1"></i> Habis
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <a href="detail_buku.php?id=<?= $d['id'] ?>" class="btn btn-info btn-sm px-3" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <?php if ($is_borrowed): ?>
                                    <a href="baca.php?id=<?= $d['id'] ?>" class="btn btn-warning btn-sm px-3" title="Baca Buku">
                                        <i class="fas fa-book-open"></i>
                                    </a>
                                    <a href="?kembali=<?= $d['id'] ?>" class="btn btn-danger btn-sm px-3" onclick="return confirm('Kembalikan buku ini?')" title="Kembalikan">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                <?php elseif ($d['stok'] > 0): ?>
                                    <a href="?pinjam=<?= $d['id'] ?>" class="btn btn-success btn-sm px-3" onclick="return confirm('Pinjam buku ini?')" title="Pinjam">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm px-3" disabled><i class="fas fa-ban"></i></button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Theme Logic yang Terintegrasi dengan assets/style.css
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;

    function applyTheme() {
        const currentTheme = localStorage.getItem('theme') || 'dark';
        if (currentTheme === 'light') {
            body.classList.add('light-mode');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            body.classList.remove('light-mode');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        }
    }

    themeToggle.addEventListener('click', () => {
        body.classList.toggle('light-mode');
        const isLight = body.classList.contains('light-mode');
        localStorage.setItem('theme', isLight ? 'light' : 'dark');
        
        if (isLight) {
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        }
    });

    // Jalankan tema saat load
    applyTheme();
</script>

</body>
</html>