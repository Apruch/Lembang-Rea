<footer class="foot" id="kontak">
  <div class="foot-grid">
    <div>
      <h3>Lembang Rea Tulaklangi</h3>
      <p>Alamat kantor: <?= h($kontak['alamat']) ?></p>
      <p>Telepon / WhatsApp: <?= h($kontak['telp']) ?></p>
    </div>
    <div>
      <h3>Tautan cepat</h3>
      <p><a href="index.php">Beranda</a></p><p><a href="profil.php">Profil</a></p><p><a href="index.php#layanan">Layanan</a></p><p><a href="organisasi.php">Organisasi</a></p><p><a href="berita.php">Berita</a></p><p><a href="galeri.php">Galeri</a></p><p><a href="kontak.php">Kontak</a></p>
    </div>
    <iframe class="map" title="Peta lokasi Lembang Rea Tulaklangi" loading="lazy" src="<?= h($kontak['peta']) ?>"></iframe>
  </div>
  <p class="copy">&copy; <?= date('Y') ?> Lembang Rea Tulaklangi. Hak cipta dilindungi.</p>
</footer>
<script src="script.js?v=<?= filemtime(__DIR__.'/script.js') ?>" defer></script>
</body>
</html>
