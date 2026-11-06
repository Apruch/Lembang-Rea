/* Halaman detail berita: tombol bagikan */
function salinLink() {
    var dummy = document.createElement('input'), text = window.location.href;
    document.body.appendChild(dummy); dummy.value = text; dummy.select(); document.execCommand('copy'); document.body.removeChild(dummy);
    alert('Tautan berita berhasil disalin!');
}

document.addEventListener('DOMContentLoaded', function () {
    var wa = document.querySelector('[data-bagikan="whatsapp"]');
    var judul = document.querySelector('.detail-judul');
    if (wa && judul) {
        wa.href = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(judul.textContent.trim()) +
                  '%20%7C%20' + encodeURIComponent(window.location.href);
    }
});
