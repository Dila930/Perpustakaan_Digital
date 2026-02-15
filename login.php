<?php
session_start();
include "config/koneksi.php";

$error = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $_SESSION['user'] = $user;
        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | E-Lib Smakaduta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #0d6efd;
            --transition-speed: 0.4s;
            --glass-bg-dark: rgba(30, 41, 59, 0.8);
            --glass-bg-light: rgba(255, 255, 255, 0.9);
        }

        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=2000') no-repeat center center;
            background-size: cover;
            transition: background-color var(--transition-speed);
            overflow: hidden;
        }

        /* Container Utama */
        .main-container {
            width: 900px;
            height: 550px;
            display: flex;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: all var(--transition-speed);
        }

        /* Login Side (Kiri) */
        .login-side {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: background-color var(--transition-speed), color var(--transition-speed);
        }

        /* Info Side (Kanan) */
        .info-side {
            flex: 1.2;
            background: rgba(13, 110, 253, 0.85);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px;
        }

        /* --- LOGIC DARK & LIGHT MODE --- */
        
        /* Dark Mode (Default) */
        body.dark-mode .login-side {
            background: var(--glass-bg-dark);
            color: #ffffff;
        }
        body.dark-mode .form-control {
            color: white;
            border-color: rgba(255,255,255,0.1);
        }

        /* Light Mode */
        body.light-mode .login-side {
            background: var(--glass-bg-light);
            color: #1e293b;
        }
        body.light-mode .auth-title { color: #1e293b !important; }
        body.light-mode .form-control {
            color: #1e293b !important;
            border-color: rgba(0,0,0,0.1) !important;
        }
        body.light-mode .text-secondary { color: #64748b !important; }

        .form-control:focus {
            box-shadow: none;
            border-color: var(--primary-blue) !important;
            background: transparent;
        }

        .theme-switch {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        @media (max-width: 768px) {
            .main-container { width: 95%; flex-direction: column; height: auto; }
            .info-side { display: none; }
        }
    </style>
</head>
<body class="dark-mode">

<div class="theme-switch">
    <button id="theme-toggle" class="btn btn-light rounded-circle shadow">
        <i id="theme-icon" class="fas fa-moon"></i>
    </button>
</div>

<div class="main-container">
    <div class="login-side">
        <div class="mb-4">
            <h2 class="fw-bold auth-title">Sign In</h2>
            <p class="text-secondary small">Masuk untuk mengakses layanan E-Library</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small border-0 mb-3">
                <i class="fas fa-exclamation-circle me-2"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small text-secondary">Alamat Email</label>
                <input type="email" name="email" id="email-input" class="form-control bg-transparent py-2" placeholder="nama@email.com" required>
            </div>
            
            <div class="mb-4">
                <label class="form-label small text-secondary">Password</label>
                <input type="password" name="password" id="pass-input" class="form-control bg-transparent py-2" placeholder="••••••••" required>
                <div class="text-end mt-1">
                    <a href="#" class="text-primary text-decoration-none" style="font-size: 0.75rem;">Lupa kata sandi?</a>
                </div>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100 py-2 fw-bold rounded-pill shadow-sm">
                SIGN IN
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="small text-secondary">
                Belum punya akun? <a href="register.php" class="text-primary fw-bold text-decoration-none">Daftar</a>
            </p>
        </div>
    </div>

    <div class="info-side">
        <i class="fas fa-book-reader fa-4x mb-4"></i>
        <h1 class="fw-bold mb-3">Halo, Teman!</h1>
        <p class="mb-4 opacity-75">Daftarkan diri Anda dan mulai gunakan layanan literasi digital kami segera.</p>
        <a href="register.php" class="btn btn-outline-light rounded-pill px-5 fw-bold">SIGN UP</a>
    </div>
</div>

<script>
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;

    function applyTheme(theme) {
        if (theme === 'light') {
            body.classList.add('light-mode');
            body.classList.remove('dark-mode');
            themeIcon.className = 'fas fa-sun text-warning';
            themeToggle.classList.replace('btn-light', 'btn-dark');
        } else {
            body.classList.remove('light-mode');
            body.classList.add('dark-mode');
            themeIcon.className = 'fas fa-moon text-dark';
            themeToggle.classList.replace('btn-dark', 'btn-light');
        }
    }

    // Load tema dari localStorage agar sinkron antar halaman
    const savedTheme = localStorage.getItem('theme') || 'dark';
    applyTheme(savedTheme);

    themeToggle.addEventListener('click', () => {
        const currentMode = body.classList.contains('light-mode') ? 'dark' : 'light';
        localStorage.setItem('theme', currentMode);
        applyTheme(currentMode);
    });
</script>

</body>
</html>