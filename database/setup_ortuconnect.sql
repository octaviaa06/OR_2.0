-- ============================================================
-- OrtuConnect Database Setup
-- Database: u137138991_ortuconnect2_0
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+07:00";

-- --------------------------------------------------------
-- Tabel: admin
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `nama_admin` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_admin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin` (`id_admin`, `nama_admin`, `email`) VALUES
(1, 'Administrator', 'admin@ortuconnect.com');

-- --------------------------------------------------------
-- Tabel: guru
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `guru` (
  `id_guru` int(11) NOT NULL AUTO_INCREMENT,
  `nama_guru` varchar(100) NOT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `kelas` varchar(10) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `alamat` varchar(255) NOT NULL,
  PRIMARY KEY (`id_guru`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `guru` (`id_guru`, `nama_guru`, `nip`, `kelas`, `email`, `no_telp`, `alamat`) VALUES
(1, 'Siti Aminah', '198501012010012001', 'A', 'siti.aminah@ortuconnect.com', '081234567891', 'Jl. Melati No. 1, Nganjuk'),
(2, 'Budi Santoso', '198601012010011002', 'B', 'budi.santoso@ortuconnect.com', '081234567892', 'Jl. Mawar No. 2, Nganjuk');

-- --------------------------------------------------------
-- Tabel: siswa
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `siswa` (
  `id_siswa` int(11) NOT NULL AUTO_INCREMENT,
  `nama_siswa` varchar(100) NOT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `gender` varchar(20) NOT NULL,
  `alamat` text DEFAULT NULL,
  `nama_ortu` varchar(100) DEFAULT NULL,
  `no_telp_ortu` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_siswa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `siswa` (`id_siswa`, `nama_siswa`, `kelas`, `tanggal_lahir`, `gender`, `alamat`, `nama_ortu`, `no_telp_ortu`) VALUES
(1, 'Ahmad Fauzi', 'A', '2019-03-15', 'Laki-Laki', 'Jl. Kenanga No. 5, Nganjuk', 'Hendra Fauzi', '081298765401'),
(2, 'Sari Dewi', 'A', '2019-07-22', 'Perempuan', 'Jl. Anggrek No. 3, Nganjuk', 'Dewi Rahayu', '081298765402'),
(3, 'Rizky Pratama', 'A', '2019-05-10', 'Laki-Laki', 'Jl. Dahlia No. 7, Nganjuk', 'Agus Pratama', '081298765403'),
(4, 'Putri Cantika', 'B', '2019-09-18', 'Perempuan', 'Jl. Flamboyan No. 2, Nganjuk', 'Rini Cantika', '081298765404'),
(5, 'Dafa Ramadhan', 'B', '2019-01-25', 'Laki-Laki', 'Jl. Bougenville No. 9, Nganjuk', 'Ramadhan Ali', '081298765405'),
(6, 'Nayla Azzahra', 'B', '2019-11-30', 'Perempuan', 'Jl. Cempaka No. 4, Nganjuk', 'Azzahra Putri', '081298765406');

-- --------------------------------------------------------
-- Tabel: akun
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `akun` (
  `id_akun` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','guru','ortu') NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `id_guru` int(11) DEFAULT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `fcm_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_akun`),
  UNIQUE KEY `username` (`username`),
  KEY `fk_akun_admin` (`id_admin`),
  KEY `fk_akun_guru` (`id_guru`),
  KEY `fk_akun_siswa` (`id_siswa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `akun` (`id_akun`, `username`, `password`, `role`, `id_admin`, `id_guru`, `id_siswa`) VALUES
-- Admin (password: admin123)
(1, 'admin', 'admin123', 'admin', 1, NULL, NULL),
-- Guru Kelas A (password: guru123)
(2, 'guru_a', 'guru123', 'guru', NULL, 1, NULL),
-- Guru Kelas B (password: guru123)
(3, 'guru_b', 'guru123', 'guru', NULL, 2, NULL),
-- Ortu siswa (password: ortu123)
(4, 'ortu_ahmad', 'ortu123', 'ortu', NULL, NULL, 1),
(5, 'ortu_sari', 'ortu123', 'ortu', NULL, NULL, 2),
(6, 'ortu_rizky', 'ortu123', 'ortu', NULL, NULL, 3),
(7, 'ortu_putri', 'ortu123', 'ortu', NULL, NULL, 4),
(8, 'ortu_dafa', 'ortu123', 'ortu', NULL, NULL, 5),
(9, 'ortu_nayla', 'ortu123', 'ortu', NULL, NULL, 6);

-- --------------------------------------------------------
-- Tabel: absensi
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `absensi` (
  `id_absensi` int(11) NOT NULL AUTO_INCREMENT,
  `id_siswa` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('Hadir','Izin','Sakit','Alpa') NOT NULL,
  PRIMARY KEY (`id_absensi`),
  KEY `fk_absensi_siswa` (`id_siswa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `absensi` (`id_siswa`, `tanggal`, `status`) VALUES
(1, CURDATE(), 'Hadir'),
(2, CURDATE(), 'Hadir'),
(3, CURDATE(), 'Izin'),
(4, CURDATE(), 'Hadir'),
(5, CURDATE(), 'Sakit'),
(6, CURDATE(), 'Hadir');

-- --------------------------------------------------------
-- Tabel: izin
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `izin` (
  `id_izin` int(11) NOT NULL AUTO_INCREMENT,
  `id_siswa` int(11) NOT NULL,
  `tanggal_pengajuan` datetime DEFAULT CURRENT_TIMESTAMP,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `jenis_izin` enum('Sakit','Izin') DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status` enum('Menunggu','Disetujui','Ditolak') DEFAULT 'Menunggu',
  `alasan_penolakan` text DEFAULT NULL,
  `tanggal_verifikasi` datetime DEFAULT NULL,
  PRIMARY KEY (`id_izin`),
  KEY `fk_izin_siswa` (`id_siswa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `izin` (`id_siswa`, `tanggal_pengajuan`, `tanggal_mulai`, `tanggal_selesai`, `jenis_izin`, `keterangan`, `status`) VALUES
(3, NOW(), CURDATE(), CURDATE(), 'Izin', 'Keperluan keluarga', 'Menunggu'),
(5, NOW(), CURDATE(), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'Sakit', 'Demam tinggi', 'Menunggu');

-- --------------------------------------------------------
-- Tabel: kalender
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `kalender` (
  `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kegiatan` varchar(150) NOT NULL,
  `tanggal` date NOT NULL,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`id_kegiatan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `kalender` (`nama_kegiatan`, `tanggal`, `deskripsi`) VALUES
('Upacara Bendera', CURDATE(), 'Upacara bendera rutin setiap hari Senin'),
('Senam Pagi', DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'Senam bersama seluruh siswa dan guru'),
('Kunjungan Orang Tua', DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'Pertemuan wali murid dengan guru kelas'),
('Hari Libur Nasional', DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'Libur nasional, sekolah tidak masuk'),
('Pentas Seni', DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'Penampilan seni budaya siswa');

-- --------------------------------------------------------
-- Foreign Keys
-- --------------------------------------------------------
ALTER TABLE `absensi`
  ADD CONSTRAINT `fk_absensi_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `akun`
  ADD CONSTRAINT `fk_akun_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_akun_guru` FOREIGN KEY (`id_guru`) REFERENCES `guru` (`id_guru`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_akun_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE ON UPDATE SET NULL;

ALTER TABLE `izin`
  ADD CONSTRAINT `fk_izin_siswa` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`) ON DELETE CASCADE ON UPDATE CASCADE;
