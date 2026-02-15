<?php
session_start();
include "../config/koneksi.php";

// 1. Proteksi Halaman
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user') {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// 2. Logika Pinjam Buku (Proses Langsung dari Katalog)
if (isset($_GET['pinjam'])) {
    $buku_id = $_GET['pinjam'];
    $tanggal = date('Y-m-d');

    $cek_pinjam = $conn->query("SELECT * FROM transaksi WHERE user_id='$user_id' AND buku_id='$buku_id' AND status='dipinjam'");
    $s = $conn->query("SELECT stok FROM buku WHERE id='$buku_id'")->fetch_assoc();

    if ($cek_pinjam->num_rows > 0) {
        header("Location: dashboard.php?msg=already");
    } elseif ($s['stok'] <= 0) {
        header("Location: dashboard.php?msg=empty");
    } else {
        $conn->query("INSERT INTO transaksi (user_id, buku_id, tanggal_pinjam, status) VALUES ('$user_id', '$buku_id', '$tanggal', 'dipinjam')");
        $conn->query("UPDATE buku SET stok = stok - 1 WHERE id='$buku_id'");
        header("Location: dashboard.php?msg=success");
    }
    exit;
}

// 3. Inisialisasi Pencarian
$keyword = "";
if (isset($_GET['search'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['search']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku | E-Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* Global Transitions */
        body { transition: background-color 0.4s, color 0.4s; }
        
        /* Light Mode Adjustments */
        body.light-mode { background-color: #f8f9fa; color: #212529; }
        body.light-mode .navbar { background-color: #ffffff !important; }
        body.light-mode .book-card { background-color: #ffffff; border-color: #dee2e6 !important; }

        /* Book Card Styling */
        .book-card { 
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1) !important;
            background-color: #1a1a1a;
        }
        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2) !important;
        }
        .cover-box { 
            height: 200px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            background: linear-gradient(45deg, rgba(13, 110, 253, 0.1), rgba(13, 110, 253, 0.05));
        }
        .sinopsis-short { 
            font-size: 0.85rem; 
            display: -webkit-box; 
            -webkit-line-clamp: 2; 
            -webkit-box-orient: vertical; 
            overflow: hidden; 
            height: 2.6em; 
            margin-bottom: 1rem;
        }
        .badge-stok { 
            position: absolute; 
            top: 15px; 
            right: 15px; 
            font-size: 0.75rem;
            padding: 6px 12px;
        }
        .btn-detail {
            border: 1px solid rgba(13, 110, 253, 0.3);
            color: var(--bs-primary);
        }
        .btn-detail:hover {
            background-color: rgba(13, 110, 253, 0.1);
        }
    </style>
</head>
<body class="bg-dark text-white">

<nav class="navbar navbar-expand-lg sticky-top shadow-sm py-3 bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="dashboard.php">
            <i class="fas fa-book-reader me-2"></i>E-LIBRARY
        </a>
        
        <div class="ms-auto d-flex align-items-center">
            <button id="theme-toggle" class="btn btn-outline-secondary border-0 rounded-circle me-3">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>
            <a href="riwayat.php" class="btn btn-outline-primary btn-sm rounded-pill px-3 me-3">
                <i class="fas fa-history me-1"></i> Riwayat
            </a>
            <div class="dropdown">
                <a class="nav-link dropdown-toggle small fw-bold" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle me-1"></i> <?= $_SESSION['user']['nama'] ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li><a class="dropdown-item text-danger" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Keluar</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row mb-5 align-items-center g-3">
        <div class="col-md-7 text-center text-md-start">
            <h1 class="fw-bold mb-1">Jelajahi Literasi</h1>
            <p class="text-secondary">Temukan ribuan ilmu dalam genggaman digital Anda.</p>
        </div>
        <div class="col-md-5">
            <form action="" method="GET">
                <div class="input-group shadow-sm rounded-pill overflow-hidden">
                    <input type="text" name="search" class="form-control border-0 py-3 px-4" 
                           placeholder="Cari judul atau pengarang..." value="<?= htmlspecialchars($keyword) ?>">
                    <button class="btn btn-primary px-4" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-primary border-0 shadow-sm mb-4 alert-dismissible fade show rounded-4" role="alert">
            <i class="fas fa-info-circle me-2"></i> 
            <?php 
                if($_GET['msg'] == 'success') echo "Buku berhasil dipinjam! Cek di menu riwayat.";
                if($_GET['msg'] == 'already') echo "Buku ini sedang Anda pinjam.";
                if($_GET['msg'] == 'empty') echo "Maaf, stok buku ini sedang kosong.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4 mb-5">
        <?php
        // Query dengan cek status pinjam user saat ini
        $sql = "SELECT buku.*, (SELECT id FROM transaksi WHERE user_id = '$user_id' AND buku_id = buku.id AND status = 'dipinjam' LIMIT 1) AS sedang_dipinjam FROM buku";
        if ($keyword != "") { 
            $sql .= " WHERE judul LIKE '%$keyword%' OR pengarang LIKE '%$keyword%'"; 
        }
        $sql .= " ORDER BY id DESC";
        
        $res = $conn->query($sql);
        if ($res->num_rows > 0):
            while($d = $res->fetch_assoc()):
                $is_borrowed = !empty($d['sedang_dipinjam']);
        ?>
        <div class="col">
            <div class="card book-card shadow-sm">
                <span class="badge rounded-pill badge-stok <?= $d['stok'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                    <?= $d['stok'] ?> Tersedia
                </span>

                <div class="cover-box">
                    <i class="fas fa-book fa-4x opacity-25 <?= $is_borrowed ? 'text-warning' : 'text-primary' ?>"></i>
                </div>

                <div class="card-body p-4">
                    <h6 class="fw-bold mb-1 text-truncate"><?= htmlspecialchars($d['judul']) ?></h6>
                    <p class="text-primary small mb-2">By: <?= htmlspecialchars($d['pengarang']) ?></p>
                    <p class="sinopsis-short text-secondary"><?= htmlspecialchars($d['sinopsis'] ?: 'Belum ada ringkasan untuk buku ini.') ?></p>

                    <div class="d-grid gap-2">
                        <a href="detail_buku.php?id=<?= $d['id'] ?>" class="btn btn-detail btn-sm rounded-pill fw-bold mb-1">
                            <i class="fas fa-info-circle me-2"></i>DETAIL INFO
                        </a>

                        <?php if($is_borrowed): ?>
                            <a href="baca.php?id=<?= $d['id'] ?>" class="btn btn-warning fw-bold rounded-pill shadow-sm">
                                <i class="fas fa-book-open me-2"></i>BACA
                            </a>
                        <?php elseif($d['stok'] > 0): ?>
                            <a href="?pinjam=<?= $d['id'] ?><?= ($keyword != "") ? '&search='.$keyword : '' ?>" 
                               class="btn btn-primary fw-bold rounded-pill shadow-sm" onclick="return confirm('Pinjam buku ini?')">
                                <i class="fas fa-plus me-2"></i>PINJAM
                            </a>
                        <?php else: ?>
                            <button class="btn btn-secondary disabled rounded-pill opacity-50" disabled>STOK HABIS</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-search fa-3x text-secondary mb-3 opacity-25"></i>
                <p class="text-secondary">Buku yang Anda cari tidak ditemukan.</p>
                <a href="dashboard.php" class="btn btn-outline-primary rounded-pill px-4">Lihat Semua Buku</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="py-5 border-top border-secondary border-opacity-10 text-center">
    <p class="text-secondary small mb-0">© 2026 E-Lib Digital Library. Literasi Tanpa Batas.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;

    function applyTheme(theme) {
        if (theme === 'light') {
            body.classList.add('light-mode');
            body.classList.remove('bg-dark', 'text-white');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            body.classList.remove('light-mode');
            body.classList.add('bg-dark', 'text-white');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        }
    }

    // Konsistensi tema antar halaman
    const savedTheme = localStorage.getItem('theme') || 'dark';
    applyTheme(savedTheme);

    themeToggle.addEventListener('click', () => {
        const isLight = body.classList.toggle('light-mode');
        const newTheme = isLight ? 'light' : 'dark';
        localStorage.setItem('theme', newTheme);
        applyTheme(newTheme);
    });
</script>
</body>
</html>