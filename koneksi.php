<?php
// Konfigurasi koneksi database - sesuaikan dengan server Anda
$db_host = 'sql208.infinityfree.com';
$db_user = 'if0_43245143';
$db_pass = 'KQvexTmAIU';
$db_name = 'if0_43245143_desa';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $koneksi = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $koneksi->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('Koneksi database gagal. Periksa pengaturan di koneksi.php.');
}
