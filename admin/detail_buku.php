<?php
session_start();
include "../config/koneksi.php";

// Proteksi Halaman
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') { 
    header("Location: ../login.php"); 
    exit; 
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$user_id = $_SESSION['user']['id'];

// 1. Ambil Detail Buku
$stmt = $conn->prepare("SELECT * FROM buku WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$d = $stmt->get_result()->fetch_assoc();

if (!$d) {
    echo "<script>alert('Buku tidak ditemukan!'); window.location='dashboard.php';</script>";
    exit;
}

// 2. Cek status pinjam (untuk tombol baca/kembalikan)
$cek_status = $conn->query("SELECT id FROM transaksi WHERE user_id='$user_id' AND buku_id='$id' AND status='dipinjam'");
$is_borrowed = $cek_status->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail | <?= htmlspecialchars($d['judul']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .cover-preview {
            height: 450px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(var(--primary-rgb), 0.05);
            border: 2px dashed var(--border-color);
            border-radius: 15px;
        }
        .info-label {
            color: var(--text-muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 4px;
        }
        .info-value {
            font-weight: 600;
            font-size: 1.1rem;
        }
        .sinopsis-box {
            line-height: 1.8;
            color: var(--text-muted);
            white-space: pre-wrap;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg shadow-sm mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold text-primary"><i class="fas fa-info-circle me-2"></i>DETAIL KOLEKSI</span>
        <div class="ms-auto d-flex align-items-center">
            <button id="theme-toggle" class="btn btn-outline-secondary border-0 rounded-circle me-3">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>
            <a href="buku.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="card border-0 shadow-lg overflow-hidden">
        <div class="row g-0">
            <div class="col-md-4 p-4 text-center">
                <div class="cover-preview">
                    <i class="fas fa-book fa-6x opacity-25"></i>
                </div>
            </div>

            <div class="col-md-8 p-4 p-lg-5">
                <div class="mb-4">
                    <span class="badge bg-primary mb-2 px-3 py-2 rounded-pill shadow-sm">Katalog #<?= $d['id'] ?></span>
                    <h1 class="fw-bold mb-1"><?= htmlspecialchars($d['judul']) ?></h1>
                    <h5 class="text-primary fw-semibold">Penulis: <?= htmlspecialchars($d['pengarang']) ?></h5>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-sm-6 col-lg-3">
                        <span class="info-label">Penerbit</span>
                        <span class="info-value"><?= htmlspecialchars($d['penerbit'] ?: '-') ?></span>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <span class="info-label">Tahun</span>
                        <span class="info-value"><?= $d['tahun_terbit'] ?: '-' ?></span>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                        <span class="info-label">Ketersediaan</span>
                        <span class="badge <?= $d['stok'] > 0 ? 'bg-success' : 'bg-danger' ?> rounded-pill px-3">
                            <?= $d['stok'] ?> Buku
                        </span>
                    </div>
                </div>

                <div class="mb-5">
                    <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3" style="letter-spacing: 1px;">Sinopsis</h6>
                    <div class="sinopsis-box">
                        <?= !empty($d['sinopsis']) ? htmlspecialchars($d['sinopsis']) : '<em>Tidak ada sinopsis yang tersedia.</em>' ?>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 pt-4 border-top">
                    <?php if ($is_borrowed): ?>
                        <a href="baca.php?id=<?= $d['id'] ?>" class="btn btn-warning px-4 rounded-pill fw-bold shadow-sm">
                            <i class="fas fa-book-open me-2"></i> LANJUT BACA
                        </a>
                    <?php elseif ($d['stok'] > 0): ?>
                        <a href="dashboard.php?pinjam=<?= $d['id'] ?>" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm" onclick="return confirm('Pinjam buku ini?')">
                            <i class="fas fa-plus me-2"></i> PINJAM SEKARANG
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary px-4 rounded-pill fw-bold" disabled>STOK KOSONG</button>
                    <?php endif; ?>
                    
                    <a href="edit_buku.php?id=<?= $d['id'] ?>" class="btn btn-outline-info px-4 rounded-pill">
                        <i class="fas fa-edit me-2"></i> EDIT DATA
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
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