<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../config/koneksi.php';

$user_id = $_SESSION['user_id'];

// Handle book borrowing
if(isset($_POST['pinjam'])) {
    $id_buku = $_POST['id_buku'];
    $tanggal_pinjam = date('Y-m-d');
    $tanggal_kembali = date('Y-m-d', strtotime('+7 days'));
    
    // Check if user has overdue books
    $overdue = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE id_user = $user_id AND status='dipinjam' AND tanggal_kembali < CURDATE()")->fetch_assoc()['total'];
    
    if($overdue > 0) {
        $error = "Anda memiliki buku yang terlambat dikembalikan!";
    } else {
        // Check if book is available
        $buku = mysqli_query($conn, "SELECT stok FROM buku WHERE id_buku=$id_buku")->fetch_assoc();
        if($buku['stok'] > 0) {
            // Check if user already borrowed this book and hasn't returned it
            $already_borrowed = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi WHERE id_user = $user_id AND id_buku = $id_buku AND status='dipinjam'")->fetch_assoc()['total'];
            
            if($already_borrowed > 0) {
                $error = "Anda sudah meminjam buku ini!";
            } else {
                mysqli_query($conn, "INSERT INTO transaksi (id_user, id_buku, tanggal_pinjam, tanggal_kembali, status) VALUES ('$user_id', '$id_buku', '$tanggal_pinjam', '$tanggal_kembali', 'dipinjam')");
                mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id_buku=$id_buku");
                header("Location: pinjam.php");
            }
        } else {
            $error = "Stok buku tidak tersedia!";
        }
    }
}

// Get available books
$buku = mysqli_query($conn, "SELECT * FROM buku WHERE stok > 0 ORDER BY judul");

// Search functionality
$search = '';
if(isset($_GET['search'])) {
    $search = $_GET['search'];
    $buku = mysqli_query($conn, "SELECT * FROM buku WHERE stok > 0 AND (judul LIKE '%$search%' OR pengarang LIKE '%$search%' OR penerbit LIKE '%$search%') ORDER BY judul");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam Buku - Perpus Digital</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .header { background: #3498db; color: white; padding: 1rem; }
        .nav { display: flex; justify-content: space-between; align-items: center; }
        .nav-links a { color: white; text-decoration: none; margin: 0 1rem; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .btn { padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #3498db; color: white; }
        .btn-success { background: #27ae60; color: white; }
        .btn-danger { background: #e74c3c; color: white; }
        .card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .card-header { padding: 1rem; border-bottom: 1px solid #eee; }
        .card-body { padding: 1rem; }
        .search-box { display: flex; gap: 0.5rem; margin-bottom: 1rem; }
        .search-input { flex: 1; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; }
        .book-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; }
        .book-card { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 1rem; transition: transform 0.2s; }
        .book-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .book-title { font-weight: bold; margin-bottom: 0.5rem; color: #2c3e50; }
        .book-info { color: #666; font-size: 0.9rem; margin-bottom: 0.25rem; }
        .book-stock { color: #27ae60; font-weight: bold; margin: 0.5rem 0; }
        .alert { padding: 1rem; margin-bottom: 1rem; border-radius: 4px; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="header">
        <div class="nav">
            <h1>Perpus Digital - Anggota</h1>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="pinjam.php">Pinjam Buku</a>
                <a href="riwayat.php">Riwayat</a>
                <a href="../logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if(isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        
        <?php if(isset($_GET['success'])) { ?>
            <div class="alert alert-success">Buku berhasil dipinjam!</div>
        <?php } ?>

        <div class="card">
            <div class="card-header">
                <h2>Pinjam Buku</h2>
            </div>
            <div class="card-body">
                <div class="search-box">
                    <form method="GET" style="display: flex; flex: 1;">
                        <input type="text" name="search" class="search-input" placeholder="Cari buku berdasarkan judul, pengarang, atau penerbit..." value="<?php echo $search; ?>">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </form>
                    <?php if($search) { ?>
                        <a href="pinjam.php" class="btn btn-danger">Reset</a>
                    <?php } ?>
                </div>

                <div class="book-grid">
                    <?php if(mysqli_num_rows($buku) > 0) { ?>
                        <?php while($row = mysqli_fetch_assoc($buku)) { ?>
                            <div class="book-card">
                                <div class="book-title"><?php echo $row['judul']; ?></div>
                                <div class="book-info">Pengarang: <?php echo $row['pengarang']; ?></div>
                                <div class="book-info">Penerbit: <?php echo $row['penerbit']; ?></div>
                                <div class="book-info">Tahun: <?php echo $row['tahun_terbit']; ?></div>
                                <div class="book-info">ISBN: <?php echo $row['isbn']; ?></div>
                                <div class="book-stock">Stok: <?php echo $row['stok']; ?></div>
                                <form method="POST" onsubmit="return confirm('Pinjam buku <?php echo $row['judul']; ?>?')">
                                    <input type="hidden" name="id_buku" value="<?php echo $row['id_buku']; ?>">
                                    <button type="submit" name="pinjam" class="btn btn-success" style="width: 100%;">Pinjam</button>
                                </form>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <p>Tidak ada buku tersedia untuk dipinjam.</p>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
