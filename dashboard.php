<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
require 'koneksi.php';
require 'fungsi.php';
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }

$tab = in_array($_GET['tab'] ?? '', ['berita','galeri','pengaturan'], true) ? $_GET['tab'] : 'ringkasan';
$judulHal = ['ringkasan'=>'Ringkasan','berita'=>'Berita','galeri'=>'Galeri','pengaturan'=>'Gambar & Logo'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) { http_response_code(403); exit('Sesi tidak valid.'); }
    if (($_POST['tab'] ?? '') === 'pengaturan') {
        $k = $_POST['kunci'] ?? '';
        if (!in_array($k, ['hero','kades','logo'], true)) exit('Pengaturan tidak valid.');
        $lama = $PENGATURAN[$k] ?? null;
        if (($_POST['aksi'] ?? '') === 'reset') { hapusFile($lama); $baru = null; }
        else {
            $baru = unggah($_FILES['gambar'] ?? []);
            if (!$baru) { header('Location: dashboard.php?tab=pengaturan&err=1'); exit; }
            hapusFile($lama);
        }
        $s = $koneksi->prepare('INSERT INTO pengaturan (kunci, nilai) VALUES (?, ?) ON DUPLICATE KEY UPDATE nilai=VALUES(nilai)');
        $s->bind_param('ss', $k, $baru); $s->execute();
        header('Location: dashboard.php?tab=pengaturan&ok=1'); exit;
    }
    $t  = ($_POST['tab'] ?? '') === 'galeri' ? 'galeri' : 'berita';
    $id = (int)($_POST['id'] ?? 0);
    $tab = $t;

    if (($_POST['aksi'] ?? '') === 'hapus') {
        $s = $koneksi->prepare("SELECT gambar FROM $t WHERE id=?"); $s->bind_param('i', $id); $s->execute();
        if ($r = $s->get_result()->fetch_assoc()) hapusFile($r['gambar']);
        $s = $koneksi->prepare("DELETE FROM $t WHERE id=?"); $s->bind_param('i', $id); $s->execute();
        header("Location: dashboard.php?tab=$t&ok=2"); exit;
    }

    $judul = trim($_POST['judul'] ?? '');
    $img = unggah($_FILES['gambar'] ?? []);
    if ($judul === '' || $img === false) {
        $error = 'Judul wajib diisi. Gambar harus JPG, PNG, atau WebP maksimal 3 MB.';
    } else {
        $lama = null;
        if ($id) {
            $s = $koneksi->prepare("SELECT gambar FROM $t WHERE id=?"); $s->bind_param('i', $id); $s->execute();
            $lama = ($s->get_result()->fetch_assoc())['gambar'] ?? null;
        }
        $gambar = $img ?? $lama;
        if ($img && $lama) hapusFile($lama);

        if ($t === 'berita') {
            $kat = in_array($_POST['kategori'] ?? '', ['Berita','Pengumuman','Agenda']) ? $_POST['kategori'] : 'Berita';
            $isi = trim($_POST['isi'] ?? '');
            $tgl = ($_POST['tanggal'] ?? '') ?: date('Y-m-d');
            if ($id) { $s = $koneksi->prepare('UPDATE berita SET judul=?,kategori=?,isi=?,tanggal=?,gambar=? WHERE id=?'); $s->bind_param('sssssi', $judul,$kat,$isi,$tgl,$gambar,$id); }
            else     { $s = $koneksi->prepare('INSERT INTO berita (judul,kategori,isi,tanggal,gambar) VALUES (?,?,?,?,?)'); $s->bind_param('sssss', $judul,$kat,$isi,$tgl,$gambar); }
        } else {
            $kat = in_array($_POST['kategori'] ?? '', ['Alam','UMKM','Budaya']) ? $_POST['kategori'] : 'Alam';
            if ($id) { $s = $koneksi->prepare('UPDATE galeri SET judul=?,kategori=?,gambar=? WHERE id=?'); $s->bind_param('sssi', $judul,$kat,$gambar,$id); }
            else     { $s = $koneksi->prepare('INSERT INTO galeri (judul,kategori,gambar) VALUES (?,?,?)'); $s->bind_param('sss', $judul,$kat,$gambar); }
        }
        $s->execute();
        header("Location: dashboard.php?tab=$t&ok=1"); exit;
    }
}

$ring = null;
if ($tab === 'ringkasan') {
    $q = fn($sql) => (int)$koneksi->query($sql)->fetch_assoc()['n'];
    $ring = ['berita' => $q('SELECT COUNT(*) n FROM berita'), 'galeri' => $q('SELECT COUNT(*) n FROM galeri'),
             'layanan' => $q('SELECT COUNT(*) n FROM layanan WHERE aktif=1'), 'perangkat' => $q('SELECT COUNT(*) n FROM perangkat')];
    $ringBerita = $koneksi->query('SELECT judul,kategori,tanggal FROM berita ORDER BY tanggal DESC, id DESC LIMIT 5')->fetch_all(MYSQLI_ASSOC);
}
$edit = null;
if (in_array($tab, ['berita','galeri'], true) && isset($_GET['edit'])) {
    $s = $koneksi->prepare("SELECT * FROM $tab WHERE id=?"); $e = (int)$_GET['edit']; $s->bind_param('i', $e); $s->execute();
    $edit = $s->get_result()->fetch_assoc();
}
$rows = !in_array($tab, ['berita','galeri'], true) ? [] : $koneksi->query("SELECT * FROM $tab ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
$kats = $tab === 'berita' ? ['Berita','Pengumuman','Agenda'] : ['Alam','UMKM','Budaya'];
?>
<?php adminAtas($judulHal[$tab], $tab === 'pengaturan' ? 'gambar' : $tab, false); ?>

  <?php if (($_GET['ok'] ?? '') === '1'): ?><div class="ok">Data tersimpan.</div><?php endif; ?>
  <?php if (($_GET['ok'] ?? '') === '2'): ?><div class="ok">Data dihapus.</div><?php endif; ?>
  <?php if ($error): ?><div class="alert"><?= h($error) ?></div><?php endif; ?>

  <?php if ($tab === 'ringkasan'): ?>
  <div class="card adm-sambut"><h2>Selamat datang, <?= h($_SESSION['admin_nama']) ?>.</h2><p>Kelola konten, layanan, dan struktur Lembang Rea Tulaklangi dari satu tempat.</p></div>
  <div class="adm-stat">
    <a href="dashboard.php?tab=berita"><b><?= $ring['berita'] ?></b><span>Berita dipublikasikan</span></a>
    <a href="dashboard.php?tab=galeri"><b><?= $ring['galeri'] ?></b><span>Foto di galeri</span></a>
    <a href="admin_layanan_kelola.php"><b><?= $ring['layanan'] ?></b><span>Layanan ditampilkan</span></a>
    <a href="admin_struktur.php"><b><?= $ring['perangkat'] ?></b><span>Posisi di struktur organisasi</span></a>
  </div>
  <div class="adm-dua">
    <div class="card"><h3>Layanan yang tampil di beranda</h3>
      <?php if (!$layanan): ?><p>Belum ada layanan.</p><?php endif; ?>
      <ul class="adm-list"><?php foreach ($layanan as $l): ?><li><span><strong><?= h($l[0]) ?></strong></span><small><?= h($l[3]) ?></small></li><?php endforeach; ?></ul>
      <p style="margin:14px 0 0"><a class="btn btn-outline btn-sm" href="admin_layanan_kelola.php">Kelola layanan</a></p></div>
    <div class="card"><h3>Berita terbaru</h3>
      <?php if (!$ringBerita): ?><p>Belum ada berita.</p><?php endif; ?>
      <ul class="adm-list"><?php foreach ($ringBerita as $r): ?><li><span><strong><?= h($r['judul']) ?></strong> <small><?= h($r['kategori']) ?></small></span><small><?= date('d/m/Y', strtotime($r['tanggal'])) ?></small></li><?php endforeach; ?></ul>
      <p style="margin:14px 0 0"><a class="btn btn-outline btn-sm" href="dashboard.php?tab=berita">Kelola berita</a></p></div>
  </div>
  <div class="card" style="margin-top:22px"><h3>Pintasan</h3>
    <div class="row">
      <a class="btn btn-green btn-sm" href="dashboard.php?tab=berita">Tambah berita</a>
      <a class="btn btn-outline btn-sm" href="admin_konten.php?grup=Beranda">Ubah sambutan</a>
      <a class="btn btn-outline btn-sm" href="admin_konten.php?grup=Statistik">Ubah statistik</a>
      <a class="btn btn-outline btn-sm" href="dashboard.php?tab=pengaturan">Ganti foto &amp; logo</a>
      <a class="btn btn-outline btn-sm" href="admin_struktur.php">Struktur organisasi</a>
    </div>
  </div>
  <?php elseif ($tab === 'pengaturan'): ?>
  <?php if (isset($_GET['err'])): ?><div class="alert">Pilih gambar JPG, PNG, atau WebP maksimal 3 MB.</div><?php endif; ?>
  <div class="grid">
  <?php foreach (['hero'=>['Gambar Hero Beranda','Foto besar di beranda dan banner halaman dalam. Disarankan landscape, lebar minimal 1600 px.'],'kades'=>['Foto Kepala Lembang','Tampil di beranda dan halaman Profil. Disarankan potret.'],'logo'=>['Logo Desa','Tampil di header semua halaman.']] as $k => $info): $img = gambarSet($k); ?>
    <form method="post" enctype="multipart/form-data" class="card adm-img">
      <h3><?= $info[0] ?></h3><p class="adm-desk"><?= $info[1] ?></p>
      <div class="adm-prev" style="height:190px;overflow:hidden"><?php if ($img): ?><img src="<?= h($img) ?>" alt="Pratinjau <?= h($info[0]) ?>" style="width:100%;height:100%;object-fit:contain;display:block"><?php else: ?><span>Memakai tampilan bawaan</span><?php endif; ?></div>
      <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
      <input type="hidden" name="tab" value="pengaturan">
      <input type="hidden" name="kunci" value="<?= $k ?>">
      <label for="g-<?= $k ?>">Pilih gambar baru</label>
      <input id="g-<?= $k ?>" type="file" name="gambar" accept="image/jpeg,image/png,image/webp">
      <div class="row" style="margin-top:16px">
        <button class="btn btn-green btn-sm" type="submit" name="aksi" value="simpan">Ganti gambar</button>
        <button class="btn btn-sm btn-del" type="submit" name="aksi" value="reset" formnovalidate onclick="return confirm('Kembalikan ke gambar bawaan?')">Kembalikan bawaan</button>
      </div>
    </form>
  <?php endforeach; ?>
  </div>
  <?php else: ?>
  <form method="post" enctype="multipart/form-data" class="card">
    <h3><?= $edit ? 'Edit' : 'Tambah' ?> <?= $tab ?></h3>
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
    <input type="hidden" name="tab" value="<?= $tab ?>">
    <input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>">
    <div class="form-grid">
      <div class="full"><label for="judul">Judul</label><input id="judul" type="text" name="judul" required value="<?= h($edit['judul'] ?? '') ?>"></div>
      <div><label for="kategori">Kategori</label>
        <select id="kategori" name="kategori"><?php foreach ($kats as $k): ?><option <?= ($edit['kategori'] ?? '')===$k?'selected':'' ?>><?= $k ?></option><?php endforeach; ?></select></div>
      <?php if ($tab === 'berita'): ?>
      <div><label for="tanggal">Tanggal</label><input id="tanggal" type="date" name="tanggal" value="<?= h($edit['tanggal'] ?? date('Y-m-d')) ?>"></div>
      <div class="full"><label for="isi">Isi</label><textarea id="isi" name="isi" rows="5" required><?= h($edit['isi'] ?? '') ?></textarea></div>
      <?php endif; ?>
      <div class="full"><label for="gambar">Gambar (JPG/PNG/WebP, maks 3 MB)<?= $edit ? ' - kosongkan jika tidak diganti' : '' ?></label><input id="gambar" type="file" name="gambar" accept="image/jpeg,image/png,image/webp"></div>
    </div>
    <div class="row" style="margin-top:20px">
      <button class="btn btn-green" type="submit">Simpan</button>
      <?php if ($edit): ?><a class="btn btn-outline" href="?tab=<?= $tab ?>">Batal</a><?php endif; ?>
    </div>
  </form>

  <div class="table-wrap card">
    <table>
      <tr><th>Gambar</th><th>Judul</th><th>Kategori</th><?= $tab==='berita' ? '<th>Tanggal</th>' : '' ?><th>Aksi</th></tr>
      <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= $r['gambar'] ? '<img src="uploads/'.h($r['gambar']).'" alt="">' : '-' ?></td>
        <td><?= h($r['judul']) ?></td>
        <td><?= h($r['kategori']) ?></td>
        <?php if ($tab==='berita'): ?><td><?= h($r['tanggal']) ?></td><?php endif; ?>
        <td>
          <a class="btn btn-outline btn-sm" href="?tab=<?= $tab ?>&edit=<?= $r['id'] ?>">Edit</a>
          <form method="post" onsubmit="return confirm('Hapus data ini?')">
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
            <input type="hidden" name="tab" value="<?= $tab ?>">
            <input type="hidden" name="id" value="<?= $r['id'] ?>">
            <input type="hidden" name="aksi" value="hapus">
            <button class="btn btn-sm btn-del" type="submit">Hapus</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="5">Belum ada data. Isi formulir di atas untuk menambah.</td></tr><?php endif; ?>
    </table>
  </div>
  <?php endif; ?>
<?php adminBawah(); ?>
