<?php
session_start();
require 'koneksi.php';

// Membuka halaman login selalu meminta kredensial: sesi admin lama diakhiri dulu.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_SESSION['admin_id'])) {
    $_SESSION = [];
    session_regenerate_id(true);
}
if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $error = 'Sesi tidak valid. Muat ulang halaman lalu coba lagi.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $stmt = $koneksi->prepare('SELECT id, username, password, nama_lengkap FROM admin WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama_lengkap'];
            header('Location: dashboard.php'); exit;
        }
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk Admin - Lembang Rea Tulaklangi</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="auth">
  <main class="auth-box">
    <h1 style="font-size:1.9rem">Masuk Admin</h1>
    <p>Khusus pengelola website Lembang Rea Tulaklangi.</p>
    <?php if ($error): ?><div class="alert"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
      <label for="username">Username</label>
      <input id="username" name="username" type="text" required autofocus>
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>
      <button type="submit" class="btn btn-green">Masuk</button>
    </form>
    <a class="back" href="index.php">Kembali ke beranda</a>
  </main>
</body>
</html>
