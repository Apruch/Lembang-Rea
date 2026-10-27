<?php
$judul = 'Tentang Desa'; $aktif = 'profil';
require 'header.php';
$I = [
  'pin'   => $IKON['pin'],
  'luas'  => 'M8 3H5a2 2 0 0 0-2 2v3 M21 8V5a2 2 0 0 0-2-2h-3 M3 16v3a2 2 0 0 0 2 2h3 M16 21h3a2 2 0 0 0 2-2v-3',
  'arah'  => 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z M16 8l-2 6-6 2 2-6z',
  'daun'  => 'M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.2 2 8 0 5.5-4.8 10-10 10z M2 21c0-3 1.9-5.5 6-7',
  'gunung'=> 'M3 20l6-12 4 7 3-5 5 10z',
  'awan'  => 'M17.5 19a4.5 4.5 0 1 0-1.4-8.8A6 6 0 0 0 4.5 12 3.5 3.5 0 0 0 6 19z',
  'mata'  => 'M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z M12 9a3 3 0 1 0 0 6 3 3 0 0 0 0-6z',
  'target'=> 'M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8z',
  'orang' => $IKON['orang'], 'rumah' => $IKON['rumah'],
  'berita'=> 'M4 4h16v16H4z M8 8h8 M8 12h8 M8 16h5',
  'cek'   => $IKON['centang'],
];
$batas = [['Utara', teks('batas_utara')], ['Selatan', teks('batas_selatan')], ['Timur', teks('batas_timur')], ['Barat', teks('batas_barat')]];
$riwayat = barisTabel(teks('riwayat_kepala'));
$kampung = pendudukKampung();
$wakil = $organisasi[0]['anak'][0] ?? null;
?>
<div class="tp">
<?php heroHalaman('Tentang Desa', 'Mengenal Lembang Rea Tulaklangi', 'Asal-usul nama, perjalanan kepemimpinan, serta letak dan batas wilayah lembang kami.', ['Lihat Sejarah', '#sejarah']); ?>

<section class="tp-sec">
  <div class="tp-duo">
    <div>
      <p class="tp-eyebrow">Pemimpin Lembang</p>
      <h2 class="tp-h2">Kenali Pemimpin Kami</h2>
      <p class="tp-lead"><?= h(teks('pemimpin_teks')) ?></p>
      <a class="tp-btn" href="organisasi.php">Lihat Struktur Organisasi</a>
    </div>
    <div class="tp-orang">
      <div><div class="tp-foto" style="background-image:url('<?= h(gambarSet('kades')) ?>')" role="img" aria-label="Foto Kepala Lembang"></div><p class="tp-nama"><?= h(kepalaNama()) ?></p><p class="tp-peran"><?= h(kepalaJabatan()) ?></p></div>
      <?php if ($wakil): ?>
      <div><div class="tp-foto" style="<?= $wakil['foto'] ? "background-image:url('uploads/".h($wakil['foto'])."')" : 'background-image:linear-gradient(150deg,#c6ec9a,#2e8f12)' ?>" role="img" aria-label="<?= h($wakil['jab']) ?>"></div><p class="tp-nama"><?= h($wakil['nama']) ?></p><p class="tp-peran"><?= h($wakil['jab']) ?></p></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="tp-sec tp-alt" id="sejarah">
  <div class="tp-sejarah">
    <p class="tp-eyebrow">Perjalanan Kami</p>
    <h2 class="tp-h2">Sejarah Lembang</h2>
    <?= blokTeks(teks('sejarah')) ?>
  </div>
  <?php if ($riwayat): ?>
  <div class="tp-tabel-wrap">
    <table class="tp-tabel">
      <caption>Daftar Nama Kepala Lembang</caption>
      <thead><tr><th>No</th><th>Nama</th><th>Masa Jabatan</th><th>Keterangan</th></tr></thead>
      <tbody><?php foreach ($riwayat as $i => $k): ?>
        <tr><td data-label="No"><?= $i + 1 ?></td><td data-label="Nama"><strong><?= h($k[0] ?? '') ?></strong></td><td data-label="Masa Jabatan"><?= h($k[1] ?? '') ?></td><td data-label="Keterangan"><?= h($k[2] ?? '') ?></td></tr>
      <?php endforeach; ?></tbody>
    </table>
  </div>
  <?php endif; ?>
</section>

<section class="tp-sec">
  <div class="tp-tengah"><p class="tp-eyebrow">Asal Nama</p><h2 class="tp-h2">Makna Nama Rea Tulaklangi'</h2><p class="tp-lead">Menopang langit yang ditumbuhi ilalang.</p></div>
  <div class="tp-cols tp-c3">
    <div class="tp-col"><div class="ik"><?= ikon($I['daun']) ?></div><h3>Rea</h3><p>Rumput ilalang dalam bahasa Toraja.</p></div>
    <div class="tp-col"><div class="ik"><?= ikon($I['gunung']) ?></div><h3>Tulak</h3><p>Menopang, merujuk pada ketinggian atau gunung yang menjulang tinggi.</p></div>
    <div class="tp-col"><div class="ik"><?= ikon($I['awan']) ?></div><h3>Langi'</h3><p>Langit.</p></div>
  </div>
</section>

<section class="tp-sec tp-alt">
  <div class="tp-tengah"><p class="tp-eyebrow">Arah Desa</p><h2 class="tp-h2">Visi dan Misi</h2></div>
  <div class="tp-cols tp-c2">
    <div class="tp-col"><div class="ik"><?= ikon($I['mata']) ?></div><h3>Visi</h3><p><?= nl2br(h(teks('visi'))) ?></p></div>
    <div class="tp-col"><div class="ik"><?= ikon($I['target']) ?></div><h3>Misi</h3><p><?= nl2br(h(teks('misi'))) ?></p></div>
  </div>
</section>

<section class="tp-sec">
  <p class="tp-eyebrow">Demografi</p>
  <h2 class="tp-h2">Letak dan Batas Wilayah</h2>
  <p class="tp-lead" style="margin-bottom:28px"><?= h(teks('demografi_intro')) ?></p>
  <div class="tp-usp">
    <div class="tp-6">
      <div class="tp-mini"><div class="ik"><?= ikon($I['luas']) ?></div><h3>Luas Wilayah</h3><p><?= h(teks('luas')) ?></p></div>
      <div class="tp-mini"><div class="ik"><?= ikon($I['arah']) ?></div><h3>Letak</h3><p><?= h(teks('letak')) ?></p></div>
      <?php foreach ($batas as $b): ?>
        <div class="tp-mini"><div class="ik"><?= ikon($I['pin']) ?></div><h3>Sebelah <?= h($b[0]) ?></h3><p><?= h($b[1]) ?></p></div>
      <?php endforeach; ?>
    </div>
    <div class="tp-gelap">
      <h3>Sekilas Lembang</h3>
      <ul><?php foreach (barisList(teks('sekilas')) as $t): ?><li><span><?= ikon($I['cek']) ?></span><?= h($t) ?></li><?php endforeach; ?></ul>
    </div>
  </div>
</section>

<?php if ($kampung): ?>
<section class="tp-sec tp-alt">
  <div class="tp-tengah"><p class="tp-eyebrow">Kependudukan</p><h2 class="tp-h2">Jumlah Penduduk per Kampung</h2><p class="tp-lead">Total <?= h(teks('stat_penduduk')) ?> jiwa, data sampai bulan <?= h(teks('penduduk_per')) ?>.</p></div>
  <div class="tp-kp">
    <?php $maks = max(max($kampung), 1); $no = 1; foreach ($kampung as $nm => $jml): ?>
      <div class="tp-kpk">
        <h3><?= $no++ ?>. Kampung <?= h($nm) ?></h3>
        <b><?= number_format($jml, 0, ',', '.') ?> <small>jiwa</small></b>
        <div class="tp-bar" role="img" aria-label="<?= h($nm) ?>: <?= $jml ?> jiwa"><i style="width:<?= round($jml / $maks * 100) ?>%"></i></div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<section class="tp-sec">
  <div class="tp-tengah"><p class="tp-eyebrow">Desa dalam Angka</p><h2 class="tp-h2">Angka yang berbicara</h2></div>
  <div class="tp-num">
    <div class="tp-n"><div class="ik"><?= ikon($I['pin']) ?></div><div><b><?= h(teks('stat_dusun')) ?></b><span>Kampung</span></div></div>
    <div class="tp-n"><div class="ik"><?= ikon($I['orang']) ?></div><div><b><?= h(teks('stat_penduduk')) ?></b><span>Penduduk (jiwa)</span></div></div>
    <div class="tp-n"><div class="ik"><?= ikon($I['rumah']) ?></div><div><b><?= h(teks('stat_kk')) ?></b><span>Kepala Keluarga</span></div></div>
    <div class="tp-n"><div class="ik"><?= ikon($I['berita']) ?></div><div><b><?= h(statBerita()) ?></b><span>Berita Dipublikasikan</span></div></div>
  </div>
</section>

<section class="tp-cta">
  <h2>Butuh layanan atau informasi lebih lanjut?</h2>
  <p>Pemerintah lembang siap melayani warga di seluruh kampung.</p>
  <div class="gap"><a class="tp-btn putih" href="index.php#layanan">Layanan Desa</a><a class="tp-btn garis" href="kontak.php">Hubungi Kami</a></div>
</section>
</div>
<?php require 'footer.php'; ?>
