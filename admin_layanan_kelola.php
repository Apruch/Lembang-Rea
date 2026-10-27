<?php
session_start();
require 'koneksi.php'; require 'fungsi.php';
adminCek();
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfOk()) { http_response_code(403); exit('Sesi tidak valid.'); }
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['aksi'] ?? '') === 'hapus') {
        $s = $koneksi->prepare('DELETE FROM layanan WHERE id=?'); $s->bind_param('i', $id); $s->execute();
        header('Location: admin_layanan_kelola.php?ok=1'); exit;
    }
    $judul = trim($_POST['judul'] ?? ''); $desk = trim($_POST['deskripsi'] ?? '');
    $ikon = isset($IKON[$_POST['ikon'] ?? '']) ? $_POST['ikon'] : 'dokumen';
    $syarat = trim(str_replace("\r\n", "\n", $_POST['persyaratan'] ?? ''));
    $urut = (int)($_POST['urutan'] ?? 0); $aktif = isset($_POST['aktif']) ? 1 : 0;
    if ($judul === '' || mb_strlen($judul) > 100 || $desk === '') { $err = 'Judul dan deskripsi wajib diisi.'; }
    else {
        if ($id) {
            $s = $koneksi->prepare('UPDATE layanan SET judul=?,deskripsi=?,ikon=?,persyaratan=?,urutan=?,aktif=? WHERE id=?');
            $s->bind_param('ssssiii', $judul, $desk, $ikon, $syarat, $urut, $aktif, $id);
        } else {
            $dasar = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($judul)), '-') ?: 'layanan';
            $dasar = substr($dasar, 0, 30); $slug = $dasar; $i = 2;
            while (true) {
                $c = $koneksi->prepare('SELECT 1 FROM layanan WHERE slug=?'); $c->bind_param('s', $slug); $c->execute();
                if ($c->get_result()->num_rows === 0) break;
                $slug = $dasar.'-'.$i++;
            }
            $s = $koneksi->prepare('INSERT INTO layanan (judul,deskripsi,ikon,persyaratan,urutan,aktif,slug) VALUES (?,?,?,?,?,?,?)');
            $s->bind_param('ssssiis', $judul, $desk, $ikon, $syarat, $urut, $aktif, $slug);
        }
        $s->execute();
        header('Location: admin_layanan_kelola.php?ok=1'); exit;
    }
}
$edit = null;
if (isset($_GET['edit'])) { $s = $koneksi->prepare('SELECT * FROM layanan WHERE id=?'); $e = (int)$_GET['edit']; $s->bind_param('i', $e); $s->execute(); $edit = $s->get_result()->fetch_assoc(); }
$rows = $koneksi->query('SELECT * FROM layanan ORDER BY urutan, id')->fetch_all(MYSQLI_ASSOC);
$v = fn($k, $d = '') => h($_POST[$k] ?? ($edit[$k] ?? $d));
adminAtas('Layanan Publik', 'layanan');
?>
<p>Tambah, ubah, atau sembunyikan layanan yang ditampilkan di bagian Layanan pada beranda. Isi nama, deskripsi, dan persyaratan. Layanan yang disembunyikan tidak tampil di situs.</p>
<?php if ($err): ?><div class="alert"><?= h($err) ?></div><?php endif; ?>
<form method="post" class="card">
  <h3><?= $edit ? 'Edit layanan' : 'Tambah layanan' ?></h3>
  <input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
  <div class="form-grid">
    <div class="full"><label for="judul">Nama layanan</label><input id="judul" type="text" name="judul" required maxlength="100" value="<?= $v('judul') ?>"></div>
    <div class="full"><label for="deskripsi">Deskripsi singkat</label><textarea id="deskripsi" name="deskripsi" rows="3" required><?= $v('deskripsi') ?></textarea></div>
    <div><label for="ikon">Ikon</label><select id="ikon" name="ikon"><?php foreach (array_keys($IKON) as $k): ?><option value="<?= $k ?>"<?= ($_POST['ikon'] ?? ($edit['ikon'] ?? 'dokumen')) === $k ? ' selected' : '' ?>><?= ucfirst($k) ?></option><?php endforeach; ?></select></div>
    <div><label for="urutan">Urutan tampil</label><input id="urutan" type="number" name="urutan" value="<?= $v('urutan', count($rows) + 1) ?>" style="width:100%;padding:.7rem;border:1.5px solid #d3dad4;border-radius:12px"></div>
    <div><label>&nbsp;</label><label style="display:flex;gap:8px;align-items:center;margin:10px 0 0"><input type="checkbox" name="aktif" value="1" <?= (!$edit && !$_POST) || ($_POST['aktif'] ?? $edit['aktif'] ?? 0) ? 'checked' : '' ?>> Tampilkan di situs</label></div>
    <div class="full"><label for="persyaratan">Persyaratan (satu baris satu syarat)</label><textarea id="persyaratan" name="persyaratan" rows="5"><?= $v('persyaratan') ?></textarea></div>
  </div>
  <div class="row" style="margin-top:18px"><button class="btn btn-green" type="submit">Simpan</button><?php if ($edit): ?><a class="btn btn-outline" href="admin_layanan_kelola.php">Batal</a><?php endif; ?></div>
</form>
<div class="table-wrap card"><table>
  <tr><th>Urutan</th><th>Layanan</th><th>Status</th><th>Aksi</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr><td><?= (int)$r['urutan'] ?></td><td><strong><?= h($r['judul']) ?></strong><br><small><?= h(mb_strimwidth($r['deskripsi'], 0, 70, '...')) ?></small></td><td><?= $r['aktif'] ? 'Tampil' : 'Disembunyikan' ?></td>
  <td><a class="btn btn-outline btn-sm" href="?edit=<?= $r['id'] ?>">Edit</a>
    <form method="post" onsubmit="return confirm('Hapus layanan ini?')"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="aksi" value="hapus"><button class="btn btn-sm btn-del" type="submit">Hapus</button></form></td></tr>
  <?php endforeach; ?>
</table></div>
<?php adminBawah(); ?>
