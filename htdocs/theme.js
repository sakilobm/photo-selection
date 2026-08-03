/**
 * OBM Studio — Global Dual-Layer Theme Engine (Mode + Color Accent)
 * Base Modes: 'dark' (Black) | 'light' (White)
 * Color Accents: 'sapphire' (Sky Blue) | 'amethyst' (Purple) | 'emerald' (Green) | 'rose' (Red) | 'amber' (Yellow)
 */

(function () {
  'use strict';

  const MODE_KEY = 'obm_theme_mode';
  const ACCENT_KEY = 'obm_theme_accent';

  const ACCENTS = {
    sapphire: {
      label: 'Sky Blue',
      dark: {
        accent: '#00d2ff', accent2: '#3a7bd5', accentRGB: '0,210,255',
        blob1: 'rgba(0,210,255,0.18)', blob2: 'rgba(167,139,250,0.15)',
        blob3: 'rgba(255,183,3,0.12)', blob4: 'rgba(16,185,129,0.08)',
        selectionBg: '#00d2ff', bgBase: '#020407'
      },
      light: {
        accent: '#0284c7', accent2: '#2563eb', accentRGB: '2,132,199',
        blob1: 'rgba(2,132,199,0.14)', blob2: 'rgba(147,51,234,0.10)',
        blob3: 'rgba(234,88,12,0.08)', blob4: 'rgba(16,185,129,0.08)',
        selectionBg: '#0284c7', bgBase: '#f8fafc'
      }
    },
    amethyst: {
      label: 'Purple',
      dark: {
        accent: '#a78bfa', accent2: '#7c3aed', accentRGB: '167,139,250',
        blob1: 'rgba(167,139,250,0.2)', blob2: 'rgba(99,102,241,0.18)',
        blob3: 'rgba(244,63,94,0.1)', blob4: 'rgba(0,210,255,0.07)',
        selectionBg: '#a78bfa', bgBase: '#040210'
      },
      light: {
        accent: '#8b5cf6', accent2: '#6d28d9', accentRGB: '139,92,246',
        blob1: 'rgba(139,92,246,0.15)', blob2: 'rgba(124,58,237,0.12)',
        blob3: 'rgba(244,63,94,0.08)', blob4: 'rgba(2,132,199,0.06)',
        selectionBg: '#8b5cf6', bgBase: '#f8fafc'
      }
    },
    emerald: {
      label: 'Green',
      dark: {
        accent: '#10b981', accent2: '#059669', accentRGB: '16,185,129',
        blob1: 'rgba(16,185,129,0.2)', blob2: 'rgba(5,150,105,0.15)',
        blob3: 'rgba(6,182,212,0.12)', blob4: 'rgba(255,183,3,0.07)',
        selectionBg: '#10b981', bgBase: '#020a06'
      },
      light: {
        accent: '#059669', accent2: '#047857', accentRGB: '5,150,105',
        blob1: 'rgba(5,150,105,0.15)', blob2: 'rgba(16,185,129,0.12)',
        blob3: 'rgba(2,132,199,0.08)', blob4: 'rgba(234,88,12,0.06)',
        selectionBg: '#059669', bgBase: '#f8fafc'
      }
    },
    rose: {
      label: 'Red',
      dark: {
        accent: '#f43f5e', accent2: '#be123c', accentRGB: '244,63,94',
        blob1: 'rgba(244,63,94,0.18)', blob2: 'rgba(167,139,250,0.12)',
        blob3: 'rgba(251,191,36,0.1)', blob4: 'rgba(0,210,255,0.06)',
        selectionBg: '#f43f5e', bgBase: '#0a0204'
      },
      light: {
        accent: '#e11d48', accent2: '#be123c', accentRGB: '225,29,72',
        blob1: 'rgba(225,29,72,0.15)', blob2: 'rgba(168,85,247,0.1)',
        blob3: 'rgba(245,158,11,0.08)', blob4: 'rgba(2,132,199,0.06)',
        selectionBg: '#e11d48', bgBase: '#f8fafc'
      }
    },
    amber: {
      label: 'Yellow',
      dark: {
        accent: '#ffb703', accent2: '#fb8500', accentRGB: '255,183,3',
        blob1: 'rgba(255,183,3,0.2)', blob2: 'rgba(251,133,0,0.15)',
        blob3: 'rgba(244,63,94,0.08)', blob4: 'rgba(167,139,250,0.07)',
        selectionBg: '#ffb703', bgBase: '#080500'
      },
      light: {
        accent: '#d97706', accent2: '#b45309', accentRGB: '217,119,6',
        blob1: 'rgba(217,119,6,0.15)', blob2: 'rgba(245,158,11,0.12)',
        blob3: 'rgba(225,29,72,0.08)', blob4: 'rgba(139,92,246,0.06)',
        selectionBg: '#d97706', bgBase: '#f8fafc'
      }
    }
  };

  function getMode() {
    return localStorage.getItem(MODE_KEY) || 'light';
  }

  function getAccent() {
    return localStorage.getItem(ACCENT_KEY) || 'sapphire';
  }

  function apply(mode, accent) {
    mode = (mode === 'light') ? 'light' : 'dark';
    if (!ACCENTS[accent]) accent = 'sapphire';

    const root = document.documentElement;
    const isLight = (mode === 'light');
    const accentDef = ACCENTS[accent];
    const t = isLight ? accentDef.light : accentDef.dark;

    // Mode Toggle Class
    if (isLight) {
      root.classList.add('theme-light');
    } else {
      root.classList.remove('theme-light');
    }

    // Custom Properties
    root.style.setProperty('--theme-accent', t.accent);
    root.style.setProperty('--theme-accent2', t.accent2);
    root.style.setProperty('--theme-accentRGB', t.accentRGB);
    root.style.setProperty('--cyan', t.accent);
    root.style.setProperty('--grad-cyan', `linear-gradient(135deg, ${t.accent} 0%, ${t.accent2} 60%, #7f00ff 100%)`);
    root.style.setProperty('--bg-primary', t.bgBase);
    root.style.setProperty('--theme-selection-bg', t.selectionBg);

    if (document.body) {
      document.body.style.backgroundColor = t.bgBase;
    }

    // Aurora blobs
    const blobs = [
      { el: document.querySelector('.aurora-blob-1'), color: t.blob1 },
      { el: document.querySelector('.aurora-blob-2'), color: t.blob2 },
      { el: document.querySelector('.aurora-blob-3'), color: t.blob3 },
      { el: document.querySelector('.aurora-blob-4'), color: t.blob4 },
    ];
    blobs.forEach(({ el, color }) => {
      if (el) el.style.background = `radial-gradient(circle, ${color} 0%, transparent 70%)`;
    });

    const liquidBlobs = document.querySelectorAll('.liquid-blob-1, .liquid-blob-2, .liquid-blob-3');
    liquidBlobs.forEach((el, i) => {
      const colors = [t.blob1, t.blob2, t.blob3];
      if (el && colors[i]) {
        el.style.background = colors[i].replace(/rgba\(/, 'rgb(').replace(/,[\d.]+\)/, ')');
      }
    });

    // Update Mode Toggle UI Buttons
    document.querySelectorAll('.mode-toggle-btn').forEach(btn => {
      const bMode = btn.dataset.mode;
      btn.classList.toggle('active', bMode === mode);
    });

    // Update Color Dots UI
    document.querySelectorAll('.theme-switcher-dot').forEach(dot => {
      const dAccent = dot.dataset.theme;
      const isSelected = (dAccent === accent);
      dot.classList.toggle('ring-2', isSelected);
      dot.classList.toggle('ring-white', isSelected);
      dot.classList.toggle('scale-110', isSelected);
    });

    // Global Event
    window.dispatchEvent(new CustomEvent('obmthemechange', {
      detail: { mode, accent, isLight, accentDef, theme: t }
    }));
  }

  function setMode(mode) {
    localStorage.setItem(MODE_KEY, mode);
    apply(mode, getAccent());
  }

  function setAccent(accent) {
    localStorage.setItem(ACCENT_KEY, accent);
    apply(getMode(), accent);
  }

  // Backward compatibility setter
  function set(name) {
    if (name === 'white') {
      setMode('light');
    } else if (ACCENTS[name]) {
      setAccent(name);
    }
  }

  function init() {
    apply(getMode(), getAccent());
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => apply(getMode(), getAccent()));
    }
  }

  window.OBMTheme = {
    setMode, setAccent, set, getMode, getAccent, apply, accents: ACCENTS, init
  };
  init();

})();
