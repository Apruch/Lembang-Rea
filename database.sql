CREATE DATABASE IF NOT EXISTS db_desa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_desa;

CREATE TABLE IF NOT EXISTS admin (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS berita (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(200) NOT NULL,
  kategori ENUM('Berita','Pengumuman','Agenda') NOT NULL DEFAULT 'Berita',
  isi TEXT NOT NULL,
  gambar VARCHAR(100) DEFAULT NULL,
  tanggal DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS galeri (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(150) NOT NULL,
  kategori ENUM('Alam','UMKM','Budaya') NOT NULL DEFAULT 'Alam',
  gambar VARCHAR(100) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pengaturan (
  kunci VARCHAR(50) PRIMARY KEY,
  nilai TEXT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS layanan (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, slug VARCHAR(40) NOT NULL UNIQUE, judul VARCHAR(100) NOT NULL,
  deskripsi TEXT NOT NULL, ikon VARCHAR(20) NOT NULL DEFAULT 'dokumen',
  persyaratan TEXT, urutan INT NOT NULL DEFAULT 0, aktif TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS perangkat (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) NOT NULL, jabatan VARCHAR(100) NOT NULL,
  parent_id INT UNSIGNED DEFAULT NULL, urutan INT NOT NULL DEFAULT 0, foto VARCHAR(100) DEFAULT NULL
) ENGINE=InnoDB;
-- Data awal layanan dan struktur organisasi diisi otomatis saat situs pertama kali dibuka.

-- Password kedua akun: admin123 (bcrypt via password_hash)
INSERT INTO admin (username, password, nama_lengkap) VALUES
('admin1', '$2y$10$LCQizUd2TPChZz5XVlKydOaVBAUFzlunFeXqMew1BiTTzYRaQwBm.', 'Administrator 1'),
('admin2', '$2y$10$LCQizUd2TPChZz5XVlKydOaVBAUFzlunFeXqMew1BiTTzYRaQwBm.', 'Administrator 2');

-- Contoh data awal (boleh dihapus lewat dasbor)
INSERT INTO berita (judul, kategori, isi, tanggal) VALUES
('Musyawarah Lembang Bahas Rencana Pembangunan', 'Berita', 'Perangkat lembang bersama warga dari seluruh kampung membahas prioritas pembangunan tahun berikutnya.', CURDATE()),
('Jadwal Pelayanan Administrasi Kantor Lembang', 'Pengumuman', 'Pelayanan surat-menyurat dibuka setiap hari kerja pukul 08.00 sampai 15.00.', CURDATE()),
('Gotong Royong Bersih Kampung', 'Agenda', 'Seluruh warga diundang mengikuti kerja bakti bersama pada akhir pekan ini.', CURDATE());
INSERT INTO galeri (judul, kategori) VALUES
('Hamparan sawah di pagi hari', 'Alam'),
('Tenun dan kerajinan warga', 'UMKM'),
('Upacara adat kampung', 'Budaya');
