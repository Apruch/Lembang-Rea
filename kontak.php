<?php
$judul = 'Kontak'; $aktif = 'kontak';
require 'header.php';
$wa = preg_replace('/\D/', '', $kontak['telp']);
if (strpos($wa, '0') === 0) { $wa = '62'.substr($wa, 1); }
$cek = 'M20 6L9 17l-5-5';
?>
<div class="tp">
<?php heroHalaman('Kontak', 'Hubungi Kami', 'Kunjungi kantor lembang atau hubungi kami untuk informasi dan layanan.', ['Lihat Peta', '#peta']); ?>

<section class="tp-sec">
  <p class="tp-eyebrow">Informasi Kontak</p>
  <h2 class="tp-h2">Temui kami di kantor lembang</h2>
  <div class="tp-usp" style="margin-top:26px">
    <div class="tp-6 dua">
      <div class="tp-mini"><div class="ik"><?= ikon('M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z M12 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6z') ?></div><h3>Alamat Kantor</h3><p><?= h($kontak['alamat']) ?></p></div>
      <a class="tp-mini" href="https://wa.me/<?= h($wa) ?>" target="_blank" rel="noopener"><div class="ik"><?= ikon('M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.2a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z') ?></div><h3>Telepon / WhatsApp</h3><p><?= h($kontak['telp']) ?></p></a>
      <a class="tp-mini" href="mailto:<?= h($kontak['email']) ?>"><div class="ik"><?= ikon('M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z M22 6l-10 7L2 6') ?></div><h3>Email</h3><p><?= h($kontak['email']) ?></p></a>
      <div class="tp-mini"><div class="ik"><?= ikon('M18 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z M6 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z M18 22a3 3 0 1 0 0-6 3 3 0 0 0 0 6z M8.6 13.5l6.8 4 M15.4 6.5l-6.8 4') ?></div><h3>Media Sosial</h3><p><?php $ada = false; foreach (['fb' => 'Facebook', 'ig' => 'Instagram', 'yt' => 'YouTube'] as $kk => $nm) { if ($kontak[$kk]) { $ada = true; echo '<a href="'.h($kontak[$kk]).'" target="_blank" rel="noopener" style="margin-right:12px;color:var(--hijau-teks);font-weight:600">'.$nm.'</a>'; } } if (!$ada) { echo 'Belum ada tautan media sosial.'; } ?></p></div>
    </div>
    <div class="tp-gelap">
      <h3>Jam Pelayanan</h3>
      <ul>
        <?php foreach ($kontak['jam'] as $j): ?>
          <li><span><?= ikon($cek) ?></span><div><strong><?= h($j[0]) ?></strong><br><?= h($j[1] ?? '') ?></div></li>
        <?php endforeach; ?>
      </ul>
      <p style="font-size:.85rem;margin-top:18px">Datang langsung ke kantor lembang pada jam pelayanan.</p>
    </div>
  </div>
</section>

<section class="tp-sec tp-alt" id="peta">
  <div class="tp-tengah"><p class="tp-eyebrow">Lokasi</p><h2 class="tp-h2">Peta Lembang Rea Tulaklangi</h2></div>
  <iframe class="tp-peta" title="Peta lokasi Lembang Rea Tulaklangi" loading="lazy" src="<?= h($kontak['peta']) ?>"></iframe>
</section>
<?php ctaHalaman('Butuh layanan administrasi?', 'Lihat daftar layanan beserta persyaratannya di beranda.', ['Lihat Layanan', 'index.php#layanan'], ['Tentang Desa', 'profil.php']); ?>
</div>
<?php require 'footer.php'; ?>
