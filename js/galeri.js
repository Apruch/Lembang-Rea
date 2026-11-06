/* Pop-up (lightbox) foto galeri */
document.addEventListener("DOMContentLoaded", function() {
    const popLinks = document.querySelectorAll('.link-galeri');
    const modal = document.getElementById('popup-galeri');
    const modalImg = document.getElementById('popup-img');
    const modalTeks = document.getElementById('popup-teks');

    popLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Hanya aktifkan pop-up jika file gambar benar-benar ada
            if(href !== '#') { 
                e.preventDefault(); // Mencegah browser membuka tab baru
                modal.style.display = "block";
                modalImg.src = href;
                modalTeks.innerHTML = this.getAttribute('data-judul');
            }
        });
    });
});
