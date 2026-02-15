<?php
session_start();
include "../config/koneksi.php";

// 1. Proteksi halaman
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

// 2. Logika Tambah Buku (Prepared Statement)
if(isset($_POST['tambah'])){
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $pengarang = mysqli_real_escape_string($conn, $_POST['pengarang']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun = mysqli_real_escape_string($conn, $_POST['tahun']);
    $stok = (int)$_POST['stok'];
    $sinopsis = mysqli_real_escape_string($conn, $_POST['sinopsis']);

    $stmt = $conn->prepare("INSERT INTO buku (judul, pengarang, penerbit, tahun_terbit, stok, sinopsis) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssis", $judul, $pengarang, $penerbit, $tahun, $stok, $sinopsis);
    
    if($stmt->execute()){
        header("Location: buku.php?msg=added");
    } else {
        echo "Error: " . $conn->error;
    }
    exit;
}

// 3. Logika Hapus Buku
if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn,"DELETE FROM buku WHERE id='$id'");
    header("Location: buku.php?msg=deleted");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* Sinkronisasi Modal dengan Style CSS Anda */
        .modal-content {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
        }
        .modal-header { border-bottom: 1px solid var(--border-color) !important; }
        .modal-footer { border-top: 1px solid var(--border-color) !important; background: transparent !important; }
        
        /* Tombol Aksi Custom agar menyatu dengan tabel */
        .btn-action-group {
            background: var(--bg-input);
            border: 1px solid var(--border-color);
            padding: 4px;
            border-radius: 50px;
            display: inline-flex;
        }
        .btn-action {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s;
            color: var(--text-main);
            text-decoration: none;
        }
        .btn-action:hover { background: rgba(255,255,255,0.1); transform: scale(1.1); }
        
        /* Badge Custom */
        .stok-badge {
            background: rgba(13, 110, 253, 0.1);
            color: var(--primary-blue);
            border: 1px solid var(--primary-blue);
            font-weight: 600;
        }

        body:not(.light-mode) .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg shadow-sm mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold text-primary"><i class="fas fa-book me-2"></i>KELOLA KOLEKSI</span>
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

<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-0">Master Data Buku</h3>
            <p class="text-secondary small mb-0">Kelola informasi buku dan stok perpustakaan</p>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="fas fa-plus me-2"></i>Tambah Buku
        </button>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-primary border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            Data buku berhasil <strong><?= ($_GET['msg'] == 'added') ? 'ditambahkan' : (($_GET['msg'] == 'deleted') ? 'dihapus' : 'diperbarui') ?></strong>!
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="px-4 py-3" width="5%">NO</th>
                        <th width="25%">INFORMASI BUKU</th>
                        <th>PENGARANG</th>
                        <th width="25%">SINOPSIS</th>
                        <th class="text-center">STOK</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no=1;
                    $data = mysqli_query($conn,"SELECT * FROM buku ORDER BY id DESC");
                    while($d = mysqli_fetch_assoc($data)):
                    ?>
                    <tr>
                        <td class="px-4 text-muted small"><?= $no++ ?></td>
                        <td>
                            <div class="fw-bold"><?= htmlspecialchars($d['judul']) ?></div>
                            <div class="text-primary small" style="font-size: 0.75rem;"><?= htmlspecialchars($d['penerbit']) ?> (<?= $d['tahun_terbit'] ?>)</div>
                        </td>
                        <td class="small"><?= htmlspecialchars($d['pengarang']) ?></td>
                        <td>
                            <div class="text-secondary small text-truncate" style="max-width: 200px;">
                                <?= htmlspecialchars($d['sinopsis'] ?: 'Tidak ada sinopsis.') ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-pill stok-badge px-3">
                                <?= $d['stok'] ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-action-group shadow-sm">
                                <a href="isi_buku.php?id_buku=<?= $d['id'] ?>" class="btn-action" title="Isi Buku">
                                    <i class="fas fa-list-ol text-warning"></i>
                                </a>
                                <a href="edit_buku.php?id=<?= $d['id'] ?>" class="btn-action" title="Edit">
                                    <i class="fas fa-edit text-info"></i>
                                </a>
                                <a href="?hapus=<?= $d['id'] ?>" class="btn-action" onclick="return confirm('Hapus buku ini?')" title="Hapus">
                                    <i class="fas fa-trash text-danger"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Input Data Buku</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Judul Buku</label>
                        <input name="judul" class="form-control" placeholder="Masukkan judul buku" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Pengarang</label>
                            <input name="pengarang" class="form-control" placeholder="Nama penulis">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Penerbit</label>
                            <input name="penerbit" class="form-control" placeholder="Nama penerbit">
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Tahun Terbit</label>
                            <input name="tahun" type="number" class="form-control" placeholder="Contoh: 2024">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Stok Awal</label>
                            <input name="stok" type="number" class="form-control" value="1">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Sinopsis Singkat</label>
                        <textarea name="sinopsis" rows="4" class="form-control" placeholder="Tulis ringkasan cerita..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Koleksi</button>
                </div>
            </form>
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