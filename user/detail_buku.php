<?php
session_start();
include "../config/koneksi.php";

// Proteksi Halaman
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user') { 
    header("Location: ../login.php"); 
    exit; 
}

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'];
$user_id = $_SESSION['user']['id'];

// 1. Ambil Detail Buku menggunakan Prepared Statement
$stmt = $conn->prepare("SELECT * FROM buku WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$d = $stmt->get_result()->fetch_assoc();

if (!$d) {
    echo "<script>alert('Buku tidak ditemukan!'); window.location='dashboard.php';</script>";
    exit;
}

// 2. Ambil Semua Part Buku dari tabel isi_buku
$query_parts = $conn->prepare("SELECT part_ke, judul_part FROM isi_buku WHERE id_buku = ? ORDER BY part_ke ASC");
$query_parts->bind_param("i", $id);
$query_parts->execute();
$result_parts = $query_parts->get_result();
$parts = [];
while ($row = $result_parts->fetch_assoc()) {
    $parts[] = $row;
}

// 3. Cek status pinjam
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
        /* Base Styles */
        body { transition: background 0.4s, color 0.4s; }
        .bg-dark-card { transition: all 0.4s ease; border-radius: 20px; }
        
        /* Dark Mode (Default) */
        body:not(.light-mode) .bg-dark-card { background-color: #1a1a1a !important; border: 1px solid #2d2d2d !important; }
        body:not(.light-mode) .info-value { color: #ffffff; }
        body:not(.light-mode) .list-group-item { background-color: #252525; border-color: #333; color: #eee; }

        /* Light Mode */
        body.light-mode { background-color: #f0f2f5 !important; color: #212529 !important; }
        body.light-mode .bg-dark-card { background-color: #ffffff !important; border: none !important; box-shadow: 0 10px 30px rgba(0,0,0,0.08) !important; }
        body.light-mode .info-value { color: #212529; }
        body.light-mode .text-white { color: #212529 !important; }
        body.light-mode .list-group-item { background-color: #f8f9fa; color: #333; }

        /* UI Elements */
        .info-label { color: #888; font-size: 0.85rem; display: block; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
        .info-value { font-weight: 600; font-size: 1.1rem; }
        .sinopsis-text { line-height: 1.8; text-align: justify; color: #adb5bd; }
        body.light-mode .sinopsis-text { color: #495057; }
        
        .book-cover-preview {
            height: 450px;
            background: linear-gradient(45deg, #2c2c2c, #1a1a1a);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,0.1);
        }
        body.light-mode .book-cover-preview { background: #e9ecef; border: 1px solid #dee2e6; }

        .theme-btn-fixed { position: fixed; top: 20px; right: 20px; z-index: 1000; }
        
        /* List Part Style */
        .part-badge { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 0.8rem; }
    </style>
</head>
<body class="bg-dark text-white">

    <div class="theme-btn-fixed">
        <button id="theme-toggle" class="btn btn-secondary rounded-circle shadow">
            <i id="theme-icon" class="fas fa-moon"></i>
        </button>
    </div>

    <div class="container mt-5 pt-3">
        <div class="card bg-dark-card border-0 p-4 shadow-lg mb-5">
            <div class="row g-5">
                <div class="col-md-4">
                    <div class="book-cover-preview shadow">
                        <i class="fas fa-book fa-7x text-secondary opacity-50"></i>
                    </div>
                </div>

                <div class="col-md-8">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php" class="text-primary text-decoration-none">Dashboard</a></li>
                            <li class="breadcrumb-item active">Detail Buku</li>
                        </ol>
                    </nav>
                    
                    <h1 class="fw-bold text-white mb-1 mt-3"><?= htmlspecialchars($d['judul']) ?></h1>
                    <p class="text-primary fs-5 mb-4">Oleh <span class="fw-bold"><?= htmlspecialchars($d['pengarang']) ?></span></p>
                    
                    <hr class="border-secondary opacity-25 mb-4">
                    
                    <div class="row g-4 mb-4">
                        <div class="col-6 col-md-3">
                            <span class="info-label">Penerbit</span>
                            <span class="info-value"><?= htmlspecialchars($d['penerbit'] ?: '-') ?></span>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="info-label">Tahun</span>
                            <span class="info-value"><?= $d['tahun_terbit'] ?: '-' ?></span>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="info-label">ISBN</span>
                            <span class="info-value"><?= htmlspecialchars($d['isbn'] ?: '-') ?></span>
                        </div>
                        <div class="col-6 col-md-3">
                            <span class="info-label">Ketersediaan</span>
                            <span class="badge rounded-pill bg-<?= $d['stok'] > 0 ? 'success' : 'danger' ?> px-3 py-2">
                                <?= $d['stok'] ?> Buku Tersedia
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-white fw-bold mb-3 d-flex align-items-center">
                            <i class="fas fa-quote-left text-primary me-2"></i> Sinopsis
                        </h5>
                        <div class="sinopsis-text mb-4">
                            <?= !empty($d['sinopsis']) ? nl2br(htmlspecialchars($d['sinopsis'])) : '<span class="text-secondary italic">Sinopsis belum tersedia.</span>' ?>
                        </div>
                    </div>

                    <div class="mb-5">
                        <h5 class="text-white fw-bold mb-3 d-flex align-items-center">
                            <i class="fas fa-list-ol text-primary me-2"></i> Daftar Isi (<?= count($parts) ?> Bagian)
                        </h5>
                        <?php if (count($parts) > 0): ?>
                            <div class="list-group list-group-flush rounded-3 overflow-hidden">
                                <?php foreach ($parts as $p): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                        <span>
                                            <span class="part-badge bg-primary text-white me-2"><?= $p['part_ke'] ?></span>
                                            <?= htmlspecialchars($p['judul_part']) ?>
                                        </span>
                                        <?php if ($is_borrowed): ?>
                                            <i class="fas fa-check-circle text-success"></i>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-secondary py-2 border-0 opacity-75">Konten digital belum tersedia.</div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-auto pt-4 border-top border-secondary border-opacity-25">
                        <div class="d-flex flex-wrap gap-3">
                            <a href="dashboard.php" class="btn btn-outline-secondary px-4 rounded-pill">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            
                            <?php if ($is_borrowed): ?>
                                <a href="baca.php?id=<?= $d['id'] ?>" class="btn btn-warning px-5 fw-bold text-dark rounded-pill">
                                    <i class="fas fa-book-open me-2"></i> MULAI BACA SEKARANG
                                </a>
                                <a href="dashboard.php?kembali=<?= $d['id'] ?>" class="btn btn-danger px-4 rounded-pill" onclick="return confirm('Kembalikan buku ini?')">
                                    <i class="fas fa-undo me-2"></i> Kembalikan
                                </a>
                            <?php elseif ($d['stok'] > 0): ?>
                                <a href="dashboard.php?pinjam=<?= $d['id'] ?>" class="btn btn-primary px-5 fw-bold rounded-pill shadow-sm" onclick="return confirm('Pinjam buku ini?')">
                                    <i class="fas fa-plus me-2"></i> PINJAM BUKU SEKARANG
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary px-5 fw-bold rounded-pill" disabled>STOK HABIS</button>
                            <?php endif; ?>
                        </div>
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

        const currentTheme = localStorage.getItem('theme') || 'dark';
        applyTheme(currentTheme);

        themeToggle.addEventListener('click', () => {
            const isLight = body.classList.toggle('light-mode');
            const newTheme = isLight ? 'light' : 'dark';
            localStorage.setItem('theme', newTheme);
            applyTheme(newTheme);
        });
    </script>
</body>
</html>