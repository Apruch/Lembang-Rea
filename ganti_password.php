<?php
session_start();
require 'koneksi.php'; require 'fungsi.php';
adminCek();
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfOk()) { http_response_code(403); exit('Sesi tidak valid.'); }
    $lama = $_POST['lama'] ?? ''; $baru = $_POST['baru'] ?? ''; $ulang = $_POST['ulang'] ?? '';
    $s = $koneksi->prepare('SELECT password FROM admin WHERE id=?'); $s->bind_param('i', $_SESSION['admin_id']); $s->execute();
    $row = $s->get_result()->fetch_assoc();
    if (!$row || !password_verify($lama, $row['password'])) { $err = 'Password lama salah.'; }
    elseif (mb_strlen($baru) < 8) { $err = 'Password baru minimal 8 karakter.'; }
    elseif ($baru !== $ulang) { $err = 'Konfirmasi password baru tidak sama.'; }
    elseif ($baru === $lama) { $err = 'Password baru harus berbeda dari password lama.'; }
    else {
        $hash = password_hash($baru, PASSWORD_DEFAULT);
        $u = $koneksi->prepare('UPDATE admin SET password=? WHERE id=?'); $u->bind_param('si', $hash, $_SESSION['admin_id']); $u->execute();
        session_regenerate_id(true);
        header('Location: ganti_password.php?ok=1'); exit;
    }
}
adminAtas('Ganti Password', 'password');
?>
<?php if ($err): ?><div class="alert"><?= h($err) ?></div><?php endif; ?>
<form method="post" class="card" style="max-width:520px" autocomplete="off">
  <input type="hidden" name="csrf" value="<?= csrf() ?>">
  <label for="lama">Password lama</label><input id="lama" type="password" name="lama" required>
  <label for="baru">Password baru (minimal 8 karakter)</label><input id="baru" type="password" name="baru" required minlength="8">
  <label for="ulang">Ulangi password baru</label><input id="ulang" type="password" name="ulang" required minlength="8">
  <div class="row" style="margin-top:20px"><button class="btn btn-green" type="submit">Ubah Password</button></div>
</form>
<?php adminBawah(); ?>
