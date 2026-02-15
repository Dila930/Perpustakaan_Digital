-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 15 Feb 2026 pada 10.30
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpus_sekolah`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `buku`
--

CREATE TABLE `buku` (
  `id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `pengarang` varchar(255) NOT NULL,
  `penerbit` varchar(255) DEFAULT NULL,
  `tahun_terbit` year(4) DEFAULT NULL,
  `sinopsis` varchar(1500) NOT NULL,
  `isbn` varchar(50) DEFAULT NULL,
  `stok` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `buku`
--

INSERT INTO `buku` (`id`, `judul`, `pengarang`, `penerbit`, `tahun_terbit`, `sinopsis`, `isbn`, `stok`) VALUES
(1, 'Petualangan Kode PHP', 'Gemini AI', 'Gramedia', '2016', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', NULL, 8),
(2, 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', '2005', 'Kisah persahabatan sepuluh anak dari keluarga miskin di Pulau Belitung yang berjuang menggapai mimpi lewat sekolah.', NULL, 12),
(3, 'Bumi', 'Tere Liye', 'Gramedia', '2014', 'Awal petualangan tiga remaja, Raib, Seli, dan Ali, di dunia paralel yang penuh kekuatan magis dan teknologi canggih.', NULL, 8),
(4, 'Filosofi Kopi', 'Dee Lestari', 'Trudee Books', '2006', 'Kumpulan cerita pendek tentang Ben dan Jody yang mencari ramuan kopi terbaik hingga menemukan arti kehidupan.', NULL, 5),
(5, 'Atomic Habits', 'James Clear', 'Gramedia', '2019', 'Panduan praktis membangun kebiasaan baik dan menghilangkan kebiasaan buruk melalui perubahan kecil yang konsisten.', NULL, 14),
(6, 'Sang Pemimpi', 'Andrea Hirata', 'Bentang Pustaka', '2006', 'Sekuel dari Laskar Pelangi yang menceritakan masa remaja Ikal dan saudara sepupunya dalam mencari identitas diri.', NULL, 7),
(7, 'Cantik Itu Luka', 'Eka Kurniawan', 'Gramedia', '2002', 'Sebuah novel realisme magis yang menggabungkan sejarah kolonial Indonesia dengan tragedi keluarga yang unik.', NULL, 4),
(8, 'Home Deus', 'Yuval Noah Harari', 'Manasuka', '2015', 'Menelusuri masa depan umat manusia di mana teknologi AI dan bioteknologi mulai mengubah hakikat kehidupan.', NULL, 10),
(9, 'Hujan', 'Tere Liye', 'Gramedia', '2016', 'Kisah tentang persahabatan dan cinta di dunia masa depan yang telah dihantam bencana alam dahsyat.', NULL, 6),
(10, 'Negeri 5 Menara', 'Ahmad Fuadi', 'Gramedia', '2009', 'Cerita tentang enam santri dari berbagai daerah yang bertemu di pesantren dan bermimpi menaklukkan dunia.', NULL, 9),
(11, 'Sapiens', 'Yuval Noah Harari', 'Kepustakaan Populer Gramedia', '2011', 'Sejarah singkat umat manusia mulai dari revolusi kognitif hingga era modern yang mendominasi planet bumi.', NULL, 11);

-- --------------------------------------------------------

--
-- Struktur dari tabel `isi_buku`
--

CREATE TABLE `isi_buku` (
  `id_isi` int(11) NOT NULL,
  `id_buku` int(11) NOT NULL,
  `part_ke` int(11) NOT NULL,
  `judul_part` varchar(255) NOT NULL,
  `konten` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `isi_buku`
--

INSERT INTO `isi_buku` (`id_isi`, `id_buku`, `part_ke`, `judul_part`, `konten`) VALUES
(1, 1, 1, 'Awal Mula Variabel', 'Dahulu kala, ada sebuah variabel bernama $data...'),
(2, 1, 2, 'Misteri Loop Tak Terbatas', 'Ketika perulangan while(true) dijalankan, dunia mulai melambat...'),
(3, 2, 1, 'Sepuluh Anggota Baru', 'Di sebuah sekolah dasar yang nyaris roboh di Belitung, sembilan anak menunggu dengan cemas. Harapan mereka untuk sekolah bergantung pada kehadiran satu anak lagi...'),
(4, 2, 2, 'Pohon Menjadi Saksi', 'Lintang datang menerjang hujan dengan sepedanya yang butut. Ia adalah jenius dari pesisir yang akan mengubah warna di kelas kecil itu selamanya...'),
(5, 3, 1, 'Gadis Biasa', 'Namaku Raib, usiaku 15 tahun. Aku sama seperti remaja lainnya, kecuali satu hal: aku bisa menghilang hanya dengan menutupkan tangan ke wajahku...'),
(6, 3, 2, 'Munculnya Tamu Tak Diundang', 'Sesosok tinggi kurus muncul di dalam cermin kamarku. Ia menyebut dirinya Tamu dari jauh, dan seketika duniaku yang tenang berubah menjadi petualangan antar dimensi...'),
(7, 4, 1, 'Ben & Jody', 'Ben adalah seorang perfeksionis dalam hal kopi. Baginya, setiap cangkir kopi memiliki jiwa. Namun tantangan terbesar muncul ketika seseorang meminta kopi yang sempurna...'),
(8, 5, 1, 'Kekuatan 1 Persen', 'Perubahan kecil yang dilakukan secara konsisten akan menghasilkan perubahan luar biasa. Jangan fokus pada tujuan, fokuslah pada sistem yang kamu bangun setiap hari...'),
(9, 5, 2, 'Cara Membangun Kebiasaan Baru', 'Gunakan rumus: Setelah [Kebiasaan Lama], aku akan melakukan [Kebiasaan Baru]. Ini adalah teknik stacking habits yang sangat efektif...'),
(10, 6, 1, 'Mimpi di Bawah Altar', 'Ikal, Arai, dan Jimbron adalah tiga remaja yang merajut mimpi di SMA bukan main. Mereka berjanji akan menginjakkan kaki di altar suci Universitas Sorbonne, Paris...'),
(11, 7, 1, 'Kebangkitan Dewi Ayu', 'Satu sore di akhir pekan di bulan Maret, Dewi Ayu bangkit dari kuburannya setelah dua puluh satu tahun kematian. Kecantikannya yang legendaris masih menyisakan misteri...'),
(12, 9, 1, 'Tahun 2042', 'Dunia sudah sangat canggih, namun bencana gunung meletus yang dahsyat menghancurkan hampir seluruh peradaban. Di sinilah Lail bertemu dengan Esok...'),
(13, 11, 1, 'Revolusi Kognitif', 'Sekitar 70.000 tahun yang lalu, organisme yang termasuk spesies Homo Sapiens mulai membentuk struktur yang jauh lebih rumit yang disebut budaya...'),
(14, 11, 2, 'Imajinasi Kolektif', 'Apa yang membuat manusia menguasai dunia? Jawabannya adalah kemampuan kita untuk percaya pada hal-hal yang hanya ada dalam imajinasi, seperti uang, negara, dan hukum...');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `buku_id` int(11) NOT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `status` enum('dipinjam','kembali') DEFAULT 'dipinjam',
  `terakhir_baca` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi`
--

INSERT INTO `transaksi` (`id`, `user_id`, `buku_id`, `tanggal_pinjam`, `tanggal_kembali`, `status`, `terakhir_baca`) VALUES
(9, 1, 1, '2026-02-14', '2026-02-21', 'kembali', 0),
(10, 2, 1, '2026-02-14', '2026-02-14', '', 0),
(11, 2, 1, '2026-02-14', '2026-02-14', '', 0),
(12, 2, 1, '2026-02-14', NULL, 'dipinjam', 2),
(13, 1, 11, '2026-02-14', '2026-02-15', '', 1),
(14, 2, 5, '2026-02-15', NULL, 'dipinjam', 2),
(15, 1, 10, '2026-02-15', '2026-02-15', '', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`) VALUES
(1, 'Administrator Perpustakaan', 'admin@mail.com', 'admin123', 'admin'),
(2, 'Budi Siswa', 'budi@mail.com', 'user123', 'user'),
(3, 'Siti Siswa', 'siti@mail.com', 'user123', 'user');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `isi_buku`
--
ALTER TABLE `isi_buku`
  ADD PRIMARY KEY (`id_isi`),
  ADD KEY `id_buku` (`id_buku`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `buku_id` (`buku_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `isi_buku`
--
ALTER TABLE `isi_buku`
  MODIFY `id_isi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `isi_buku`
--
ALTER TABLE `isi_buku`
  ADD CONSTRAINT `isi_buku_ibfk_1` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`buku_id`) REFERENCES `buku` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
