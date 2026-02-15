<?php
session_start();
include "../config/koneksi.php";

// Proteksi Admin
if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}

// Logika Hapus
if(isset($_GET['hapus'])){
    $id = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn,"DELETE FROM users WHERE id='$id' AND role='user'");
    header("Location: anggota.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Anggota | Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg shadow-sm mb-4">
    <div class="container">
        <span class="navbar-brand fw-bold text-primary"><i class="fas fa-users-cog me-2"></i>KELOLA ANGGOTA</span>
        <div class="ms-auto d-flex align-items-center">
            <button id="theme-toggle" class="btn btn-outline-secondary border-0 rounded-circle me-3">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>
            <a href="dashboard.php" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-opacity-10 bg-primary">
                        <tr>
                            <th class="px-4 py-3" width="5%">No</th>
                            <th>Nama Lengkap</th>
                            <th>Alamat Email</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no=1;
                        $data = mysqli_query($conn,"SELECT * FROM users WHERE role='user' ORDER BY nama ASC");
                        if(mysqli_num_rows($data) > 0):
                            while($d = mysqli_fetch_assoc($data)):
                        ?>
                        <tr>
                            <td class="px-4 text-muted small"><?= $no++ ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($d['nama']) ?></td>
                            <td class="text-secondary"><?= htmlspecialchars($d['email']) ?></td>
                            <td class="text-center">
                                <a href="?hapus=<?= $d['id'] ?>" 
                                   class="btn btn-outline-danger btn-sm rounded-pill px-3" 
                                   onclick="return confirm('Yakin ingin menghapus anggota ini?')">
                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Belum ada anggota terdaftar.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Logika Sinkronisasi Tema
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

    // Ambil tema dari localStorage
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