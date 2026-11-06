/* Filter kategori pada halaman Berita dan Galeri (tanpa memuat ulang halaman) */
document.addEventListener('DOMContentLoaded', function () {
    var tombol = document.querySelectorAll('.tp-chip[data-kategori]');
    var kosong = document.querySelector('.tp-kosong');
    if (!tombol.length) return;

    var daftar = Array.prototype.map.call(tombol, function (t) { return t.dataset.kategori; });

    function terapkan(kat) {
        var tampil = 0;
        var item = document.querySelectorAll('[data-kategori]:not(.tp-chip)');
        tombol.forEach(function (t) { t.classList.toggle('on', t.dataset.kategori === kat); });
        item.forEach(function (el) {
            var cocok = kat === '' || el.dataset.kategori === kat;
            el.style.display = cocok ? '' : 'none';
            if (cocok) tampil++;
        });
        if (kosong) kosong.hidden = tampil > 0;
    }

    tombol.forEach(function (t) {
        t.addEventListener('click', function (e) {
            e.preventDefault();
            var kat = t.dataset.kategori;
            history.replaceState(null, '', location.pathname + (kat ? '?kategori=' + encodeURIComponent(kat) : '') + '#daftar');
            terapkan(kat);
        });
    });

    var awal = new URLSearchParams(location.search).get('kategori') || '';
    terapkan(daftar.indexOf(awal) > -1 ? awal : '');
});
