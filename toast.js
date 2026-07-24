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
    sapphire:  { emoji: '💎', color: '#00d2ff' },
    gold:      { emoji: '✨', color: '#ffb703' },
    success:   { emoji: '✅', color: '#34d399' },
    error:     { emoji: '❌', color: '#f87171' },
    warning:   { emoji: '⚠️', color: '#fbbf24' },
    purple:    { emoji: '🔮', color: '#a78bfa' }
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
    const icon = config.icon || themeIcon.emoji;

    const toastEl = document.createElement('div');
    toastEl.className = `toast-item toast-theme-${config.theme}`;
    toastEl.innerHTML = `
      <div class="toast-body">
        <div class="toast-icon-box" style="font-size:1.4rem">${icon}</div>
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
