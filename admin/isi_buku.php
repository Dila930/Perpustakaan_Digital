<?php
session_start();
include "../config/koneksi.php";

// Proteksi Halaman Admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

// Pastikan parameter id_buku ada
if(!isset($_GET['id_buku'])){
    header("Location: buku.php");
    exit;
}

$id_buku = mysqli_real_escape_string($conn, $_GET['id_buku']);

// Ambil info buku untuk judul halaman
$buku_info = mysqli_query($conn, "SELECT judul FROM buku WHERE id = '$id_buku'");
$b = mysqli_fetch_assoc($buku_info);

if (!$b) {
    die("Buku tidak ditemukan.");
}

// 1. Logika Tambah/Edit Episode
if(isset($_POST['simpan_isi'])){
    $id_isi = $_POST['id_isi']; 
    $part_ke = (int)$_POST['part_ke'];
    $judul_part = mysqli_real_escape_string($conn, $_POST['judul_part']);
    $konten = mysqli_real_escape_string($conn, $_POST['konten']);

    if(empty($id_isi)){
        $stmt = $conn->prepare("INSERT INTO isi_buku (id_buku, part_ke, judul_part, konten) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $id_buku, $part_ke, $judul_part, $konten);
    } else {
        $stmt = $conn->prepare("UPDATE isi_buku SET part_ke=?, judul_part=?, konten=? WHERE id_isi=?");
        $stmt->bind_param("issi", $part_ke, $judul_part, $konten, $id_isi);
    }
    
    if($stmt->execute()){
        header("Location: isi_buku.php?id_buku=$id_buku&msg=success");
    } else {
        header("Location: isi_buku.php?id_buku=$id_buku&msg=error");
    }
    exit;
}

// 2. Logika Hapus Episode
if(isset($_GET['hapus_isi'])){
    $id_isi = mysqli_real_escape_string($conn, $_GET['hapus_isi']);
    mysqli_query($conn, "DELETE FROM isi_buku WHERE id_isi = '$id_isi'");
    header("Location: isi_buku.php?id_buku=$id_buku&msg=deleted");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isi Buku: <?= htmlspecialchars($b['judul']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* Tambahan CSS Khusus Modal agar sinkron dengan style.css Anda */
        .modal-content {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
        }
        .modal-header {
            border-bottom: 1px solid var(--border-color) !important;
        }
        .modal-footer {
            background-color: transparent !important;
            border-top: 1px solid var(--border-color) !important;
        }
        /* Menghilangkan background putih bawaan bootstrap modal */
        .btn-close {
            filter: var(--text-main) == '#ffffff' ? invert(1) : none;
        }
        body.light-mode .btn-close { filter: none; }
        body:not(.light-mode) .btn-close { filter: invert(1); }
    </style>
</head>
<body class="dark"> <nav class="navbar navbar-expand-lg shadow-sm mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold text-primary"><i class="fas fa-list-ol me-2"></i>KELOLA EPISODE</span>
        <div class="ms-auto d-flex align-items-center">
            <button id="theme-toggle" class="btn btn-outline-secondary border-0 rounded-circle me-3">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>
            <a href="buku.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">Kembali</a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Episode: <?= htmlspecialchars($b['judul']) ?></h4>
            <p class="text-secondary small mb-0">Manajemen konten bab dan urutan part</p>
        </div>
        <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="tambahEpisode()">
            <i class="fas fa-plus me-2"></i>Tambah Part
        </button>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert <?= $_GET['msg'] == 'deleted' ? 'alert-danger' : 'alert-success' ?> border-0 shadow-sm mb-4" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            Data berhasil <strong><?= ($_GET['msg'] == 'success') ? 'disimpan' : 'dihapus' ?></strong>!
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="fw-bold">
                        <th class="px-4 py-3" width="10%">URUTAN</th>
                        <th width="30%">JUDUL PART</th>
                        <th>PREVIEW KONTEN</th>
                        <th class="text-center" width="15%">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $res = mysqli_query($conn, "SELECT * FROM isi_buku WHERE id_buku = '$id_buku' ORDER BY part_ke ASC");
                    if(mysqli_num_rows($res) == 0):
                    ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted small">Belum ada konten untuk buku ini.</td>
                    </tr>
                    <?php endif; while($row = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td class="px-4">
                            <span class="badge bg-primary bg-opacity-10 text-primary">Part <?= $row['part_ke'] ?></span>
                        </td>
                        <td class="fw-bold"><?= htmlspecialchars($row['judul_part']) ?></td>
                        <td class="text-secondary small">
                            <div class="text-truncate" style="max-width: 300px;">
                                <?= strip_tags($row['konten']) ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="btn-group bg-input rounded-pill p-1 shadow-sm">
                                <button onclick='editEpisode(<?= json_encode($row) ?>)' class="btn btn-sm btn-link text-info">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?id_buku=<?= $id_buku ?>&hapus_isi=<?= $row['id_isi'] ?>" 
                                   class="btn btn-sm btn-link text-danger" onclick="return confirm('Hapus episode ini?')">
                                    <i class="fas fa-trash"></i>
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

<div class="modal fade" id="modalIsi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="modalTitle">Tambah Episode</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_isi" id="id_isi">
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Part Ke-</label>
                            <input type="number" name="part_ke" id="part_ke" class="form-control shadow-sm" required>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label small fw-bold">Judul Part/Bab</label>
                            <input type="text" name="judul_part" id="judul_part" class="form-control shadow-sm" placeholder="Contoh: Perkenalan..." required>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold">Konten Cerita</label>
                        <textarea name="konten" id="konten" rows="10" class="form-control shadow-sm" placeholder="Tuliskan isi bab..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="simpan_isi" class="btn btn-primary rounded-pill px-4">Simpan Konten</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const modal = new bootstrap.Modal(document.getElementById('modalIsi'));

    function tambahEpisode() {
        document.getElementById('id_isi').value = '';
        document.getElementById('modalTitle').innerText = 'Tambah Episode Baru';
        document.getElementById('part_ke').value = '';
        document.getElementById('judul_part').value = '';
        document.getElementById('konten').value = '';
        modal.show();
    }

    function editEpisode(data) {
        document.getElementById('id_isi').value = data.id_isi;
        document.getElementById('modalTitle').innerText = 'Edit Episode: ' + data.judul_part;
        document.getElementById('part_ke').value = data.part_ke;
        document.getElementById('judul_part').value = data.judul_part;
        document.getElementById('konten').value = data.konten;
        modal.show();
    }

    // Logic Tema agar sinkron
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;

    function applyTheme(theme) {
        if (theme === 'light') {
            body.classList.add('light-mode');
            body.classList.remove('dark');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            body.classList.remove('light-mode');
            body.classList.add('dark');
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