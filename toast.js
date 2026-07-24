/**
 * OBM Studio - Universal Toast Notification Engine
 * Supports: sapphire, gold, success, error, warning, purple themes
 * Supports: top-right, top-left, top-center, bottom-right, bottom-left, bottom-center
 */

(function() {
  'use strict';

  // Default config
  const DEFAULT = {
    duration: 4000,
    position: 'top-right',
    theme: 'sapphire',
    showProgress: true,
    closeOnClick: true,
    pauseOnHover: true,
    icon: null
  };

  const THEME_ICONS = {
    sapphire:  { svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#00d2ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12l4 6-10 12L2 9z"/></svg>' },
    gold:      { svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ffb703" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>' },
    success:   { svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>' },
    error:     { svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>' },
    warning:   { svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>' },
    purple:    { svg: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>' }
  };

  const containers = {};

  function getContainer(position) {
    if (!containers[position]) {
      const el = document.createElement('div');
      el.className = `toast-container toast-pos-${position}`;
      document.body.appendChild(el);
      containers[position] = el;
    }
    return containers[position];
  }

  window.showToast = function(title, message, theme = 'sapphire', options = {}) {
    const config = { ...DEFAULT, ...options, theme, title, message };
    const container = getContainer(config.position);
    const themeIcon = THEME_ICONS[config.theme] || THEME_ICONS.sapphire;
    const iconHtml = config.icon || themeIcon.svg;

    const toastEl = document.createElement('div');
    toastEl.className = `toast-item toast-theme-${config.theme}`;
    toastEl.innerHTML = `
      <div class="toast-body">
        <div class="toast-icon-box">${iconHtml}</div>
        <div class="toast-content">
          <p class="toast-title">${config.title}</p>
          ${config.message ? `<p class="toast-msg">${config.message}</p>` : ''}
        </div>
        <button class="toast-close-btn" aria-label="Close">×</button>
      </div>
      ${config.showProgress ? `<div class="toast-progress-bar" style="animation-duration:${config.duration}ms"></div>` : ''}
    `;

    container.appendChild(toastEl);

    let dismissed = false;
    let paused = false;
    let elapsed = 0;
    let start = Date.now();
    let timer;

    function dismiss() {
      if (dismissed) return;
      dismissed = true;
      toastEl.classList.remove('toast-show');
      toastEl.classList.add('toast-hide');
      setTimeout(() => toastEl.remove(), 400);
    }

    function startTimer() {
      timer = setTimeout(dismiss, config.duration - elapsed);
    }

    // Show
    requestAnimationFrame(() => {
      requestAnimationFrame(() => toastEl.classList.add('toast-show'));
    });

    startTimer();

    // Pause on hover
    if (config.pauseOnHover) {
      toastEl.addEventListener('mouseenter', () => {
        paused = true;
        clearTimeout(timer);
        elapsed += Date.now() - start;
        const bar = toastEl.querySelector('.toast-progress-bar');
        if (bar) bar.style.animationPlayState = 'paused';
      });
      toastEl.addEventListener('mouseleave', () => {
        paused = false;
        start = Date.now();
        const bar = toastEl.querySelector('.toast-progress-bar');
        if (bar) bar.style.animationPlayState = 'running';
        startTimer();
      });
    }

    // Close button
    toastEl.querySelector('.toast-close-btn').addEventListener('click', dismiss);
    if (config.closeOnClick) {
      toastEl.addEventListener('click', (e) => {
        if (!e.target.classList.contains('toast-close-btn')) dismiss();
      });
    }

    return { dismiss };
  };

  // Convenience aliases
  window.toast = {
    success: (title, msg, opts) => showToast(title, msg, 'success', opts),
    error:   (title, msg, opts) => showToast(title, msg, 'error', opts),
    warning: (title, msg, opts) => showToast(title, msg, 'warning', opts),
    info:    (title, msg, opts) => showToast(title, msg, 'sapphire', opts),
    gold:    (title, msg, opts) => showToast(title, msg, 'gold', opts),
    purple:  (title, msg, opts) => showToast(title, msg, 'purple', opts),
  };

})();
