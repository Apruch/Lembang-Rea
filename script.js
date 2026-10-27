/* Navigasi & interaksi umum - Lembang Rea Tulaklangi */
document.addEventListener('DOMContentLoaded', () => {
  const burger = document.getElementById('burger');
  const menu = document.getElementById('menu');
  const dds = document.querySelectorAll('.has-dd');
  const desktop = window.matchMedia('(hover: hover) and (min-width: 961px)');

  const setDropdown = (li, buka) => {
    li.classList.toggle('open', buka);
    const btn = li.querySelector('.dd-btn');
    if (btn) btn.setAttribute('aria-expanded', String(buka));
  };
  const tutupDropdown = (kecuali) => dds.forEach(li => { if (li !== kecuali) setDropdown(li, false); });
  const setMenu = (buka) => {
    if (!menu || !burger) return;
    menu.classList.toggle('open', buka);
    burger.setAttribute('aria-expanded', String(buka));
    burger.setAttribute('aria-label', buka ? 'Tutup menu' : 'Buka menu');
    document.body.classList.toggle('menu-buka', buka && !desktop.matches);
    if (!buka) tutupDropdown(null);
  };

  /* Tombol burger (HP) */
  if (burger) burger.addEventListener('click', e => { e.stopPropagation(); setMenu(!menu.classList.contains('open')); });

  /* Dropdown: klik di HP, hover di desktop */
  dds.forEach(li => {
    const btn = li.querySelector('.dd-btn');
    if (!btn) return;
    let timer;
    btn.addEventListener('click', e => {
      e.stopPropagation();
      tutupDropdown(li);
      setDropdown(li, !li.classList.contains('open'));
    });
    li.addEventListener('mouseenter', () => {
      if (!desktop.matches) return;
      clearTimeout(timer); tutupDropdown(li); setDropdown(li, true);
    });
    li.addEventListener('mouseleave', () => {
      if (!desktop.matches) return;
      timer = setTimeout(() => setDropdown(li, false), 150);
    });
    li.addEventListener('focusout', e => { if (!li.contains(e.relatedTarget)) setDropdown(li, false); });
  });

  /* Tutup saat klik di luar, tekan ESC, atau pilih tautan */
  document.addEventListener('click', e => {
    if (e.target.closest('.nav')) return;
    tutupDropdown(null); setMenu(false);
  });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') { tutupDropdown(null); setMenu(false); } });
  if (menu) menu.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setMenu(false)));
  desktop.addEventListener('change', () => setMenu(false));

  /* Tombol "Baca Sejarah Lengkap" */
  document.querySelectorAll('[data-toggle]').forEach(b => {
    b.addEventListener('click', () => {
      const t = document.getElementById(b.dataset.toggle);
      if (!t) return;
      const buka = t.hasAttribute('hidden');
      t.toggleAttribute('hidden', !buka);
      b.setAttribute('aria-expanded', String(buka));
      b.textContent = buka ? 'Tutup Sejarah Lengkap' : 'Baca Sejarah Lengkap';
    });
  });
});
