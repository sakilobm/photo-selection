/**
 * OBM STUDIO - MODERN CUSTOMIZABLE TOAST NOTIFICATION ENGINE
 * Version 2.0.0
 */

class ToastEngine {
  constructor() {
    this.containers = {};
    this.audioCtx = null;
    this.soundEnabled = true;
    this.initContainers();
  }

  initContainers() {
    const positions = ['top-right', 'top-left', 'top-center', 'bottom-right', 'bottom-left', 'bottom-center'];
    positions.forEach(pos => {
      let container = document.getElementById(`toast-container-${pos}`);
      if (!container) {
        container = document.createElement('div');
        container.id = `toast-container-${pos}`;
        container.className = `toast-container toast-pos-${pos}`;
        document.body.appendChild(container);
      }
      this.containers[pos] = container;
    });
  }

  playChime(type) {
    if (!this.soundEnabled) return;
    try {
      if (!this.audioCtx) {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (AudioContext) this.audioCtx = new AudioContext();
      }
      if (this.audioCtx && this.audioCtx.state === 'suspended') {
        this.audioCtx.resume();
      }
      if (!this.audioCtx) return;

      const osc = this.audioCtx.createOscillator();
      const gain = this.audioCtx.createGain();
      osc.connect(gain);
      gain.connect(this.audioCtx.destination);

      const now = this.audioCtx.currentTime;
      let freq = 520;
      if (type === 'gold' || type === 'success') freq = 659.25; // E5
      else if (type === 'warning') freq = 440; // A4
      else if (type === 'error') freq = 330; // E4
      else if (type === 'sapphire') freq = 880; // A5

      osc.frequency.setValueAtTime(freq, now);
      osc.frequency.exponentialRampToValueAtTime(freq * 1.5, now + 0.12);

      gain.gain.setValueAtTime(0.12, now);
      gain.gain.exponentialRampToValueAtTime(0.001, now + 0.25);

      osc.start(now);
      osc.stop(now + 0.25);
    } catch (e) {
      // Audio fallback
    }
  }

  show(config = {}) {
    const {
      title = 'Notification',
      message = '',
      type = 'sapphire', // success, error, warning, info, gold, sapphire, purple
      position = 'bottom-right',
      duration = 4000,
      showProgress = true,
      actionText = null,
      onAction = null,
      icon = null
    } = config;

    const targetContainer = this.containers[position] || this.containers['bottom-right'];

    // Create Toast Element
    const toast = document.createElement('div');
    toast.className = `toast-item toast-theme-${type}`;

    // Select Icon
    let iconMarkup = icon;
    if (!iconMarkup) {
      switch (type) {
        case 'success': iconMarkup = '<i data-lucide="check-circle" class="w-5 h-5 text-emerald-400"></i>'; break;
        case 'gold': iconMarkup = '<i data-lucide="sparkles" class="w-5 h-5 text-amber-400"></i>'; break;
        case 'warning': iconMarkup = '<i data-lucide="alert-triangle" class="w-5 h-5 text-amber-400"></i>'; break;
        case 'error': iconMarkup = '<i data-lucide="x-circle" class="w-5 h-5 text-rose-400"></i>'; break;
        case 'purple': iconMarkup = '<i data-lucide="crown" class="w-5 h-5 text-purple-400"></i>'; break;
        default: iconMarkup = '<i data-lucide="bell" class="w-5 h-5 text-cyan-400"></i>'; break;
      }
    }

    toast.innerHTML = `
      <div class="toast-body">
        <div class="toast-icon-box">${iconMarkup}</div>
        <div class="toast-content">
          <h4 class="toast-title">${title}</h4>
          ${message ? `<p class="toast-msg">${message}</p>` : ''}
        </div>
        ${actionText ? `<button class="toast-action-btn">${actionText}</button>` : ''}
        <button class="toast-close-btn" aria-label="Close">&times;</button>
      </div>
      ${showProgress && duration > 0 ? `<div class="toast-progress-bar" style="animation-duration: ${duration}ms;"></div>` : ''}
    `;

    // Render Lucide icons inside toast
    if (window.lucide) {
      lucide.createIcons({ el: toast });
    }

    // Attach Action Handler
    if (actionText && typeof onAction === 'function') {
      const actionBtn = toast.querySelector('.toast-action-btn');
      if (actionBtn) {
        actionBtn.addEventListener('click', () => {
          onAction();
          this.dismiss(toast);
        });
      }
    }

    // Close Button Event
    const closeBtn = toast.querySelector('.toast-close-btn');
    if (closeBtn) {
      closeBtn.addEventListener('click', () => this.dismiss(toast));
    }

    // Append to container
    targetContainer.appendChild(toast);
    this.playChime(type);

    // Trigger Entrance Animation
    requestAnimationFrame(() => {
      toast.classList.add('toast-show');
    });

    // Auto Dismiss
    if (duration > 0) {
      toast.dismissTimer = setTimeout(() => {
        this.dismiss(toast);
      }, duration);
    }

    return toast;
  }

  dismiss(toast) {
    if (!toast) return;
    if (toast.dismissTimer) clearTimeout(toast.dismissTimer);
    toast.classList.remove('toast-show');
    toast.classList.add('toast-hide');
    setTimeout(() => {
      if (toast.parentNode) {
        toast.parentNode.removeChild(toast);
      }
    }, 350);
  }
}

// Global Toast Singleton
window.obmToast = new ToastEngine();

// Convenience shorthand
window.showToast = function(title, message, type = 'sapphire', options = {}) {
  return window.obmToast.show({ title, message, type, ...options });
};
