/**
 * OBM STUDIO — portfolio.js v2.0
 * Handles: Lucide icons init, login modal, filter buttons, gallery logic
 */

document.addEventListener('DOMContentLoaded', () => {

  // ── Lucide icons
  if (window.lucide) lucide.createIcons();

  // ── NAV LINK ACTIVE STATE (scroll spy)
  const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
  const sections = document.querySelectorAll('section[id]');
  const observerNav = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        navLinks.forEach(l => l.classList.remove('active'));
        const active = document.querySelector(`.nav-link[href="#${entry.target.id}"]`);
        if (active) active.classList.add('active');
      }
    });
  }, { threshold: 0.4 });
  sections.forEach(s => observerNav.observe(s));

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
    document.getElementById('event-passcode').value = 'DEMO2026';
    showToast('Demo Code Applied', 'Click "Unlock Photo Portal" to continue', 'gold', { duration: 3000, icon: '🔑' });
  });

  loginForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const code = document.getElementById('event-passcode').value.trim().toUpperCase();
    errorBox.classList.add('hidden');

    if (!code) {
      errorBox.textContent = 'Please enter your event passcode.';
      errorBox.classList.remove('hidden');
      return;
    }

    const submitBtn = loginForm.querySelector('button[type="submit"]');
    const originalHtml = submitBtn.innerHTML;
    submitBtn.innerHTML = '<svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 11-6.219-8.56"/></svg> Verifying...';
    submitBtn.disabled = true;

    await new Promise(r => setTimeout(r, 900)); // simulate

    if (code === 'DEMO2026') {
      showToast('✅ Access Granted!', 'Welcome to your OBM Studio photo gallery', 'success', { duration: 3500 });
      closeModal();
      setTimeout(() => {
        window.location.href = 'photo-selection.html?token=DEMO2026';
      }, 1200);
    } else {
      errorBox.textContent = 'Invalid passcode. Please contact your photographer for your unique event code.';
      errorBox.classList.remove('hidden');
      showToast('Access Denied', 'The event passcode is incorrect', 'error', { duration: 4000 });
      submitBtn.innerHTML = originalHtml;
      submitBtn.disabled = false;
    }
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
