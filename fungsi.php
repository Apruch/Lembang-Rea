<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function ikon($p){ return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="'.$p.'"/></svg>'; }
function tglIndo($t){
    $bln = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $x = strtotime($t);
    return date('j', $x).' '.$bln[(int)date('n', $x)].' '.date('Y', $x);
}
function kartuBerita($b){
    $bg = $b['gambar'] ? ' style="background-image:url(\'uploads/'.h($b['gambar']).'\')"' : '';
    echo '<article class="card post"><div class="thumb"'.$bg.'></div><div class="post-body"><div class="meta"><span class="tag">'.h($b['kategori']).'</span><time>'.tglIndo($b['tanggal']).'</time></div><h3>'.h($b['judul']).'</h3><p>'.h(mb_strimwidth($b['isi'], 0, 130, '...')).'</p></div></article>';
}

$koneksi->query("CREATE TABLE IF NOT EXISTS pengaturan (kunci VARCHAR(50) PRIMARY KEY, nilai TEXT) ENGINE=InnoDB");
$kolNilai = $koneksi->query("SHOW COLUMNS FROM pengaturan LIKE 'nilai'")->fetch_assoc();
if ($kolNilai && stripos($kolNilai['Type'], 'varchar') === 0) { $koneksi->query("ALTER TABLE pengaturan MODIFY nilai TEXT"); }
$PENGATURAN = [];
foreach ($koneksi->query("SELECT kunci, nilai FROM pengaturan") as $r) { $PENGATURAN[$r['kunci']] = $r['nilai']; }
function gambarSet($k){
    global $PENGATURAN;
    $bawaan = ['hero'=>'assets/hero.jpg','kades'=>'assets/kades.jpg','logo'=>''];
    if (!empty($PENGATURAN[$k]) && is_file('uploads/'.$PENGATURAN[$k])) { return 'uploads/'.$PENGATURAN[$k]; }
    $d = $bawaan[$k] ?? '';
    return ($d !== '' && is_file($d)) ? $d : ''; // gambar bawaan hanya dipakai jika filenya memang ada
}

function csrf(){ if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); } return $_SESSION['csrf']; }
function csrfOk(){ return hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? ''); }

$suratSeed = [
 'pengantar' => ['Surat Pengantar', 'Pengantar resmi dari kantor lembang untuk keperluan di instansi lain, misalnya sekolah, bank, atau kantor kecamatan.', '1 hari kerja', ['Fotokopi KTP dan Kartu Keluarga', 'Surat pengantar dari kepala kampung atau dusun', 'Dokumen pendukung sesuai tujuan surat']],
 'kependudukan' => ['Dokumen Kependudukan', 'Pengantar pengurusan KTP, Kartu Keluarga, dan akta ke dinas kependudukan. Pencetakan dokumen dilakukan oleh dinas terkait.', '1 hari kerja untuk pengantar', ['KTP atau KK lama (jika ada)', 'Akta kelahiran atau surat nikah sesuai jenis pengurusan', 'Surat pengantar dari kepala kampung atau dusun']],
 'usaha' => ['Surat Keterangan Usaha', 'Surat bagi pelaku UMKM untuk keperluan perizinan, pembiayaan, atau administrasi usaha.', '1 sampai 2 hari kerja', ['Fotokopi KTP dan Kartu Keluarga', 'Foto lokasi atau kegiatan usaha', 'Surat pengantar dari kepala kampung atau dusun']],
 'domisili' => ['Surat Domisili', 'Keterangan tempat tinggal warga untuk berbagai keperluan administrasi.', '1 hari kerja', ['Fotokopi KTP dan Kartu Keluarga', 'Surat pengantar dari kepala kampung atau dusun', 'Pas foto 3x4 (bila diminta)']],
];


// Struktur organisasi terbaru (BPL ditampilkan di samping Kepala Lembang)
$orgSeed = [
  'nama' => "A. Laso' Bitungan, S.H.", 'jab' => 'Kepala Lembang', 'anak' => [
    ['nama' => 'Rahmawati H., S.E.', 'jab' => 'Sekretaris Lembang', 'anak' => [
      ['nama' => 'Nelwan Palimbong', 'jab' => 'Kaur Pelaporan dan Perencanaan'],
      ['nama' => 'Patmawati Karunen', 'jab' => 'Kaur Umum', 'anak' => [
        ['nama' => 'Reni Borolino, S.Pd', 'jab' => 'Staf Kaur Umum'],
      ]],
      ['nama' => 'Rika Baliallo, S.Th', 'jab' => 'Kaur Keuangan'],
    ]],
    ['nama' => 'Tikayan Patodingan', 'jab' => 'Kasi Pemerintahan', 'anak' => [
      ['nama' => 'S.P. Ampulembang', 'jab' => 'Kepala Kampung Rea Kadodo'],
    ]],
    ['nama' => 'Nurdin Ranggoa', 'jab' => 'Kasi Pembangunan', 'anak' => [
      ['nama' => 'Rusman Patintingan', 'jab' => "Kepala Kampung Pa'buran"],
    ]],
    ['nama' => "Marsuki Aco' Ruruk", 'jab' => 'Kasi Kemasyarakatan', 'anak' => [
      ['nama' => 'Serli Sirenden', 'jab' => 'Kepala Kampung Tulaklangi'],
      ['nama' => 'Yohanis Paluta', 'jab' => "Kepala Kampung Malo'"],
    ]],
  ],
];

// ================= IKON, TABEL LAYANAN & PERANGKAT (+ data awal) =================
$IKON = [
  'dokumen' => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6 M16 13H8 M16 17H8',
  'orang'   => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M22 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75',
  'usaha'   => 'M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16 M4 6h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z',
  'rumah'   => 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z M9 22V12h6v10',
  'pesan'   => 'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z',
  'hp'      => 'M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z M12 18h.01',
  'pin'     => 'M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z M12 7a3 3 0 1 0 0 6 3 3 0 0 0 0-6z',
  'kartu'   => 'M3 5h18a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z M7 10h4 M7 14h2 M15 10h2',
  'centang' => 'M20 6L9 17l-5-5',
];
$koneksi->query("CREATE TABLE IF NOT EXISTS layanan (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, slug VARCHAR(40) NOT NULL UNIQUE, judul VARCHAR(100) NOT NULL,
  deskripsi TEXT NOT NULL, ikon VARCHAR(20) NOT NULL DEFAULT 'dokumen',
  persyaratan TEXT, urutan INT NOT NULL DEFAULT 0, aktif TINYINT(1) NOT NULL DEFAULT 1) ENGINE=InnoDB");
$koneksi->query("CREATE TABLE IF NOT EXISTS perangkat (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(100) NOT NULL, jabatan VARCHAR(100) NOT NULL,
  parent_id INT UNSIGNED DEFAULT NULL, urutan INT NOT NULL DEFAULT 0, foto VARCHAR(100) DEFAULT NULL) ENGINE=InnoDB");

if ((int)$koneksi->query("SELECT COUNT(*) n FROM layanan")->fetch_assoc()['n'] === 0) {
    $ikSeed = ['pengantar'=>'dokumen','kependudukan'=>'orang','usaha'=>'usaha','domisili'=>'rumah'];
    $no = 0;
    $s = $koneksi->prepare('INSERT INTO layanan (slug,judul,deskripsi,ikon,persyaratan,urutan) VALUES (?,?,?,?,?,?)');
    foreach ($suratSeed as $slug => $d) {
        $no++; $syarat = implode("\n", $d[3]); $ik = $ikSeed[$slug] ?? 'dokumen';
        $s->bind_param('sssssi', $slug, $d[0], $d[1], $ik, $syarat, $no); $s->execute();
    }
}
function seedPerangkat($n, $parent, &$urut) {
    global $koneksi;
    $urut++;
    $s = $koneksi->prepare('INSERT INTO perangkat (nama,jabatan,parent_id,urutan) VALUES (?,?,?,?)');
    $s->bind_param('ssii', $n['nama'], $n['jab'], $parent, $urut); $s->execute();
    $id = $koneksi->insert_id;
    foreach ($n['anak'] ?? [] as $a) { seedPerangkat($a, $id, $urut); }
}
if ((int)$koneksi->query("SELECT COUNT(*) n FROM perangkat")->fetch_assoc()['n'] === 0) { $u = 0; seedPerangkat($orgSeed, null, $u); }

// Migrasi satu kali (versi 2): samakan data struktur lama dengan struktur terbaru.
// Posisi dicocokkan lewat jabatan sehingga foto yang sudah diunggah tetap aman.
function sinkronPerangkat($n, $parent, &$urut) {
    global $koneksi;
    static $alias = ['Kepala Kampung Rea Kadodo' => 'Kepala Kampung Kea Padang', "Kepala Kampung Pa'buran" => "Kepala Kampung Pa'buaran", 'Kepala Kampung Tulaklangi' => "Kepala Kampung Tulaklangi'"];
    $urut++;
    $cari = [$n['jab']]; if (isset($alias[$n['jab']])) { $cari[] = $alias[$n['jab']]; }
    $id = 0;
    foreach ($cari as $j) {
        $c = $koneksi->prepare('SELECT id FROM perangkat WHERE jabatan=? LIMIT 1'); $c->bind_param('s', $j); $c->execute();
        if ($r = $c->get_result()->fetch_assoc()) { $id = (int)$r['id']; break; }
    }
    if ($id) {
        $s = $koneksi->prepare('UPDATE perangkat SET nama=?,jabatan=?,parent_id=?,urutan=? WHERE id=?');
        $s->bind_param('ssiii', $n['nama'], $n['jab'], $parent, $urut, $id); $s->execute();
    } else {
        $s = $koneksi->prepare('INSERT INTO perangkat (nama,jabatan,parent_id,urutan) VALUES (?,?,?,?)');
        $s->bind_param('ssii', $n['nama'], $n['jab'], $parent, $urut); $s->execute();
        $id = $koneksi->insert_id;
    }
    foreach ($n['anak'] ?? [] as $a) { sinkronPerangkat($a, $id, $urut); }
}
if (($PENGATURAN['struktur_versi'] ?? '') !== '2') {
    $u = 0; sinkronPerangkat($orgSeed, null, $u);
    $koneksi->query("INSERT INTO pengaturan (kunci, nilai) VALUES ('struktur_versi', '2') ON DUPLICATE KEY UPDATE nilai='2'");
    $PENGATURAN['struktur_versi'] = '2';
}

// Daftar layanan yang ditampilkan di beranda: [judul, deskripsi, path ikon, daftar persyaratan]
$layanan = [];
foreach ($koneksi->query("SELECT * FROM layanan WHERE aktif=1 ORDER BY urutan, id") as $r) {
    $syarat = array_values(array_filter(array_map('trim', preg_split('/\R/', (string)$r['persyaratan']))));
    $layanan[] = [$r['judul'], $r['deskripsi'], $IKON[$r['ikon']] ?? $IKON['dokumen'], $syarat];
}
function kartuLayananInfo($l) {
    echo '<article class="card svc"><div class="ico">'.ikon($l[2]).'</div><h3>'.h($l[0]).'</h3><p>'.h($l[1]).'</p>';
    if ($l[3]) {
        echo '<details><summary>Lihat persyaratan</summary><ul>';
        foreach ($l[3] as $s) { echo '<li>'.h($s).'</li>'; }
        echo '</ul></details>';
    }
    echo '</article>';
}
// Struktur organisasi dari database: $organisasi = daftar simpul teratas, tiap simpul punya 'anak'
$perangkat = []; $anakDari = [];
foreach ($koneksi->query("SELECT * FROM perangkat ORDER BY urutan, id") as $r) {
    $perangkat[(int)$r['id']] = $r;
    $anakDari[(int)($r['parent_id'] ?? 0)][] = (int)$r['id'];
}
function pohon($id) {
    global $perangkat, $anakDari;
    $r = $perangkat[$id];
    $n = ['id' => $id, 'nama' => $r['nama'], 'jab' => $r['jabatan'], 'foto' => $r['foto'], 'anak' => []];
    foreach ($anakDari[$id] ?? [] as $a) { $n['anak'][] = pohon($a); }
    return $n;
}
$organisasi = [];
foreach ($anakDari[0] ?? [] as $i => $id) { $n = pohon($id); if ($i === 0) { $n['bpl'] = true; } $organisasi[] = $n; }

// ================= TEKS YANG BISA DIEDIT ADMIN =================
$KONTEN = [
 'Beranda' => [
  'hero_judul' => ['Judul utama (hero)', 'text', 'Lembang Rea Tulaklangi'],
  'hero_sub' => ['Subjudul (hero)', 'area', 'Portal Informasi, transparansi, dan pelayanan masyarakat yang menaungi 4 Kampung dengan potensi dan keunikannya masing-masing.'],
  'sambutan_nama' => ['Nama kepala di sambutan (kosongkan = ikuti Struktur Organisasi)', 'text', ''],
  'sambutan_jabatan' => ['Jabatan / keterangan kepala', 'text', 'Kepala Lembang Rea Tulaklangi'],
  'sambutan_kutipan' => ['Kutipan sambutan', 'area', 'Desa yang maju dimulai dari keterbukaan dan kebersamaan seluruh warganya.'],
  'sambutan_isi' => ['Isi sambutan', 'area', 'Selamat datang di website resmi Lembang Rea Tulaklangi. Website ini hadir sebagai sarana informasi, pelayanan, dan transparansi pemerintahan lembang. Semoga media ini memberikan manfaat bagi seluruh warga di tujuh dusun.'],
 ],
 'Statistik' => [
  'stat_dusun' => ['Jumlah dusun', 'text', '7'],
  'stat_penduduk' => ['Jumlah penduduk (jiwa)', 'text', '825'],
  'stat_kk' => ['Jumlah kepala keluarga', 'text', '498+'],
  'stat_berita' => ['Jumlah berita (kosongkan = otomatis dari berita yang ada)', 'text', ''],
  'penduduk_per' => ['Data penduduk sampai bulan', 'text', 'Oktober 2026'],
  'penduduk_kampung' => ['Penduduk per kampung (satu baris satu kampung, format: Nama=jumlah)', 'area', "Kadodo=285\nPa'Buaran=210\nTulaklangi'=180\nMalo'=152"],
 ],
 'Profil' => [
  'pemimpin_teks' => ['Keterangan di bagian Pemimpin Lembang', 'area', "Bapak A. Laso' Butungan, S.H. dilantik pada 3 Januari 2020 dengan SK Bupati Tana Toraja Nomor 01/I/2020 sebagai Kepala Lembang periode 2020 s/d 2026."],
  'sejarah' => ['Sejarah lembang (pisahkan paragraf dengan baris kosong; baris berawalan "- " menjadi daftar bernomor)', 'besar',
    "Pada zaman dahulu terdapat sebuah tempat yang ditumbuhi rumput ilalang yang sangat banyak sehingga sulit di basmi. Rumput ilalang dalam bahasa Toraja disebut Rea. Adapun kata Tulaklangi', berasal dari defografi tempat tersebut yang berada di sebuah ketinggian/gunung yang menjulang tinggi seakan-akan menopang (Tulak) langit (Langi'). Atas dasar itulah, tempat ini di sebut Rea Tulaklangi', yaitu menopang langit yang ditumbuhi ilalang. Ada penduduk berasal dari sebuah daerah di Pangala' (Rea) yang membawa tanah segenggam dari daerah asalnya dan menabur tanah tersebut di daerah ini. Daerah ini tidak ada air sehingga hanya menunggu air dari langit (hujan).\n\n".
    "Pada tahun 1997, sebagai hasil pemekaran dari Kelurahan Ulusalu Kecamatan Saluputti, kampung ini kemudian berdiri sendiri dan berbentuk Lembang (Desa) yang di beri nama Lembang Rea Tulaklangi'. Pada tahun ini pula, Lembang Rea Tulaklangi' di jabat oleh pejabat sementara yang bernama (S.P Pariakan) yang ditunjuk langsung oleh Bupati Tana Toraja. Setelah itu pada tahun 1998 Lembang Rea Tulaklangi' mengadakan pemilihan Kepala Lembang yang pertama dipimpin oleh (M.D Sareong) yang menjabat hingga tahun 2001. Tahun 2002 Lembang Rea Tulaklangi' kembali bergabung ke Lembang Ulusalu. Pada tahun 2007 Lembang Rea Tulaklangi' mengadakan pemilihan Kepala Lembang yang kedua kalinya yang di pimpin oleh (Tangibali Palluka') yang menjabat hingga tahun 2013. Pada tahun 2013 Lembang Rea Tulaklangi' mengadakan pemilihan Kepala Lembang yang ketiga kalinya yang di pimpin oleh (Agustinus Laso' Butungan, S.H.).\n\n".
    "A. Laso' Butungan, S.H. menjabat sebagai Kepala Lembang Rea Tulaklangi' dari tahun 2013 - 2019. Dan pada tanggal 29 November 2019 diadakan Pemilihan Kepala Lembang Rea Tulaklangi' yang diikuti oleh 2 calon Kepala Lembang antara lain:\n\n".
    "- A. Laso' Butungan, S.H.\n- Jafaruddin\n\n".
    "Dari hasil perhitungan suara calon Kepala Lembang Rea Tulaklangi', yang terpilih adalah Bapak A. Laso' Butungan, S.H. dan dilantik pada tanggal 3 Januari 2020 dengan SK Bupati Tana Toraja Nomor 01/I/2020 sebagai Kepala Lembang Rea Tulaklangi' periode 2020 s/d 2026."],
  'riwayat_kepala' => ['Daftar kepala lembang (satu baris satu orang, format: Nama | Masa jabatan | Keterangan)', 'area', "S.P Pariakan | 1997 - 1998 | Kepala Desa\nM.D Sareong | 1998 - 2001 | Kepala Desa\nTangibali Palluka | 2007 - 2013 | Kepala Lembang\nAgustinus Laso' Butungan, S.H. | 2014 s/d Sekarang | Kepala Lembang"],
  'visi' => ['Visi', 'area', 'Desa yang mandiri, transparan, dan sejahtera dengan tetap menjaga budaya lokal.'],
  'misi' => ['Misi', 'area', 'Memberikan pelayanan publik yang cepat dan terbuka, memeratakan pembangunan di 7 dusun, serta mengembangkan UMKM dan potensi wisata desa.'],
  'demografi_intro' => ['Pengantar letak dan wilayah', 'area', "Lembang Rea Tulaklangi' merupakan salah satu dari 8 Lembang di wilayah Kecamatan Saluputti yang terletak ±5 Km ke arah Barat dari Kecamatan Saluputti, dengan luas wilayah 250 hektar."],
  'luas' => ['Luas wilayah', 'text', '250 hektar'],
  'letak' => ['Letak wilayah', 'text', '±5 Km ke arah Barat dari Kecamatan Saluputti'],
  'batas_utara' => ['Batas sebelah utara', 'text', "Berbatasan dengan Lembang Batu Tiakka'"],
  'batas_selatan' => ['Batas sebelah selatan', 'text', "Berbatasan dengan Lembang Ratte Talonge dan Lembang Buri' Kecamatan Rembon"],
  'batas_timur' => ['Batas sebelah timur', 'text', 'Berbatasan dengan Lembang Tapparan Utara Kecamatan Rantetayo'],
  'batas_barat' => ['Batas sebelah barat', 'text', 'Berbatasan dengan Kelurahan Pattan Ulusalu'],
  'sekilas' => ['Kotak "Sekilas Lembang" (satu baris satu poin)', 'area', "Satu dari 8 lembang di Kecamatan Saluputti\nBerdiri sendiri pada tahun 1997\nHasil pemekaran dari Kelurahan Ulusalu\nKepala Lembang periode 2020 s/d 2026\nDilantik dengan SK Bupati Tana Toraja Nomor 01/I/2020"],
 ],
 'Kontak' => [
  'k_alamat' => ['Alamat kantor', 'text', 'Jl. Kantor Lembang No. 1, Lembang Rea Tulaklangi'],
  'k_telp' => ['Telepon / WhatsApp', 'text', '0812-0000-0000'],
  'k_email' => ['Email', 'text', 'lembang.reatulaklangi@example.com'],
  'k_jam' => ['Jam pelayanan (satu baris, format: Hari | Jam)', 'area', "Senin - Jumat | 08.00 - 15.00\nSabtu - Minggu | Tutup"],
  'k_peta' => ['Peta Google Maps (tempel kode embed atau tautan embed)', 'area', 'https://www.google.com/maps?q=Tana+Toraja&output=embed'],
  'k_fb' => ['Tautan Facebook (https://...)', 'text', ''],
  'k_ig' => ['Tautan Instagram (https://...)', 'text', ''],
  'k_yt' => ['Tautan YouTube (https://...)', 'text', ''],
 ],
];
function teks($k) {
    global $PENGATURAN, $KONTEN;
    $v = $PENGATURAN[$k] ?? null;
    if ($v !== null && $v !== '') { return $v; }
    foreach ($KONTEN as $g) { if (isset($g[$k])) { return $g[$k][2]; } }
    return '';
}
function barisTabel($s) {
    $r = [];
    foreach (preg_split('/\R/', (string)$s) as $l) { $l = trim($l); if ($l !== '') { $r[] = array_map('trim', explode('|', $l)); } }
    return $r;
}
function barisList($s) { return array_values(array_filter(array_map('trim', preg_split('/\R/', (string)$s)))); }
function blokTeks($s) {
    $out = '';
    foreach (preg_split('/\R\s*\R/', trim((string)$s)) as $b) {
        $b = trim($b); if ($b === '') { continue; }
        $baris = preg_split('/\R/', $b);
        $semua = count(array_filter($baris, fn($x) => strpos(trim($x), '- ') === 0)) === count($baris);
        if ($semua) { $out .= '<ol>'; foreach ($baris as $x) { $out .= '<li>'.h(ltrim(substr(trim($x), 2))).'</li>'; } $out .= '</ol>'; }
        else { $out .= '<p>'.nl2br(h($b)).'</p>'; }
    }
    return $out;
}
function pendudukKampung() {
    $r = [];
    foreach (barisList(teks('penduduk_kampung')) as $l) {
        if (strpos($l, '=') !== false) { [$n, $j] = array_map('trim', explode('=', $l, 2)); $r[$n] = (int)preg_replace('/\D/', '', $j); }
    }
    return $r;
}
function statBerita() {
    global $koneksi;
    $t = teks('stat_berita');
    return $t !== '' ? $t : (string)$koneksi->query('SELECT COUNT(*) n FROM berita')->fetch_assoc()['n'];
}
function kepalaNama() { global $organisasi; $t = teks('sambutan_nama'); return $t !== '' ? $t : ($organisasi[0]['nama'] ?? 'Kepala Lembang'); }
function kepalaJabatan() { global $organisasi; return $organisasi[0]['jab'] ?? 'Kepala Lembang'; }

// Data kontak (diambil dari teks yang bisa diedit)
$kontak = [
  'alamat' => teks('k_alamat'), 'telp' => teks('k_telp'), 'email' => teks('k_email'),
  'jam' => barisTabel(teks('k_jam')), 'peta' => teks('k_peta'),
  'fb' => teks('k_fb'), 'ig' => teks('k_ig'), 'yt' => teks('k_yt'),
];

// ================= UNGGAH FILE & TAMPILAN ADMIN =================
function unggah($f) { // null = tidak ada file, false = tidak valid, string = nama file
    if (empty($f['name'])) return null;
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 3*1024*1024) return false;
    $ext = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    if (!isset($ext[$mime])) return false;
    if (!is_dir('uploads')) mkdir('uploads', 0755, true);
    $n = bin2hex(random_bytes(8)).'.'.$ext[$mime];
    return move_uploaded_file($f['tmp_name'], "uploads/$n") ? $n : false;
}
function hapusFile($n){ if ($n && is_file("uploads/$n")) unlink("uploads/$n"); }
function adminCek() { if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; } }
function adminMenu($aktif) {
    global $koneksi, $IKON;
    $grup = [
      'Utama' => [['ringkasan', 'Ringkasan', 'dashboard.php', 'M3 3h7v7H3z M14 3h7v7h-7z M3 14h7v7H3z M14 14h7v7h-7z', 0]],
      'Konten Situs' => [
        ['berita', 'Berita', 'dashboard.php?tab=berita', 'M4 4h16v16H4z M8 8h8 M8 12h8 M8 16h5', 0],
        ['galeri', 'Galeri', 'dashboard.php?tab=galeri', 'M3 3h18v18H3z M8.5 10a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z M21 15l-5-5L5 21', 0],
        ['gambar', 'Gambar & Logo', 'dashboard.php?tab=pengaturan', 'M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z', 0],
        ['konten', 'Teks & Statistik', 'admin_konten.php', 'M4 20V10 M10 20V4 M16 20v-8 M22 20H2', 0]],
      'Layanan' => [['layanan', 'Layanan Publik', 'admin_layanan_kelola.php', $IKON['usaha'], 0]],
      'Pemerintahan' => [['struktur', 'Struktur Organisasi', 'admin_struktur.php', 'M12 3v6 M5 15v-3h14v3 M12 9v3 M3 15h4v6H3z M17 15h4v6h-4z M10 15h4v6h-4z', 0]],
      'Akun' => [['password', 'Ganti Password', 'ganti_password.php', 'M5 11h14v10H5z M8 11V7a4 4 0 0 1 8 0v4', 0]],
    ];
    foreach ($grup as $nama => $items) {
        echo '<p class="adm-grup">'.h($nama).'</p>';
        foreach ($items as [$k, $label, $url, $ik, $n]) {
            echo '<a class="adm-link'.($k === $aktif ? ' on' : '').'" href="'.h($url).'">'.ikon($ik).'<span>'.h($label).'</span>'.($n ? '<em class="adm-badge">'.$n.'</em>' : '').'</a>';
        }
    }
}
function adminAtas($judul, $aktif, $tampilOk = true) {
    adminCek();
    $logo = gambarSet('logo');
    $nama = (string)($_SESSION['admin_nama'] ?? 'Admin');
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>'.h($judul).' - Panel Admin Lembang Rea Tulaklangi</title>'
        .'<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet"><link rel="stylesheet" href="style.css?v='.@filemtime(__DIR__.'/style.css').'"><link rel="stylesheet" href="admin.css?v='.@filemtime(__DIR__.'/admin.css').'">';
    echo '</head><body class="adm-body"><div class="adm-app" id="admApp"><aside class="adm-side" id="admSide">';
    echo '<a class="adm-brand" href="dashboard.php">'.($logo ? '<img src="'.h($logo).'" alt="">' : '<svg viewBox="0 0 40 40" width="36" height="36" aria-hidden="true"><circle cx="20" cy="20" r="20" fill="#58c41c"/><path d="M12 28c0-9 6-15 16-16 0 10-6 16-16 16zm0 0l9-9" fill="none" stroke="#0a3d27" stroke-width="2.5" stroke-linecap="round"/></svg>').'<span>Lembang Rea Tulaklangi<small>Panel Admin</small></span></a>';
    adminMenu($aktif);
    echo '<div class="adm-bawah"><a class="adm-link" href="index.php" target="_blank" rel="noopener">'.ikon('M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6 M15 3h6v6 M10 14L21 3').'<span>Lihat situs</span></a>'
        .'<a class="adm-link" href="logout.php">'.ikon('M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4 M16 17l5-5-5-5 M21 12H9').'<span>Keluar</span></a></div>';
    echo '</aside><div class="adm-overlay" id="admOverlay"></div><div class="adm-main"><header class="adm-top"><button type="button" class="adm-burger" id="admBurger" aria-label="Buka menu">&#9776;</button><h1>'.h($judul).'</h1>'
        .'<div class="adm-user"><span class="adm-avatar">'.h(mb_strtoupper(mb_substr($nama, 0, 1))).'</span><span class="adm-nama">'.h($nama).'</span></div></header><main class="adm-content">';
    if ($tampilOk && isset($_GET['ok'])) { echo '<div class="ok">Perubahan tersimpan.</div>'; }
}
function adminBawah() {
    echo '</main></div></div><script>(function(){var a=document.getElementById("admApp");document.getElementById("admBurger").onclick=function(){a.classList.toggle("buka")};document.getElementById("admOverlay").onclick=function(){a.classList.remove("buka")};})();</script></body></html>';
}

function heroHalaman($eyebrow, $judul, $lead, $btn = null) {
    echo '<section class="tp-hero"><div class="tp-hero-teks"><p class="tp-eyebrow">'.h($eyebrow).'</p><h1>'.h($judul).'</h1><p class="tp-lead">'.h($lead).'</p>';
    if ($btn) { echo '<a class="tp-btn" href="'.h($btn[1]).'">'.h($btn[0]).'</a>'; }
    echo '</div><div class="tp-hero-img" style="background-image:url(\''.h(gambarSet('hero')).'\')"></div></section>';
}
function ctaHalaman($judul, $teks, $b1, $b2) {
    echo '<section class="tp-cta"><h2>'.h($judul).'</h2><p>'.h($teks).'</p><div class="gap"><a class="tp-btn putih" href="'.h($b1[1]).'">'.h($b1[0]).'</a><a class="tp-btn garis" href="'.h($b2[1]).'">'.h($b2[0]).'</a></div></section>';
}
