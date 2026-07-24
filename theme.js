/**
 * OBM Studio — Global Theme Engine
 * Persists theme selection via localStorage across all pages.
 * Applies CSS custom properties + aurora blob colors on every page.
 *
 * Usage: import this FIRST in every page's <head> (before styles.css loads).
 * Then call window.OBMTheme.set('sapphire') to switch.
 */

(function () {
  'use strict';

  const STORAGE_KEY = 'obm_theme';

  /**
   * Theme definitions:
   *  accent       — primary highlight / gradient start
   *  accent2      — gradient end / secondary color
   *  blob1        — aurora blob 1 color (RGBA)
   *  blob2        — aurora blob 2 color (RGBA)
   *  blob3        — aurora blob 3 color (RGBA)
   *  blob4        — aurora blob 4 color (RGBA)
   *  selectionBg  — ::selection bg
   *  label        — display name
   */
  const THEMES = {
    sapphire: {
      label: 'Sapphire Ice',
      accent: '#00d2ff',
      accent2: '#3a7bd5',
      accentRGB: '0,210,255',
      blob1: 'rgba(0,210,255,0.18)',
      blob2: 'rgba(167,139,250,0.15)',
      blob3: 'rgba(255,183,3,0.12)',
      blob4: 'rgba(16,185,129,0.08)',
      selectionBg: '#00d2ff',
      bgBase: '#020407',
    },
    amethyst: {
      label: 'Amethyst Glow',
      accent: '#a78bfa',
      accent2: '#7c3aed',
      accentRGB: '167,139,250',
      blob1: 'rgba(167,139,250,0.2)',
      blob2: 'rgba(99,102,241,0.18)',
      blob3: 'rgba(244,63,94,0.1)',
      blob4: 'rgba(0,210,255,0.07)',
      selectionBg: '#a78bfa',
      bgBase: '#040210',
    },
    emerald: {
      label: 'Emerald Forest',
      accent: '#10b981',
      accent2: '#059669',
      accentRGB: '16,185,129',
      blob1: 'rgba(16,185,129,0.2)',
      blob2: 'rgba(5,150,105,0.15)',
      blob3: 'rgba(6,182,212,0.12)',
      blob4: 'rgba(255,183,3,0.07)',
      selectionBg: '#10b981',
      bgBase: '#020a06',
    },
    rose: {
      label: 'Rose Quartz',
      accent: '#f43f5e',
      accent2: '#be123c',
      accentRGB: '244,63,94',
      blob1: 'rgba(244,63,94,0.18)',
      blob2: 'rgba(167,139,250,0.12)',
      blob3: 'rgba(251,191,36,0.1)',
      blob4: 'rgba(0,210,255,0.06)',
      selectionBg: '#f43f5e',
      bgBase: '#0a0204',
    },
    amber: {
      label: 'Amber Sunset',
      accent: '#ffb703',
      accent2: '#fb8500',
      accentRGB: '255,183,3',
      blob1: 'rgba(255,183,3,0.2)',
      blob2: 'rgba(251,133,0,0.15)',
      blob3: 'rgba(244,63,94,0.08)',
      blob4: 'rgba(167,139,250,0.07)',
      selectionBg: '#ffb703',
      bgBase: '#080500',
    },
    white: {
      label: 'Pearl White',
      accent: '#2563eb',
      accent2: '#0284c7',
      accentRGB: '37,99,235',
      blob1: 'rgba(37,99,235,0.12)',
      blob2: 'rgba(168,85,247,0.10)',
      blob3: 'rgba(236,72,153,0.08)',
      blob4: 'rgba(14,165,233,0.08)',
      selectionBg: '#2563eb',
      bgBase: '#f8fafc',
      isLight: true,
    }
  };

  // Apply theme: set CSS variables + recolor aurora blobs
  function apply(name) {
    const t = THEMES[name] || THEMES.sapphire;
    const root = document.documentElement;

    // ── Light mode class toggle
    if (t.isLight) {
      root.classList.add('theme-light');
    } else {
      root.classList.remove('theme-light');
    }

    // ── CSS custom properties
    root.style.setProperty('--theme-accent',    t.accent);
    root.style.setProperty('--theme-accent2',   t.accent2);
    root.style.setProperty('--theme-accentRGB', t.accentRGB);
    root.style.setProperty('--theme-name',      `'${name}'`);
    root.style.setProperty('--cyan',            t.accent);
    root.style.setProperty('--grad-cyan',
      `linear-gradient(135deg, ${t.accent} 0%, ${t.accent2} 60%, #7f00ff 100%)`);

    root.style.setProperty('--bg-primary',      t.bgBase);

    // ── Background base color (if body exists)
    if (document.body) {
      document.body.style.backgroundColor = t.bgBase;
    }

    // ── Aurora blobs: update inline background styles if blobs exist
    const blobs = [
      { el: document.querySelector('.aurora-blob-1'), color: t.blob1 },
      { el: document.querySelector('.aurora-blob-2'), color: t.blob2 },
      { el: document.querySelector('.aurora-blob-3'), color: t.blob3 },
      { el: document.querySelector('.aurora-blob-4'), color: t.blob4 },
    ];
    blobs.forEach(({ el, color }) => {
      if (!el) return;
      el.style.background = `radial-gradient(circle, ${color} 0%, transparent 70%)`;
    });

    // ── photo-selection.html liquid blobs (different class names)
    const liquidBlobs = document.querySelectorAll('.liquid-blob-1, .liquid-blob-2, .liquid-blob-3');
    liquidBlobs.forEach((el, i) => {
      const colors = [t.blob1, t.blob2, t.blob3];
      el.style.background = colors[i] ? colors[i].replace(/rgba\(/, 'rgb(').replace(/,[\d.]+\)/, ')') : undefined;
    });

    // ── nav badge + pill accent ring
    const navBadge = document.querySelector('.nav-logo-ring');
    if (navBadge) {
      navBadge.style.setProperty('--accent', t.accent);
    }

    // ── Gradient text elements reuse --theme-accent now
    root.style.setProperty('--theme-selection-bg', t.selectionBg);

    // ── Active filter button / theme dot
    document.querySelectorAll('.theme-switcher-dot').forEach(dot => {
      dot.classList.toggle('ring-2', dot.dataset.theme === name);
      dot.classList.toggle('ring-white', dot.dataset.theme === name);
      dot.classList.toggle('scale-110', dot.dataset.theme === name);
    });

    // ── dispatch global event so other scripts can react
    window.dispatchEvent(new CustomEvent('obmthemechange', { detail: { name, theme: t } }));
  }

  function set(name) {
    if (!THEMES[name]) name = 'sapphire';
    localStorage.setItem(STORAGE_KEY, name);
    apply(name);
  }

  function get() {
    return localStorage.getItem(STORAGE_KEY) || 'sapphire';
  }

  function init() {
    apply(get());
    // Re-apply after DOM is ready (blobs may not exist yet at script parse time)
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => apply(get()));
    }
  }

  window.OBMTheme = { set, get, apply, themes: THEMES, init };
  init();

})();
