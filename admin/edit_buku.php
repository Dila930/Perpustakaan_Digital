<?php
session_start();
include "../config/koneksi.php";

// Proteksi halaman
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

// Ambil data buku
if(!isset($_GET['id'])) {
    header("Location: buku.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$query = mysqli_query($conn, "SELECT * FROM buku WHERE id='$id'");
$d = mysqli_fetch_assoc($query);

if(!$d) {
    header("Location: buku.php");
    exit;
}

// Logika Update
if(isset($_POST['update'])){
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $pengarang = mysqli_real_escape_string($conn, $_POST['pengarang']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun = mysqli_real_escape_string($conn, $_POST['tahun']);
    $stok = (int)$_POST['stok'];
    $sinopsis = mysqli_real_escape_string($conn, $_POST['sinopsis']);

    $upd = $conn->prepare("UPDATE buku SET judul=?, pengarang=?, penerbit=?, tahun_terbit=?, stok=?, sinopsis=? WHERE id=?");
    $upd->bind_param("ssssisi", $judul, $pengarang, $penerbit, $tahun, $stok, $sinopsis, $id);
    
    if($upd->execute()){
        header("Location: buku.php?msg=updated");
    } else {
        echo "Error: " . $conn->error;
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku | <?= htmlspecialchars($d['judul']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg shadow-sm mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold text-primary"><i class="fas fa-edit me-2"></i>EDIT KOLEKSI</span>
        <div class="ms-auto d-flex align-items-center">
            <button id="theme-toggle" class="btn btn-outline-secondary border-0 rounded-circle me-3">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>
            <a href="buku.php" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                <i class="fas fa-times me-1"></i> Batal
            </a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg overflow-hidden">
                <div class="card-header bg-primary bg-opacity-10 border-0 py-3">
                    <h5 class="mb-0 fw-bold text-primary text-center">Formulir Perubahan Data</h5>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase" style="letter-spacing: 1px;">Judul Buku</label>
                            <input name="judul" class="form-control form-control-lg shadow-sm" 
                                   value="<?= htmlspecialchars($d['judul']) ?>" required>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-uppercase">Pengarang</label>
                                <input name="pengarang" class="form-control shadow-sm" 
                                       value="<?= htmlspecialchars($d['pengarang']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-uppercase">Penerbit</label>
                                <input name="penerbit" class="form-control shadow-sm" 
                                       value="<?= htmlspecialchars($d['penerbit']) ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-uppercase">Tahun Terbit</label>
                                <input name="tahun" type="number" class="form-control shadow-sm" 
                                       value="<?= $d['tahun_terbit'] ?>">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-uppercase">Stok Koleksi</label>
                                <input name="stok" type="number" class="form-control shadow-sm" 
                                       value="<?= $d['stok'] ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-uppercase">Sinopsis</label>
                            <textarea name="sinopsis" rows="6" class="form-control shadow-sm" 
                                      placeholder="Tulis ringkasan buku..."><?= htmlspecialchars($d['sinopsis']) ?></textarea>
                        </div>

                        <div class="d-grid gap-2 mt-5">
                            <button type="submit" name="update" class="btn btn-primary btn-lg rounded-pill shadow">
                                <i class="fas fa-check-circle me-2"></i>Simpan Perubahan
                            </button>
                            <a href="buku.php" class="btn btn-link text-secondary text-decoration-none">Kembali ke Daftar Buku</a>
                        </div>
                    </form>
                </div>
            </div>
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