<?php
$judul = 'Beranda'; $aktif = 'beranda';
require 'header.php';
$berita = $koneksi->query("SELECT * FROM berita ORDER BY tanggal DESC, id DESC LIMIT 3")->fetch_all(MYSQLI_ASSOC);
?>
<section class="hero" style="background-image:url('<?= h(gambarSet('hero')) ?>')">
  <div class="hero-text">
    <h1><?= h(teks('hero_judul')) ?></h1>
    <p><?= h(teks('hero_sub')) ?></p>
    <div class="row">
      <a href="profil.php" class="btn btn-green">Jelajahi</a>
      <a href="#layanan" class="btn btn-ghost">Layanan</a>
    </div>
  </div>
  <div class="stats">
    <div class="stat"><b><?= h(teks('stat_dusun')) ?></b><span>Kampung</span></div>
    <div class="stat"><b><?= h(teks('stat_penduduk')) ?></b><span>Penduduk (jiwa)</span></div>
  </div>
</section>

<section class="sec putih">
  <div class="welcome">
    <div class="kades-foto" style="background-image:url('<?= h(gambarSet('kades')) ?>')" role="img" aria-label="Foto Kepala Lembang"></div>
    <div>
      <span class="pill soft"><?= ikon('M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2 M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8') ?> Sambutan</span>
      <h2>Sambutan Kepala Lembang</h2>
      <h3 class="kades-nama"><?= h(kepalaNama()) ?></h3>
      <p class="kades-jab"><?= h(teks('sambutan_jabatan')) ?></p>
      <blockquote class="quote">"<?= h(teks('sambutan_kutipan')) ?>"</blockquote>
      <p class="isi-sambutan"><?= nl2br(h(teks('sambutan_isi'))) ?></p>
      <div class="ttd">
        <strong><?= h(kepalaNama()) ?></strong>
        <span><?= h(mb_strtoupper(kepalaJabatan())) ?></span>
      </div>
      <a href="profil.php" class="btn btn-outline">Baca Selengkapnya</a>
    </div>
  </div>
</section>

<section class="sec band" id="layanan">
  <div class="head"><span class="pill">Layanan</span><h2>Layanan yang <span class="hl">kami sediakan</span></h2></div>
  <?php if (!$layanan): ?><p class="empty">Belum ada layanan yang ditampilkan.</p><?php endif; ?>
  <div class="grid slider"><?php foreach ($layanan as $l) kartuLayananInfo($l); ?></div>
</section>

<section class="sec">
  <div class="head"><span class="pill">Berita &amp; Kegiatan</span><h2>Kabar terbaru <span class="hl">dari desa</span></h2></div>
  <?php if (!$berita): ?><p class="empty">Belum ada berita.</p><?php endif; ?>
  <div class="grid g3 slider"><?php foreach ($berita as $b) kartuBerita($b); ?></div>
  <p class="more"><a href="berita.php" class="btn btn-outline">Berita Lainnya</a></p>
</section>
<?php require 'footer.php'; ?>
