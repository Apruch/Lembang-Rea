<?php
session_start();
require 'koneksi.php'; require 'fungsi.php';
adminCek();
$grup = isset($KONTEN[$_GET['grup'] ?? '']) ? $_GET['grup'] : 'Beranda';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfOk()) { http_response_code(403); exit('Sesi tidak valid.'); }
    $grup = isset($KONTEN[$_POST['grup'] ?? '']) ? $_POST['grup'] : 'Beranda';
    $baru = [];
    foreach ($KONTEN[$grup] as $k => $def) {
        $v = trim(str_replace("\r\n", "\n", $_POST[$k] ?? ''));
        if (mb_strlen($v) > 12000) { $err = 'Teks "'.$def[0].'" terlalu panjang.'; break; }
        if ($k === 'k_peta' && $v !== '') {
            if (preg_match('/src="([^"]+)"/i', $v, $m)) { $v = html_entity_decode($m[1]); }
            if (strpos($v, 'https://www.google.com/maps') !== 0) { $err = 'Peta harus berupa tautan atau kode embed dari Google Maps.'; break; }
        }
        if (in_array($k, ['k_fb', 'k_ig', 'k_yt'], true) && $v !== '' && !preg_match('#^https?://#i', $v)) { $err = 'Tautan media sosial harus diawali https://'; break; }
        $baru[$k] = $v;
    }
    if (!$err) {
        $ins = $koneksi->prepare('INSERT INTO pengaturan (kunci, nilai) VALUES (?, ?) ON DUPLICATE KEY UPDATE nilai=VALUES(nilai)');
        $del = $koneksi->prepare('DELETE FROM pengaturan WHERE kunci=?');
        foreach ($baru as $k => $v) {
            if ($v === '') { $del->bind_param('s', $k); $del->execute(); }
            else { $ins->bind_param('ss', $k, $v); $ins->execute(); }
        }
        header('Location: admin_konten.php?grup='.urlencode($grup).'&ok=1'); exit;
    }
}
adminAtas('Teks & Statistik', 'konten');
?>
<p>Ubah teks yang tampil di situs. Kosongkan kolom untuk kembali ke teks bawaan (ditampilkan samar di dalam kolom). Foto dan logo diatur di tab "Gambar &amp; Logo"; nama dan foto perangkat di tab "Struktur Organisasi".</p>
<div class="tabs" style="margin-top:0">
  <?php foreach (array_keys($KONTEN) as $g): ?><a class="btn btn-sm btn-outline <?= $g === $grup ? 'on' : '' ?>" href="?grup=<?= urlencode($g) ?>"><?= h($g) ?></a><?php endforeach; ?>
</div>
<?php if ($err): ?><div class="alert"><?= h($err) ?></div><?php endif; ?>
<form method="post" class="card">
  <input type="hidden" name="csrf" value="<?= csrf() ?>">
  <input type="hidden" name="grup" value="<?= h($grup) ?>">
  <?php foreach ($KONTEN[$grup] as $k => $d): $nilai = $_POST[$k] ?? ($PENGATURAN[$k] ?? ''); ?>
    <label for="f-<?= h($k) ?>"><?= h($d[0]) ?></label>
    <?php if ($d[1] === 'text'): ?>
      <input id="f-<?= h($k) ?>" type="text" name="<?= h($k) ?>" value="<?= h($nilai) ?>" placeholder="<?= h($d[2]) ?>">
    <?php else: ?>
      <textarea id="f-<?= h($k) ?>" name="<?= h($k) ?>" rows="<?= $d[1] === 'besar' ? 16 : 4 ?>" placeholder="<?= h($d[2]) ?>"><?= h($nilai) ?></textarea>
    <?php endif; ?>
  <?php endforeach; ?>
  <div class="row" style="margin-top:20px"><button class="btn btn-green" type="submit">Simpan</button></div>
</form>
<?php adminBawah(); ?>
