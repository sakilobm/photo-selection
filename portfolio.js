/**
 * OBM STUDIO - PORTFOLIO & CLIENT GATEWAY INTERACTION SCRIPT
 * Version 1.0.0
 */

document.addEventListener('DOMContentLoaded', () => {
  // Initialize Lucide Icons
  if (window.lucide) {
    lucide.createIcons();
  }

  // Navbar Scroll effect
  const navbar = document.getElementById('main-nav');
  if (navbar) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 50) {
        navbar.classList.add('glass-nav');
      } else {
        navbar.classList.remove('glass-nav');
      }
    });
  }

  // Portfolio Filtering System
  const filterButtons = document.querySelectorAll('.filter-btn');
  const galleryItems = document.querySelectorAll('.gallery-card');

  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      // Remove active class from all buttons
      filterButtons.forEach(b => {
        b.classList.remove('active', 'bg-cyan-500', 'text-slate-950');
        b.classList.add('bg-slate-800/60', 'text-slate-300', 'hover:bg-slate-700/80');
      });

      // Add active state to clicked button
      btn.classList.add('active', 'bg-cyan-500', 'text-slate-950');
      btn.classList.remove('bg-slate-800/60', 'text-slate-300', 'hover:bg-slate-700/80');

      const filterValue = btn.getAttribute('data-filter');

      galleryItems.forEach(item => {
        const itemCategory = item.getAttribute('data-category');
        if (filterValue === 'all' || itemCategory === filterValue) {
          item.style.display = 'block';
          setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'scale(1)';
          }, 50);
        } else {
          item.style.opacity = '0';
          item.style.transform = 'scale(0.95)';
          setTimeout(() => {
            item.style.display = 'none';
          }, 300);
        }
      });
    });
  });

  // Client Login Modal Logic
  const loginModal = document.getElementById('login-modal');
  const openLoginBtns = document.querySelectorAll('.open-login-btn');
  const closeLoginBtn = document.getElementById('close-login-btn');
  const demoCodeBtn = document.getElementById('demo-code-btn');
  const loginForm = document.getElementById('client-login-form');
  const passcodeError = document.getElementById('passcode-error');

  openLoginBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      if (loginModal) {
        loginModal.classList.add('active');
      }
    });
  });

  if (closeLoginBtn) {
    closeLoginBtn.addEventListener('click', () => {
      if (loginModal) {
        loginModal.classList.remove('active');
      }
    });
  }

  // Close modal when clicking outside box
  if (loginModal) {
    loginModal.addEventListener('click', (e) => {
      if (e.target === loginModal) {
        loginModal.classList.remove('active');
      }
    });
  }

  // Quick Demo Code autofill
  if (demoCodeBtn) {
    demoCodeBtn.addEventListener('click', () => {
      const codeInput = document.getElementById('event-passcode');
      if (codeInput) {
        codeInput.value = 'DEMO2026';
        if (passcodeError) passcodeError.classList.add('hidden');
      }
    });
  }

  // Handle Client Login Submission
  if (loginForm) {
    loginForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const codeInput = document.getElementById('event-passcode');
      const passVal = codeInput ? codeInput.value.trim().toUpperCase() : '';

      if (!passVal) {
        if (passcodeError) {
          passcodeError.textContent = 'Please enter your Event Passcode.';
          passcodeError.classList.remove('hidden');
        }
        return;
      }

      // Valid codes accepted (DEMO2026 or any 4+ character passcode)
      if (passVal.length >= 4) {
        // Save session state
        localStorage.setItem('obm_client_authenticated', 'true');
        localStorage.setItem('obm_client_passcode', passVal);

        // Show feedback UI on submit button
        const submitBtn = loginForm.querySelector('button[type="submit"]');
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = `<i data-lucide="loader-2" class="animate-spin w-5 h-5 inline mr-2"></i> Opening Portal...`;
          if (window.lucide) lucide.createIcons();
        }

        // Redirect to photo selection workspace inside the same directory
        setTimeout(() => {
          window.location.href = './photo-selection.html';
        }, 800);
      } else {
        if (passcodeError) {
          passcodeError.textContent = 'Invalid passcode format. Try DEMO2026';
          passcodeError.classList.remove('hidden');
        }
      }
    });
  }

  // Lightbox Image Preview Modal
  const previewModal = document.getElementById('preview-modal');
  const previewImg = document.getElementById('preview-modal-img');
  const previewTitle = document.getElementById('preview-modal-title');
  const closePreviewBtn = document.getElementById('close-preview-btn');

  document.querySelectorAll('.preview-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const card = btn.closest('.gallery-card');
      const img = card ? card.querySelector('img') : null;
      const title = card ? card.querySelector('h3') : null;

      if (previewModal && img) {
        previewImg.src = img.src;
        if (previewTitle && title) {
          previewTitle.textContent = title.textContent;
        }
        previewModal.classList.add('active');
      }
    });
  });

  if (closePreviewBtn && previewModal) {
    closePreviewBtn.addEventListener('click', () => {
      previewModal.classList.remove('active');
    });
    previewModal.addEventListener('click', (e) => {
      if (e.target === previewModal) {
        previewModal.classList.remove('active');
      }
    });
  }
});
