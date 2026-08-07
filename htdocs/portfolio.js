/**
 * OBM STUDIO — portfolio.js v2.0
 * Handles: Lucide icons init, login modal, filter buttons, gallery logic
 */

document.addEventListener('DOMContentLoaded', () => {

  // ── Lucide icons
  if (window.lucide) lucide.createIcons();

  // ── NAV LINK ACTIVE STATE (scroll spy)
  const navLinks = document.querySelectorAll('.nav-link[href*="#"]');
  const sections = document.querySelectorAll('section[id]');
  const observerNav = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        navLinks.forEach(l => {
          const href = l.getAttribute('href');
          if (href && href.endsWith('#' + entry.target.id)) {
            l.classList.add('active');
          } else {
            l.classList.remove('active');
          }
        });
      }
    });
  }, { threshold: 0.4 });
  sections.forEach(s => observerNav.observe(s));

  // ── SMOOTH SCROLL FOR HASH LINKS ON HOME
  const isHomepage = window.location.pathname === '/' || window.location.pathname === '/index' || window.location.pathname.endsWith('/index.php');
  document.querySelectorAll('a[href*="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      const hashIndex = href.indexOf('#');
      if (hashIndex !== -1) {
        const hash = href.substring(hashIndex);
        const target = document.querySelector(hash);
        if (target && isHomepage) {
          e.preventDefault();
          const offset = 90;
          const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
          window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
          });
        }
      }
    });
  });

  // ── SMOOTH SCROLL ENTRANCE ON PAGE LOAD IF HASH EXISTS
  if (window.location.hash && isHomepage) {
    const hash = window.location.hash;
    const target = document.querySelector(hash);
    if (target) {
      window.scrollTo(0, 0);
      setTimeout(() => {
        const offset = 90;
        const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
        window.scrollTo({
          top: targetPosition,
          behavior: 'smooth'
        });
      }, 300);
    }
  }

  // ── LOGIN MODAL LOGIC
  const modal = document.getElementById('login-modal');
  const closeBtn = document.getElementById('close-login-btn');
  const openBtns = document.querySelectorAll('.open-login-btn');
  const loginForm = document.getElementById('client-login-form');
  const errorBox = document.getElementById('passcode-error');
  const demoBt = document.getElementById('demo-code-btn');

  function openModal() {
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
    setTimeout(() => document.getElementById('event-passcode')?.focus(), 300);
  }
  function closeModal() {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }

  openBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      // Prevent anchor navigation for <a> tags
      if (btn.tagName === 'A') e.preventDefault();
      openModal();
    });
  });
  closeBtn?.addEventListener('click', closeModal);
  modal?.addEventListener('click', e => { if (e.target === modal) closeModal(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

  demoBt?.addEventListener('click', () => {
    const emailInput = document.getElementById('client-email');
    if (emailInput) emailInput.value = 'vikram@example.com';
    document.getElementById('event-passcode').value = 'DEMO2026';
    showToast('Demo Credentials Applied', 'Click "Unlock Photo Portal" to continue', 'gold', { duration: 3000, icon: '🔑' });
  });

  // ── GALLERY CATEGORY FILTERS
  const filterBtns = document.querySelectorAll('.filter-btn');
  const galleryCards = document.querySelectorAll('.gallery-card');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const filter = btn.dataset.filter;
      filterBtns.forEach(b => {
        b.classList.remove('active', 'btn-primary');
        b.classList.add('bg-slate-800/60', 'text-slate-300');
      });
      btn.classList.remove('bg-slate-800/60', 'text-slate-300');
      btn.classList.add('active', 'btn-primary');

      galleryCards.forEach(card => {
        const cat = card.dataset.category;
        if (filter === 'all' || cat === filter) {
          card.style.display = '';
          requestAnimationFrame(() => card.classList.add('revealed'));
        } else {
          card.style.display = 'none';
        }
      });

      showToast('Filter Applied', `Showing: ${btn.textContent.trim()}`, 'purple', { duration: 2000, position: 'bottom-right' });
    });
  });

  // ── WELCOME TOAST
  setTimeout(() => {
    showToast('Welcome to OBM Studio 🎬', 'A decade of love, craft & cinematic storytelling.', 'sapphire', {
      duration: 5000, position: 'bottom-right'
    });
  }, 1500);

});
