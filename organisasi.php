<?php
$judul = 'Struktur Organisasi'; $aktif = 'organisasi';
require 'header.php';

function inisial($nama) {
    preg_match_all('/\b\p{Lu}/u', $nama, $m);
    return mb_substr(implode('', array_slice($m[0], 0, 2)), 0, 2);
}
function simpul($n) {
    $f = !empty($n['foto']) ? 'uploads/'.$n['foto'] : '';
    if (!$f && !empty($n['bpl'])) { $f = gambarSet('kades'); }
    $ada = $f && is_file($f);
    echo '<li><div class="nd'.(!empty($n['bpl']) ? ' utama' : '').'">';
    echo '<div class="nd-foto"'.($ada ? ' style="background-image:url(\''.h($f).'\')"' : '').'>'.($ada ? '' : h(inisial($n['nama']))).'</div>';
    echo '<div class="nd-card"><b>'.h($n['nama']).'</b><span>'.h($n['jab']).'</span></div>';
    if (!empty($n['bpl'])) {
        echo '<div class="bpl"><div class="nd-foto bpl-foto">BPL</div><div class="nd-card"><b>BPL</b><span>Badan Perwakilan Lembang</span></div></div>';
    }
    echo '</div>';
    if (!empty($n['anak'])) {
        echo '<ul>';
        foreach ($n['anak'] as $a) { simpul($a); }
        echo '</ul>';
    }
    echo '</li>';
}
?>
<section class="og">
  <h1>Struktur Organisasi</h1>
  <p class="og-sub">Pemerintah Lembang Rea Tulaklangi</p>
  <div class="og-scroll">
    <div class="og-tree"><ul><?php foreach ($organisasi as $akar) { simpul($akar); } ?></ul></div>
  </div>
</section>
<?php require 'footer.php'; ?>
