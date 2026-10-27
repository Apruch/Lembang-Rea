<?php
require_once 'koneksi.php';
require_once 'fungsi.php';
$judul = $judul ?? 'Beranda';
$aktif = $aktif ?? '';
$logo = gambarSet('logo');
$p = fn($d) => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="'.$d.'"/></svg>';
$menu = [
  ['Beranda', 'index.php', 'beranda'],
  ['Profil', [
    ['Tentang Desa', 'Sambutan dan sejarah Lembang Rea Tulaklangi', 'profil.php', 'profil', 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z M9 22V12h6v10'],
    ['Struktur Organisasi', 'Bagan perangkat Pemerintah Lembang', 'organisasi.php', 'organisasi', 'M12 3v6 M5 15v-3h14v3 M12 9v3 M3 15h4v6H3z M17 15h4v6h-4z M10 15h4v6h-4z'],
  ]],
  ['Informasi', [
    ['Berita', 'Artikel, pengumuman, dan agenda desa', 'berita.php', 'berita', 'M4 4h16v16H4z M8 8h8 M8 12h8 M8 16h5'],
    ['Galeri', 'Foto alam, UMKM, dan budaya desa', 'galeri.php', 'galeri', 'M3 3h18v18H3z M8.5 10a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z M21 15l-5-5L5 21'],
  ]],
  ['Kontak', 'kontak.php', 'kontak'],
];
$chev = '<svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($judul) ?> - Lembang Rea Tulaklangi</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css?v=<?= filemtime(__DIR__.'/style.css') ?>">
<link rel="stylesheet" href="halaman.css?v=<?= filemtime(__DIR__.'/halaman.css') ?>">
</head>
<body class="situs">
<header class="nav">
  <a class="brand" href="index.php">
    <?php if ($logo): ?><img class="logo" src="<?= h($logo) ?>" alt="Logo Lembang Rea Tulaklangi"><?php else: ?><svg viewBox="0 0 40 40" width="40" height="40" aria-hidden="true"><circle cx="20" cy="20" r="20" fill="#58c41c"/><path d="M12 28c0-9 6-15 16-16 0 10-6 16-16 16zm0 0l9-9" fill="none" stroke="#0a3d27" stroke-width="2.5" stroke-linecap="round"/></svg><?php endif; ?>
    <span>Lembang Rea Tulaklangi</span>
  </a>
  
  <button class="burger" id="burger" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="menu">
    <span class="burger-bar" aria-hidden="true"></span>
  </button>
  
  <nav class="menu" id="menu" aria-label="Navigasi utama">
    <ul>
    <?php foreach ($menu as $m): ?>
      <?php if (is_array($m[1])): $on = in_array($aktif, array_column($m[1], 3), true); ?>
      <li class="has-dd<?= $on ? ' on' : '' ?>">
        <button type="button" class="dd-btn" aria-expanded="false" aria-haspopup="true"><?= h($m[0]) ?> <?= $chev ?></button>
        <div class="dd">
          <?php foreach ($m[1] as $c): ?>
          <a href="<?= $c[2] ?>"<?= $aktif === $c[3] && $c[3] ? ' class="on" aria-current="page"' : '' ?>>
            <span class="di"><?= $p($c[4]) ?></span>
            <span><b><?= h($c[0]) ?></b><small><?= h($c[1]) ?></small></span>
          </a>
          <?php endforeach; ?>
        </div>
      </li>
      <?php else: ?>
      <li<?= $aktif === $m[2] ? ' class="on"' : '' ?>><a href="<?= $m[1] ?>"<?= $aktif === $m[2] ? ' aria-current="page"' : '' ?>><?= h($m[0]) ?></a></li>
      <?php endif; ?>
    <?php endforeach; ?>
    </ul>
    <a href="login.php" class="btn btn-outline login-m">Login Admin</a>
  </nav>
  <a href="login.php" class="btn btn-outline login">Login Admin</a>
</header>
