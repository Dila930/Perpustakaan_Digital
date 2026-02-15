<?php
session_start();
include "config/koneksi.php";

// Statistik Perpustakaan
$count_buku = mysqli_query($conn, "SELECT COUNT(*) as total FROM buku");
$buku = mysqli_fetch_assoc($count_buku)['total'] ?? 0;

$count_user = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'");
$user = mysqli_fetch_assoc($count_user)['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Lib Smakaduta | SMKN 2 Surakarta</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        :root {
            --primary-blue: #0d6efd;
            --transition-speed: 0.4s;
        }

        /* Smooth Scrolling */
        html { scroll-behavior: smooth; }

        /* Global Transition untuk Dark Mode */
        body, .card, .navbar, section {
            transition: background-color var(--transition-speed) ease, color var(--transition-speed) ease;
        }

        /* Hero Section */
        .hero-section {
            padding: 120px 0;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.08) 0%, rgba(0, 0, 0, 0) 100%);
        }

        /* Card Custom Styling */
        .feature-card {
            border-radius: 18px;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            border: 1px solid transparent !important;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(13, 110, 253, 0.15) !important;
            border-color: rgba(13, 110, 253, 0.2) !important;
        }

        /* Stat Circle & Icons */
        .stat-circle {
            width: 70px; height: 70px;
            display: flex; align-items: center; justify-content: center;
            background-color: var(--primary-blue);
            color: #ffffff !important;
            border-radius: 18px;
            margin-bottom: 1.5rem;
            font-size: 1.6rem;
            transition: transform 0.5s ease;
        }

        .feature-card:hover .stat-circle {
            transform: scale(1.1) rotate(8deg);
        }

        /* Maps Styling with Blue Accent for Dark Mode */
        .map-container iframe {
            transition: all 0.5s ease;
            border-radius: 20px;
        }

        .dark .map-container iframe {
            filter: invert(90%) hue-rotate(180deg) brightness(105%) contrast(90%) saturate(120%);
        }

        /* Gallery Activity */
        .gallery-img {
            width: 100%; height: 220px; object-fit: cover;
            border-radius: 15px; transition: 0.4s;
            cursor: pointer;
        }
        .gallery-img:hover { transform: scale(1.03); filter: brightness(1.1); }

        /* Social Buttons */
        .social-btn {
            width: 45px; height: 45px; display: inline-flex;
            align-items: center; justify-content: center;
            border-radius: 50%; background: rgba(13, 110, 253, 0.1);
            color: var(--primary-blue);
            transition: 0.3s; text-decoration: none;
        }
        .social-btn:hover { background: var(--primary-blue); color: #fff; transform: translateY(-5px); }

        /* Badge Styling */
        .badge-dev {
            background: rgba(13, 110, 253, 0.1);
            color: var(--primary-blue);
            letter-spacing: 1px;
            font-weight: 600;
        }
    </style>
</head>
<body class="dark-mode"> 

<nav class="navbar navbar-expand-lg sticky-top shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="#">
            <i class="fas fa-book-reader me-2"></i>E-LIB SMAKADUTA
        </a>
        <div class="ms-auto d-flex align-items-center">
            <button id="theme-toggle" class="btn btn-outline-secondary border-0 rounded-circle me-3">
                <i id="theme-icon" class="fas fa-moon"></i>
            </button>
            <?php if(isset($_SESSION['user'])): ?>
                <a href="user/dashboard.php" class="btn btn-primary rounded-pill px-4 shadow-sm">Dashboard</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary rounded-pill px-4 shadow-sm">Masuk / Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <h1 class="display-4 fw-bold mb-3">Literasi Digital <span class="text-primary">Smakaduta.</span></h1>
                <p class="lead text-secondary mb-4">Pusat referensi belajar, modul kejuruan, dan koleksi literatur resmi SMKN 2 Surakarta dalam satu genggaman.</p>
                <div class="d-flex gap-3">
                    <a href="login.php" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">Mulai Membaca</a>
                    <a href="#tentang" class="btn btn-outline-secondary btn-lg rounded-pill px-4">Tentang Kami</a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-center text-primary opacity-25" data-aos="zoom-in">
                <i class="fas fa-university fa-10x"></i>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-primary bg-opacity-10">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <h2 class="fw-bold text-primary"><span class="counter"><?= $buku ?></span>+</h2>
                <p class="text-secondary mb-0">Koleksi Buku</p>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <h2 class="fw-bold text-primary"><span class="counter"><?= $user ?></span>+</h2>
                <p class="text-secondary mb-0">Siswa Terdaftar</p>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <h2 class="fw-bold text-primary">Akreditasi A</h2>
                <p class="text-secondary mb-0">Standar Nasional</p>
            </div>
        </div>
    </div>
</section>

<section id="tentang" class="py-5">
    <div class="container py-5">
        <div class="row align-items-center g-5">
            <div class="col-md-6" data-aos="fade-right">
                <h2 class="fw-bold mb-4">Visi & Misi</h2>
                <p class="text-secondary lead">Menjadi jantung pendidikan SMKN 2 Surakarta yang berbasis teknologi informasi untuk mencetak lulusan kompeten.</p>
                <div class="mt-4">
                    <div class="d-flex mb-3 align-items-start">
                        <i class="fas fa-check-circle text-primary mt-1 me-3"></i>
                        <p class="mb-0">Menyediakan literatur teknik dan umum yang relevan dengan industri.</p>
                    </div>
                    <div class="d-flex mb-3 align-items-start">
                        <i class="fas fa-check-circle text-primary mt-1 me-3"></i>
                        <p class="mb-0">Mempermudah akses materi ajar melalui platform digital 24/7.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <div class="card border-0 shadow-sm p-4 bg-primary bg-opacity-5">
                    <h5 class="fw-bold text-primary"><i class="fas fa-quote-left me-2"></i>Slogan Kami</h5>
                    <p class="fst-italic mb-0 text-secondary">"Literasi Berkualitas, Teknologi Tanpa Batas, Smakaduta Berkelas."</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold">Layanan Digital</h2>
            <p class="text-secondary">Kemudahan akses untuk seluruh civitas akademika</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card feature-card p-4 h-100 text-center border-0 shadow-sm">
                    <div class="stat-circle mx-auto"><i class="fas fa-search"></i></div>
                    <h4 class="fw-bold">Pencarian Cepat</h4>
                    <p class="text-secondary small">Cari modul dan buku teknik berdasarkan kategori jurusan dengan instan.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card feature-card p-4 h-100 text-center border-0 shadow-sm">
                    <div class="stat-circle mx-auto"><i class="fas fa-mobile-alt"></i></div>
                    <h4 class="fw-bold">Akses Mobile</h4>
                    <p class="text-secondary small">Baca e-book dan jurnal kapan saja langsung melalui smartphone Anda.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="card feature-card p-4 h-100 text-center border-0 shadow-sm">
                    <div class="stat-circle mx-auto"><i class="fas fa-history"></i></div>
                    <h4 class="fw-bold">E-Inventory</h4>
                    <p class="text-secondary small">Pantau status peminjaman buku fisik secara real-time dari akun Anda.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light-section">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="fw-bold">Galeri Aktivitas</h2>
            <p class="text-secondary">Suasana literasi di Perpustakaan SMKN 2 Surakarta</p>
        </div>
        <div class="row g-3">
            <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="100"><img src="https://images.unsplash.com/photo-1521587760476-6c12a4b040da?q=80&w=500" class="gallery-img" alt="Aktivitas 1"></div>
            <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="200"><img src="https://images.unsplash.com/photo-1497633762265-9d179a990aa6?q=80&w=500" class="gallery-img" alt="Aktivitas 2"></div>
            <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="300"><img src="https://images.unsplash.com/photo-1529148482759-b35b25c5f217?q=80&w=500" class="gallery-img" alt="Aktivitas 3"></div>
            <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="400"><img src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=500" class="gallery-img" alt="Aktivitas 4"></div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container py-5">
        <div class="text-center mb-5 mx-auto" style="max-width: 700px;" data-aos="fade-up">
            <h2 class="fw-bold">Pengelola & Pengembang</h2>
            <div class="mb-3 mx-auto" style="width: 60px; height: 4px; background: var(--primary-blue); border-radius: 2px;"></div>
            <p class="text-secondary">Sinergi antara manajemen literasi dan inovasi teknologi untuk mewujudkan layanan modern.</p>
        </div>

        <div class="row g-4 justify-content-center mb-5">
            <div class="col-md-6 col-lg-5" data-aos="fade-up">
                <div class="card feature-card border-0 shadow-sm p-4 h-100 text-center">
                    <div class="stat-circle mx-auto bg-success mb-3" style="background-color: #198754 !important;">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Nama Guru, S.Pd</h5>
                    <p class="text-primary fw-medium small mb-3">Kepala Perpustakaan</p>
                    <p class="text-secondary small">Penanggung jawab utama kebijakan perpustakaan dan pengawasan operasional harian E-Lib.</p>
                </div>
            </div>
        </div>

        <div class="row g-4 justify-content-center">
            <?php
            $devs = [
                ['name' => 'Fadhilah Nor .M', 'role' => 'Fullstack Developer', 'icon' => 'fa-code', 'color' => '#eef2ff', 'text' => '#4f46e5'],
                ['name' => 'Kirana Putri .A', 'role' => 'UI/UX Designer', 'icon' => 'fa-paint-brush', 'color' => '#fff1f2', 'text' => '#e11d48'],
                ['name' => 'Mutia Putri .n.a', 'role' => 'Backend Developer', 'icon' => 'fa-database', 'color' => '#f0fdf4', 'text' => '#16a34a'],
                ['name' => 'Ocha Viantika .S', 'role' => 'DevOps & QA', 'icon' => 'fa-server', 'color' => '#fff7ed', 'text' => '#ea580c'],
            ];
            foreach($devs as $index => $dev): ?>
            <div class="col-md-6 col-lg-3 text-center" data-aos="fade-up" data-aos-delay="<?= ($index+1)*100 ?>">
                <div class="card feature-card border-0 shadow-sm p-4 h-100">
                    <div class="stat-circle mx-auto mb-3" style="background-color: <?= $dev['color'] ?> !important; color: <?= $dev['text'] ?> !important;">
                        <i class="fas <?= $dev['icon'] ?>"></i>
                    </div>
                    <h6 class="fw-bold mb-1"><?= $dev['name'] ?></h6>
                    <p class="text-primary small fw-bold mb-2"><?= $dev['role'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="kontak" class="py-5 bg-primary bg-opacity-10">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-7" data-aos="fade-right">
                <div class="map-container rounded-4 shadow-sm overflow-hidden" style="height: 520px;">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.1234!2d110.80!3d-7.55!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a169e6b!2sSMK%20Negeri%202%20Surakarta!5e0!3m2!1sid!2sid!4v1700000000000" 
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
            <div class="col-lg-5" data-aos="fade-left">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <h4 class="fw-bold mb-4">Informasi & Kontak</h4>
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary"><i class="fas fa-clock me-2"></i>Jam Operasional</h6>
                        <ul class="list-unstyled schedule-list small">
                            <li class="d-flex justify-content-between py-2 border-bottom"><span>Senin - Kamis</span> <span>07:00 - 16:00</span></li>
                            <li class="d-flex justify-content-between py-2 border-bottom"><span>Jumat</span> <span>07:00 - 14:00</span></li>
                            <li class="d-flex justify-content-between py-2 text-danger fw-bold"><span>Sabtu - Minggu</span> <span>Tutup</span></li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary"><i class="fas fa-comments me-2"></i>Hubungi Kami</h6>
                        <div class="d-flex gap-2 mt-3">
                            <a href="#" class="social-btn"><i class="fab fa-whatsapp"></i></a>
                            <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-btn"><i class="fas fa-envelope"></i></a>
                            <a href="#" class="social-btn"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>
                    <div>
                        <h6 class="fw-bold text-primary"><i class="fas fa-map-marker-alt me-2"></i>Alamat</h6>
                        <p class="text-secondary small mb-0">Jl. Adi Sucipto No.33, Manahan, Kec. Banjarsari, Kota Surakarta, Jawa Tengah 57139</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="py-5 border-top text-center mt-5">
    <div class="container">
        <p class="text-secondary mb-0 small">© 2026 E-Lib Digital Library SMKN 2 Surakarta. Dibuat dengan <i class="fas fa-heart text-danger"></i> untuk Literasi Indonesia.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // Initialize Animations
    AOS.init({ duration: 1000, once: true });

    // Counting Animation Logic
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const updateCount = () => {
            const target = +counter.innerText;
            const count = +counter.getAttribute('data-count') || 0;
            const speed = target / 100;
            if (count < target) {
                const nextCount = Math.ceil(count + speed);
                counter.innerText = nextCount;
                counter.setAttribute('data-count', nextCount);
                setTimeout(updateCount, 20);
            } else {
                counter.innerText = target;
            }
        };
        
        // Trigger counting when visible
        const observer = new IntersectionObserver((entries) => {
            if(entries[0].isIntersecting) updateCount();
        }, { threshold: 0.5 });
        observer.observe(counter);
    });

    // Theme Toggle Logic
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');
    const body = document.body;

    function applyTheme(theme) {
        if (theme === 'light') {
            body.classList.add('light-mode');
            body.classList.remove('dark-mode');
            body.classList.remove('dark'); // untuk filter maps
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            body.classList.remove('light-mode');
            body.classList.add('dark-mode');
            body.classList.add('dark'); // untuk filter maps
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        }
    }

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