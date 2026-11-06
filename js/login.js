/* Halaman login: versi statis tidak memiliki server, jadi masuk admin tidak tersedia */
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('form-login');
    var pesan = document.getElementById('pesan-login');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        pesan.textContent = 'Login admin hanya tersedia pada versi PHP yang terhubung ke database. Versi statis ini hanya untuk ditampilkan.';
        pesan.hidden = false;
    });
});
