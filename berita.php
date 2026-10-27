<?php
$judul = 'Berita'; $aktif = 'berita';
require 'header.php';
$kats = ['Berita', 'Pengumuman', 'Agenda'];
$kat = in_array($_GET['kategori'] ?? '', $kats, true) ? $_GET['kategori'] : '';
if ($kat) {
    $s = $koneksi->prepare('SELECT * FROM berita WHERE kategori=? ORDER BY tanggal DESC, id DESC'); $s->bind_param('s', $kat); $s->execute();
    $berita = $s->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $berita = $koneksi->query('SELECT * FROM berita ORDER BY tanggal DESC, id DESC')->fetch_all(MYSQLI_ASSOC);
}
?>
<div class="tp">
<?php heroHalaman('Informasi', 'Berita dan Kegiatan Desa', 'Artikel, pengumuman, dan agenda terbaru dari Lembang Rea Tulaklangi.', ['Lihat Berita', '#daftar']); ?>

<section class="tp-sec tp-alt" id="daftar">
  <div class="tp-tengah"><p class="tp-eyebrow">Kabar Terbaru</p><h2 class="tp-h2">Semua informasi desa</h2></div>
  <div class="tp-chips">
    <a class="tp-chip<?= $kat === '' ? ' on' : '' ?>" href="berita.php#daftar">Semua</a>
    <?php foreach ($kats as $k): ?><a class="tp-chip<?= $kat === $k ? ' on' : '' ?>" href="berita.php?kategori=<?= urlencode($k) ?>#daftar"><?= $k ?></a><?php endforeach; ?>
  </div>
  <?php if (!$berita): ?><p class="tp-kosong">Belum ada berita pada kategori ini.</p><?php endif; ?>
  <div class="tp-posts">
    <?php foreach ($berita as $b): ?>
      <article class="tp-post">
        <div class="th"<?= $b['gambar'] ? ' style="background-image:url(\'uploads/'.h($b['gambar']).'\')"' : '' ?>></div>
        <div class="bd">
          <div class="mt"><span class="tp-tag"><?= h($b['kategori']) ?></span><time><?= date('d/m/Y', strtotime($b['tanggal'])) ?></time></div>
          <h3><?= h($b['judul']) ?></h3>
          <p><?= h(mb_strimwidth($b['isi'], 0, 140, '...')) ?></p>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php ctaHalaman('Punya informasi untuk dibagikan?', 'Hubungi kantor lembang atau lihat layanan yang tersedia.', ['Hubungi Kami', 'kontak.php'], ['Layanan Desa', 'index.php#layanan']); ?>
</div>
<?php require 'footer.php'; ?>
