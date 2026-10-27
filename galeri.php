<?php
$judul = 'Galeri'; $aktif = 'galeri';
require 'header.php';
$kats = ['Alam', 'UMKM', 'Budaya'];
$kat = in_array($_GET['kategori'] ?? '', $kats, true) ? $_GET['kategori'] : '';
if ($kat) {
    $s = $koneksi->prepare('SELECT * FROM galeri WHERE kategori=? ORDER BY id DESC'); $s->bind_param('s', $kat); $s->execute();
    $galeri = $s->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $galeri = $koneksi->query('SELECT * FROM galeri ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC);
}
?>
<div class="tp">
<?php heroHalaman('Informasi', 'Galeri Desa', 'Keindahan alam, produk UMKM, dan kegiatan budaya Lembang Rea Tulaklangi.', ['Lihat Galeri', '#daftar']); ?>

<section class="tp-sec tp-alt" id="daftar">
  <div class="tp-tengah"><p class="tp-eyebrow">Potensi Desa</p><h2 class="tp-h2">Potret Lembang Rea Tulaklangi</h2></div>
  <div class="tp-chips">
    <a class="tp-chip<?= $kat === '' ? ' on' : '' ?>" href="galeri.php#daftar">Semua</a>
    <?php foreach ($kats as $k): ?><a class="tp-chip<?= $kat === $k ? ' on' : '' ?>" href="galeri.php?kategori=<?= urlencode($k) ?>#daftar"><?= $k ?></a><?php endforeach; ?>
  </div>
  <?php if (!$galeri): ?><p class="tp-kosong">Belum ada foto pada kategori ini.</p><?php endif; ?>
  <div class="tp-masonry">
    <?php foreach ($galeri as $g): ?>
      <figure class="tp-shot"<?= $g['gambar'] ? ' style="background-image:url(\'uploads/'.h($g['gambar']).'\')"' : '' ?>>
        <figcaption><span class="tp-tag"><?= h($g['kategori']) ?></span><?= h($g['judul']) ?></figcaption>
      </figure>
    <?php endforeach; ?>
  </div>
</section>
<?php ctaHalaman('Ingin berkunjung ke desa kami?', 'Hubungi kantor lembang untuk informasi kunjungan dan kegiatan.', ['Hubungi Kami', 'kontak.php'], ['Tentang Desa', 'profil.php']); ?>
</div>
<?php require 'footer.php'; ?>
