/**
 * OBM Studio — Centralized Admin Store & Dynamic Sync Engine
 * Manages persistent state across all site pages:
 * - Packages & Pricing
 * - Live Event Broadcast state
 * - Digital Album Spreads & Retouch Notes
 * - Photo Selection Client Passcodes
 * - Homepage Story Metrics
 * - Client Directory (full CRUD with flag/block/download)
 * - Upload Queue & Dispatch Engine
 * - Deleted Photo Detection per client
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
      {
        code: 'DEMO2026', clientName: 'Vikram & Ananya', email: 'vikram@example.com',
        eventDate: '2026-12-15', totalPhotos: 1250, selectedPhotos: 85, maxSelection: 100,
        status: 'In Progress', flag: 'COMPLETED', blocked: false, flagged: true,
        addedDate: '2026-07-15',
        photos: {
          approved: [
            { name: 'OBM_Candid_Ceremony_001.jpg', category: 'CANDID', size: '4.2 MB' },
            { name: 'OBM_Portrait_Couple_002.jpg', category: 'PORTRAIT', size: '3.8 MB' },
            { name: 'OBM_Traditional_Ritual_003.jpg', category: 'TRADITIONAL', size: '5.1 MB' }
          ],
          rejected: [
            { name: 'OBM_Candid_Wedding_001.jpg', category: 'CANDID', size: '4.5 MB', thumb: 'https://images.unsplash.com/photo-1519741497674-611481863552?w=80&h=80&fit=crop' },
            { name: 'OBM_Portrait_Bridal_002.jpg', category: 'PORTRAIT', size: '3.2 MB', thumb: 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=80&h=80&fit=crop' },
            { name: 'OBM_Traditional_Ritual_003.jpg', category: 'TRADITIONAL', size: '5.8 MB', thumb: 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?w=80&h=80&fit=crop' },
            { name: 'OBM_Candid_Laughter_004.jpg', category: 'CANDID', size: '3.9 MB', thumb: 'https://images.unsplash.com/photo-1591604466107-ec97de577aff?w=80&h=80&fit=crop' },
            { name: 'OBM_Portrait_Studio_005.jpg', category: 'PORTRAIT', size: '4.1 MB', thumb: 'https://images.unsplash.com/photo-1537633552985-df8429e8048b?w=80&h=80&fit=crop' },
            { name: 'OBM_Traditional_Mandap_006.jpg', category: 'TRADITIONAL', size: '6.2 MB', thumb: 'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?w=80&h=80&fit=crop' },
            { name: 'OBM_Candid_Dance_007.jpg', category: 'CANDID', size: '3.7 MB', thumb: 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=80&h=80&fit=crop' },
            { name: 'OBM_Portrait_Family_008.jpg', category: 'PORTRAIT', size: '4.3 MB', thumb: 'https://images.unsplash.com/photo-1529634597503-139d3726fed5?w=80&h=80&fit=crop' }
          ],
          deleted: []
        }
      },
      {
        code: 'KUMAR2026', clientName: 'Kumar & Priya', email: 'priya@example.com',
        eventDate: '2026-11-20', totalPhotos: 980, selectedPhotos: 100, maxSelection: 100,
        status: 'Completed', flag: 'COMPLETED', blocked: false, flagged: true,
        addedDate: '2026-07-15',
        photos: {
          approved: [
            { name: 'OBM_CandidKP_001.jpg', category: 'CANDID', size: '4.0 MB' },
            { name: 'OBM_PortraitKP_002.jpg', category: 'PORTRAIT', size: '3.5 MB' },
            { name: 'OBM_TraditionalKP_003.jpg', category: 'TRADITIONAL', size: '5.0 MB' }
          ],
          rejected: [
            { name: 'OBM_CandidKP_Reject_001.jpg', category: 'CANDID', size: '3.8 MB', thumb: 'https://images.unsplash.com/photo-1519741497674-611481863552?w=80&h=80&fit=crop' },
            { name: 'OBM_PortraitKP_Reject_002.jpg', category: 'PORTRAIT', size: '4.1 MB', thumb: 'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=80&h=80&fit=crop' }
          ],
          deleted: []
        }
      },
      {
        code: 'SNEHA2026', clientName: 'Rahul & Sneha', email: 'arun@example.com',
        eventDate: '2027-01-10', totalPhotos: 1400, selectedPhotos: 0, maxSelection: 120,
        status: 'Pending', flag: 'PENDING', blocked: false, flagged: false,
        addedDate: '2026-07-18',
        photos: { approved: [], rejected: [], deleted: [] }
      },
      {
        code: 'MEERA2026', clientName: 'Meera Nair', email: 'meera@example.com',
        eventDate: '2026-10-05', totalPhotos: 600, selectedPhotos: 2, maxSelection: 80,
        status: 'Blocked', flag: 'BLOCKED', blocked: true, flagged: false,
        addedDate: '2026-07-10',
        photos: { approved: [], rejected: [], deleted: [] }
      }
    ],
    albums: [
      { id: 'ch-wedding', chapter: 'Wedding Ceremony', spreads: 25, status: 'In Review', clientNotes: 2 }
    ],
    uploadQueue: []
  };

  class AdminStore {
    constructor() {
      this.data = this.load();
    }

    load() {
      try {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) {
          const parsed = JSON.parse(saved);
          // Deep merge: ensure new fields from DEFAULT_DATA are present
          const merged = JSON.parse(JSON.stringify(DEFAULT_DATA));
          Object.keys(parsed).forEach(key => {
            if (key === 'clientPortals' && Array.isArray(parsed[key])) {
              // Merge client portals: ensure each portal has all required fields
              merged.clientPortals = parsed[key].map(p => {
                const defaultPortal = DEFAULT_DATA.clientPortals[0];
                return {
                  ...defaultPortal,
                  ...p,
                  photos: p.photos || { approved: [], rejected: [], deleted: [] },
                  email: p.email || '',
                  flag: p.flag || 'PENDING',
                  blocked: p.blocked || false,
                  flagged: p.flagged || false,
                  addedDate: p.addedDate || new Date().toISOString().split('T')[0]
                };
              });
            } else {
              merged[key] = parsed[key];
            }
          });
          return merged;
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

    // Client Portals — Full CRUD
    addClientPortal(portal) {
      // Ensure all required fields
      const fullPortal = {
        code: portal.code || '',
        clientName: portal.clientName || '',
        email: portal.email || '',
        eventDate: portal.eventDate || new Date().toISOString().split('T')[0],
        totalPhotos: portal.totalPhotos || 0,
        selectedPhotos: portal.selectedPhotos || 0,
        maxSelection: portal.maxSelection || 100,
        status: portal.status || 'Pending',
        flag: 'PENDING',
        blocked: false,
        flagged: false,
        addedDate: new Date().toISOString().split('T')[0],
        photos: { approved: [], rejected: [], deleted: [] }
      };
      this.data.clientPortals.unshift(fullPortal);
      this.save();
    }

    updateClientPortal(code, updates) {
      const client = this.data.clientPortals.find(p => p.code === code);
      if (client) {
        Object.assign(client, updates);
        this.save();
      }
    }

    toggleClientFlag(code) {
      const client = this.data.clientPortals.find(p => p.code === code);
      if (client) {
        client.flagged = !client.flagged;
        if (client.flagged) {
          client.flag = 'COMPLETED';
          client.status = 'Completed';
        } else {
          client.flag = client.blocked ? 'BLOCKED' : 'PENDING';
          client.status = client.blocked ? 'Blocked' : 'Pending';
        }
        this.save();
      }
    }

    toggleClientBlock(code) {
      const client = this.data.clientPortals.find(p => p.code === code);
      if (client) {
        client.blocked = !client.blocked;
        if (client.blocked) {
          client.flag = 'BLOCKED';
          client.status = 'Blocked';
        } else {
          client.flag = client.flagged ? 'COMPLETED' : 'PENDING';
          client.status = client.flagged ? 'Completed' : 'In Progress';
        }
        this.save();
      }
    }

    deleteClientPortal(code) {
      this.data.clientPortals = this.data.clientPortals.filter(p => p.code !== code);
      this.save();
    }

    getActiveClients() {
      return this.data.clientPortals.filter(p => !p.blocked);
    }

    getClientByCode(code) {
      return this.data.clientPortals.find(p => p.code === code);
    }

    // Metrics
    updateMetrics(newMetrics) {
      this.data.metrics = { ...this.data.metrics, ...newMetrics };
      this.save();
    }

    resetDefaults() {
      localStorage.removeItem(STORAGE_KEY);
      this.data = JSON.parse(JSON.stringify(DEFAULT_DATA));
      this.save();
    }
  }

  window.OBMStore = new AdminStore();
})();
