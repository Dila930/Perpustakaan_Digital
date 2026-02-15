<?php
session_start();
include "../config/koneksi.php";

// 1. Proteksi Halaman
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// 2. Validasi Parameter
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = (int)$_GET['id'];
$user_id = $_SESSION['user']['id'];
$current_part = isset($_GET['part']) ? (int)$_GET['part'] : 1;

// 3. Cek Status Peminjaman
$cek = $conn->query("SELECT * FROM transaksi WHERE user_id='$user_id' AND buku_id='$id' AND status='dipinjam'");
if ($cek->num_rows == 0) {
    echo "<script>alert('Anda harus meminjam buku ini terlebih dahulu!'); window.location='dashboard.php';</script>";
    exit;
}

// 4. UPDATE RIWAYAT BACA (Fitur Baru)
// Menyimpan part yang sedang dibuka ke kolom terakhir_baca di tabel transaksi
$conn->query("UPDATE transaksi SET terakhir_baca = '$current_part' 
              WHERE user_id = '$user_id' AND buku_id = '$id' AND status = 'dipinjam'");

// 5. Ambil Data Buku & Daftar Semua Part untuk Sidebar
$buku = $conn->query("SELECT * FROM buku WHERE id='$id'")->fetch_assoc();
$daftar_part = $conn->query("SELECT id_isi, part_ke, judul_part FROM isi_buku WHERE id_buku='$id' ORDER BY part_ke ASC");

// 6. Ambil Konten Part yang Sedang Dibuka
$stmt_isi = $conn->prepare("SELECT * FROM isi_buku WHERE id_buku = ? AND part_ke = ?");
$stmt_isi->bind_param("ii", $id, $current_part);
$stmt_isi->execute();
$isi_sekarang = $stmt_isi->get_result()->fetch_assoc();

$total_part = $daftar_part->num_rows;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membaca | <?= htmlspecialchars($buku['judul']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        body { transition: background 0.4s; overflow-x: hidden; }
        .wrapper { display: flex; align-items: stretch; min-height: 100vh; }
        #sidebar { min-width: 280px; max-width: 280px; transition: all 0.3s; border-right: 1px solid rgba(0,0,0,0.1); z-index: 1000; }
        #content { width: 100%; padding: 0; min-height: 100vh; }
        .sidebar-header { padding: 20px; border-bottom: 1px solid rgba(0,0,0,0.05); }
        .nav-link-custom { padding: 12px 20px; display: block; text-decoration: none; color: inherit; transition: 0.2s; border-left: 4px solid transparent; font-size: 0.9rem; }
        .nav-link-custom:hover { background: rgba(13, 110, 253, 0.05); color: #0d6efd; }
        .nav-link-custom.active { background: rgba(13, 110, 253, 0.1); color: #0d6efd; font-weight: bold; border-left-color: #0d6efd; }
        .reader-paper { max-width: 800px; margin: 40px auto; padding: 60px; border-radius: 15px; min-height: 80vh; }
        .content-body { font-family: 'Georgia', serif; font-size: 1.25rem; line-height: 2; text-align: justify; }

        body:not(.light-mode) { background: #0f0f0f; color: #e0e0e0; }
        body:not(.light-mode) #sidebar { background: #1a1a1a; border-right-color: #333; }
        body:not(.light-mode) .reader-paper { background: #1a1a1a; border: 1px solid #333; }
        body:not(.light-mode) .navbar { background: #1a1a1a; border-bottom: 1px solid #333; }

        body.light-mode { background: #f4f7f6; color: #2d3436; }
        body.light-mode #sidebar { background: #ffffff; }
        body.light-mode .reader-paper { background: #ffffff; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        body.light-mode .navbar { background: #ffffff; border-bottom: 1px solid #dee2e6; }

        @media (max-width: 992px) {
            #sidebar { margin-left: -280px; position: fixed; height: 100%; }
            #sidebar.active { margin-left: 0; }
            .reader-paper { padding: 30px; margin: 20px; }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h6 class="fw-bold text-primary mb-0"><i class="fas fa-list-ul me-2"></i>Daftar Isi</h6>
            <small class="text-muted"><?= $total_part ?> Bagian tersedia</small>
        </div>
        <div class="py-3">
            <?php 
            $daftar_part->data_seek(0);
            while($p = $daftar_part->fetch_assoc()): 
            ?>
                <a href="?id=<?= $id ?>&part=<?= $p['part_ke'] ?>" 
                   class="nav-link-custom <?= ($current_part == $p['part_ke']) ? 'active' : '' ?>">
                    <div class="small opacity-50">BAGIAN <?= $p['part_ke'] ?></div>
                    <div class="text-truncate"><?= htmlspecialchars($p['judul_part']) ?></div>
                </a>
            <?php endwhile; ?>
        </div>
    </nav>

    <div id="content">
        <nav class="navbar sticky-top px-4 py-2">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <button id="sidebarCollapse" class="btn btn-outline-primary border-0 me-3">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="navbar-brand fw-bold text-primary d-none d-sm-block">E-READER</span>
                </div>
                <div class="ms-auto d-flex align-items-center">
                    <button id="theme-toggle" class="btn btn-outline-secondary border-0 rounded-circle me-3">
                        <i id="theme-icon" class="fas fa-moon"></i>
                    </button>
                    <a href="riwayat.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="fas fa-times"></i> Tutup
                    </a>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="reader-paper shadow-sm">
                <?php if($isi_sekarang): ?>
                    <div class="text-center mb-5">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 rounded-pill mb-2">
                            Bagian <?= $isi_sekarang['part_ke'] ?> dari <?= $total_part ?>
                        </span>
                        <h2 class="fw-bold"><?= htmlspecialchars($isi_sekarang['judul_part']) ?></h2>
                        <div class="text-muted small">Buku: <?= htmlspecialchars($buku['judul']) ?></div>
                        <hr class="mt-4 opacity-25">
                    </div>

                    <div class="content-body">
                        <?= nl2br($isi_sekarang['konten']) ?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-5 pt-4 border-top opacity-75">
                        <?php if($current_part > 1): ?>
                            <a href="?id=<?= $id ?>&part=<?= $current_part - 1 ?>" class="btn btn-light rounded-pill px-4">
                                <i class="fas fa-chevron-left me-2"></i> Sebelumnya
                            </a>
                        <?php else: ?>
                            <div></div>
                        <?php endif; ?>

                        <span class="small fw-bold text-primary">Hal <?= $current_part ?></span>

                        <?php if($current_part < $total_part): ?>
                            <a href="?id=<?= $id ?>&part=<?= $current_part + 1 ?>" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                Berikutnya <i class="fas fa-chevron-right ms-2"></i>
                            </a>
                        <?php else: ?>
                            <button onclick="alert('Anda telah menyelesaikan buku ini!')" class="btn btn-success rounded-pill px-4">
                                <i class="fas fa-check-circle me-2"></i> Selesai
                            </button>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-book-open fa-4x text-secondary mb-4 opacity-25"></i>
                        <h4>Konten tidak ditemukan.</h4>
                        <p class="text-muted">Pilih bab melalui daftar isi di sebelah kiri.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebar = document.getElementById('sidebar');
    sidebarCollapse.addEventListener('click', () => { sidebar.classList.toggle('active'); });

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