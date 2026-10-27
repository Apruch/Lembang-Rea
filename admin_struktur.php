<?php
session_start();
require 'koneksi.php'; require 'fungsi.php';
adminCek();
$err = '';
function turunan($id, &$o) { global $anakDari; foreach ($anakDari[$id] ?? [] as $a) { $o[] = $a; turunan($a, $o); } }
function datar($n, $d, &$o) { $o[] = [$n, $d]; foreach ($n['anak'] as $a) { datar($a, $d + 1, $o); } }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfOk()) { http_response_code(403); exit('Sesi tidak valid.'); }
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['aksi'] ?? '') === 'hapus') {
        if (!empty($anakDari[$id])) { $err = 'Posisi ini masih punya bawahan. Pindahkan atau hapus bawahannya dulu.'; }
        else {
            hapusFile($perangkat[$id]['foto'] ?? null);
            $s = $koneksi->prepare('DELETE FROM perangkat WHERE id=?'); $s->bind_param('i', $id); $s->execute();
            header('Location: admin_struktur.php?ok=1'); exit;
        }
    } else {
        $nama = trim($_POST['nama'] ?? ''); $jab = trim($_POST['jabatan'] ?? '');
        $parent = (int)($_POST['parent_id'] ?? 0); $urut = (int)($_POST['urutan'] ?? 0);
        $dilarang = [$id]; if ($id) { turunan($id, $dilarang); }
        $foto = unggah($_FILES['foto'] ?? []);
        if ($nama === '' || $jab === '' || mb_strlen($nama) > 100 || mb_strlen($jab) > 100) { $err = 'Nama dan jabatan wajib diisi.'; }
        elseif ($foto === false) { $err = 'Foto harus JPG, PNG, atau WebP maksimal 3 MB.'; }
        elseif ($parent && (!isset($perangkat[$parent]) || in_array($parent, $dilarang, true))) { $err = 'Atasan tidak valid.'; }
        else {
            $lama = $id ? ($perangkat[$id]['foto'] ?? null) : null;
            $fotoBaru = $foto ?? $lama;
            if (isset($_POST['hapus_foto']) && !$foto) { hapusFile($lama); $fotoBaru = null; }
            elseif ($foto && $lama) { hapusFile($lama); }
            $p = $parent ?: null;
            if ($id) { $s = $koneksi->prepare('UPDATE perangkat SET nama=?,jabatan=?,parent_id=?,urutan=?,foto=? WHERE id=?'); $s->bind_param('ssiisi', $nama, $jab, $p, $urut, $fotoBaru, $id); }
            else { $s = $koneksi->prepare('INSERT INTO perangkat (nama,jabatan,parent_id,urutan,foto) VALUES (?,?,?,?,?)'); $s->bind_param('ssiis', $nama, $jab, $p, $urut, $fotoBaru); }
            $s->execute();
            header('Location: admin_struktur.php?ok=1'); exit;
        }
    }
}
$editId = (int)($_GET['edit'] ?? 0);
$edit = $perangkat[$editId] ?? null;
$dilarang = $edit ? [$editId] : []; if ($edit) { turunan($editId, $dilarang); }
$daftar = []; foreach ($organisasi as $r) { datar($r, 0, $daftar); }
adminAtas('Struktur Organisasi', 'struktur');
?>
<p>Tambah posisi, ubah nama atau jabatan saat ada pergantian, pindahkan atasan, dan unggah foto. Perubahan langsung tampil di halaman Struktur Organisasi. Satu posisi tanpa atasan menjadi puncak bagan.</p>
<?php if ($err): ?><div class="alert"><?= h($err) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data" class="card">
  <h3><?= $edit ? 'Edit posisi' : 'Tambah posisi' ?></h3>
  <input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= $editId ?>">
  <div class="form-grid">
    <div><label for="nama">Nama</label><input id="nama" type="text" name="nama" required maxlength="100" value="<?= h($_POST['nama'] ?? ($edit['nama'] ?? '')) ?>"></div>
    <div><label for="jabatan">Jabatan</label><input id="jabatan" type="text" name="jabatan" required maxlength="100" value="<?= h($_POST['jabatan'] ?? ($edit['jabatan'] ?? '')) ?>"></div>
    <div><label for="parent_id">Atasan</label>
      <select id="parent_id" name="parent_id"><option value="0">- Tanpa atasan (puncak) -</option>
        <?php foreach ($daftar as [$n, $d]): if (in_array($n['id'], $dilarang, true)) continue; ?>
          <option value="<?= $n['id'] ?>"<?= (int)($_POST['parent_id'] ?? ($edit['parent_id'] ?? 0)) === $n['id'] ? ' selected' : '' ?>><?= str_repeat('— ', $d).h($n['nama']).' ('.h($n['jab']).')' ?></option>
        <?php endforeach; ?></select></div>
    <div><label for="urutan">Urutan (kecil tampil lebih kiri)</label><input id="urutan" type="number" name="urutan" value="<?= (int)($_POST['urutan'] ?? ($edit['urutan'] ?? count($perangkat) + 1)) ?>" style="width:100%;padding:.7rem;border:1.5px solid #d3dad4;border-radius:12px"></div>
    <div class="full"><label for="foto">Foto (JPG/PNG/WebP, maks 3 MB)<?= $edit ? ' - kosongkan jika tidak diganti' : '' ?></label><input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp">
      <?php if ($edit && $edit['foto']): ?><p><img src="uploads/<?= h($edit['foto']) ?>" alt="" style="width:70px;height:70px;object-fit:cover;border-radius:50%"> <label style="display:inline"><input type="checkbox" name="hapus_foto" value="1"> Hapus foto ini</label></p><?php endif; ?></div>
  </div>
  <div class="row" style="margin-top:18px"><button class="btn btn-green" type="submit">Simpan</button><?php if ($edit): ?><a class="btn btn-outline" href="admin_struktur.php">Batal</a><?php endif; ?></div>
</form>
<div class="table-wrap card"><table>
  <tr><th>Foto</th><th>Posisi</th><th>Aksi</th></tr>
  <?php foreach ($daftar as [$n, $d]): ?>
  <tr><td><?= $n['foto'] ? '<img src="uploads/'.h($n['foto']).'" alt="" style="width:44px;height:44px;object-fit:cover;border-radius:50%">' : '-' ?></td>
  <td style="padding-left:<?= 10 + $d * 22 ?>px"><?= $d ? '&#8627; ' : '' ?><strong><?= h($n['nama']) ?></strong><br><small style="padding-left:<?= $d ? 16 : 0 ?>px"><?= h($n['jab']) ?></small></td>
  <td><a class="btn btn-outline btn-sm" href="?edit=<?= $n['id'] ?>">Edit</a>
    <form method="post" onsubmit="return confirm('Hapus posisi ini?')"><input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="id" value="<?= $n['id'] ?>"><input type="hidden" name="aksi" value="hapus"><button class="btn btn-sm btn-del" type="submit">Hapus</button></form></td></tr>
  <?php endforeach; ?>
</table></div>
<?php adminBawah(); ?>
