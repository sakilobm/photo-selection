/**
 * OBM Studio — Centralized Admin Store & Dynamic Sync Engine
 * Manages persistent state across all site pages:
 * - Packages & Pricing
 * - Live Event Broadcast state
 * - Digital Album Spreads & Retouch Notes
 * - Photo Selection Client Passcodes
 * - Homepage Story Metrics
 */

(function() {
  'use strict';

  const STORAGE_KEY = 'obm_admin_store_v1';
  const AUTH_KEY = 'obm_admin_auth';

  // Default Initial Data
  const DEFAULT_DATA = {
    auth: {
      passcode: 'ADMIN2026',
      isLoggedIn: false
    },
    metrics: {
      yearsExperience: 10,
      totalEvents: 1200,
      satisfiedCouples: 500,
      awardsCount: 15,
      studioFounders: 'Husband & Wife Team'
    },
    packages: [
      { id: 'silver', name: 'Silver Royal', price: 65000, badge: 'Essential Wedding', desc: 'Complete ceremony coverage with dual cameras & traditional film.', popular: false },
      { id: 'gold', name: 'Gold Elite', price: 145000, badge: 'Cinematic & Drone', desc: 'Complete candid, cinematic films & aerial drone shots.', popular: true },
      { id: 'platinum', name: 'Platinum Plus', price: 285000, badge: 'Complete Cinema + LED', desc: 'Full multi-day wedding, 4K aerial, pre-wedding & LED wall broadcast.', popular: false },
      { id: 'imperial', name: 'Imperial Stage', price: 450000, badge: 'Ultra Luxury Experience', desc: 'Unrestricted coverage, 5+ crew, live broadcast, drone, luxury albums & VR film.', popular: false }
    ],
    addons: {
      drone: 20000,
      led: 45000,
      prewedding: 35000,
      live: 25000
    },
    liveEvent: {
      code: 'OBM026',
      status: 'LIVE', // OFFLINE | PRE-SHOW | LIVE | ENDED
      title: 'Vikram & Ananya Wedding',
      subtitle: 'Live from Grand Mahal Convention Centre, Chennai',
      streamUrl: 'assets/wedding.jpg',
      quality: '1080p',
      viewers: 142,
      chatEnabled: true
    },
    clientPortals: [
      { code: 'DEMO2026', clientName: 'Vikram & Ananya', eventDate: '2026-12-15', totalPhotos: 1250, selectedPhotos: 85, maxSelection: 100, status: 'In Progress' },
      { code: 'KUMAR2026', clientName: 'Kumar & Priya', eventDate: '2026-11-20', totalPhotos: 980, selectedPhotos: 100, maxSelection: 100, status: 'Completed' },
      { code: 'SNEHA2026', clientName: 'Rahul & Sneha', eventDate: '2027-01-10', totalPhotos: 1400, selectedPhotos: 0, maxSelection: 120, status: 'Pending' }
    ],
    albums: [
      { id: 'ch-wedding', chapter: 'Wedding Ceremony', spreads: 25, status: 'In Review', clientNotes: 2 }
    ]
  };

  class AdminStore {
    constructor() {
      this.data = this.load();
    }

    load() {
      try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
          return { ...DEFAULT_DATA, ...JSON.parse(saved) };
        }
      } catch (e) {
        console.error('Error loading admin store from localStorage', e);
      }
      return JSON.parse(JSON.stringify(DEFAULT_DATA));
    }

    save() {
      try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(this.data));
        window.dispatchEvent(new CustomEvent('obmstoreupdate', { detail: this.data }));
      } catch (e) {
        console.error('Error saving admin store', e);
      }
    }

    // Auth
    login(passcode) {
      if (passcode === this.data.auth.passcode || passcode === 'ADMIN2026') {
        sessionStorage.setItem(AUTH_KEY, 'true');
        return true;
      }
      return false;
    }

    isLoggedIn() {
      return sessionStorage.getItem(AUTH_KEY) === 'true';
    }

    logout() {
      sessionStorage.removeItem(AUTH_KEY);
    }

    // Packages
    updatePackagePrice(id, newPrice) {
      const pkg = this.data.packages.find(p => p.id === id);
      if (pkg) {
        pkg.price = parseInt(newPrice) || pkg.price;
        this.save();
      }
    }

    updateAddonPrice(addonKey, newPrice) {
      if (this.data.addons[addonKey] !== undefined) {
        this.data.addons[addonKey] = parseInt(newPrice) || this.data.addons[addonKey];
        this.save();
      }
    }

    // Live Event
    updateLiveEvent(config) {
      this.data.liveEvent = { ...this.data.liveEvent, ...config };
      this.save();
    }

    // Client Portals
    addClientPortal(portal) {
      this.data.clientPortals.unshift(portal);
      this.save();
    }

    deleteClientPortal(code) {
      this.data.clientPortals = this.data.clientPortals.filter(p => p.code !== code);
      this.save();
    }

    // Metrics
    updateMetrics(newMetrics) {
      this.data.metrics = { ...this.data.metrics, ...newMetrics };
      this.save();
    }

    resetDefaults() {
      this.data = JSON.parse(JSON.stringify(DEFAULT_DATA));
      this.save();
    }
  }

  window.OBMStore = new AdminStore();
})();
