// --- CENTRALIZED APP STATE ---
let currentAuthMode = 'login';
let currentUser = null;
let selectedCategory = 'all';
let activeLightboxIndex = null;
let activeTheme = 'sapphire';
let activeTab = 'gallery';

// Action History (Stack for Undo/Redo operations)
let deletedPhotosHistory = [];
let selectionHistory = [];

// --- Modal callback store ---
let _modalResolve = null;

// ==========================================
// Featured Masterpieces
// ==========================================

/**
 * showConfirmModal — Reusable glassmorphic confirmation modal.
 * Use this INSTEAD of native confirm() anywhere in the project.
 *
 * @param {Object} options
 * @param {string} options.title       — Modal heading
 * @param {string} options.message     — Description / body text
 * @param {string} options.icon        — Lucide icon name (default: 'alert-triangle')
 * @param {string} options.type        — Color preset: 'warning' | 'danger' | 'info' | 'success' (default: 'warning')
 * @param {string} options.confirmText — Confirm button label (default: 'Confirm')
 * @param {string} options.cancelText  — Cancel button label  (default: 'Cancel')
 * @param {string} options.confirmIcon — Lucide icon for confirm btn (default: 'check')
 * @param {boolean} options.dangerBtn  — If true, confirm button is red (default: false)
 * @returns {Promise<boolean>} Resolves true if confirmed, false if cancelled
 */
function showConfirmModal({
    title = 'Are you sure?',
    message = 'This action cannot be undone.',
    icon = 'alert-triangle',
    type = 'warning',
    confirmText = 'Confirm',
    cancelText = 'Cancel',
    confirmIcon = 'check',
    dangerBtn = false
} = {}) {
    return new Promise((resolve) => {
        _modalResolve = resolve;

        const modal = document.getElementById('obmModal');
        const iconRing = document.getElementById('obmModalIconRing');
        const iconEl = document.getElementById('obmModalIcon');
        const titleEl = document.getElementById('obmModalTitle');
        const msgEl = document.getElementById('obmModalMessage');
        const confirmBtnTextEl = document.getElementById('obmModalConfirmText');
        const cancelBtnTextEl = document.getElementById('obmModalCancelText');
        const confirmBtnEl = document.getElementById('obmModalConfirm');
        const confirmIconEl = document.getElementById('obmModalConfirmIcon');
        const cancelBtnEl = document.getElementById('obmModalCancel');

        // Set content
        titleEl.innerText = title;
        msgEl.innerText = message;
        confirmBtnTextEl.innerText = confirmText;
        cancelBtnTextEl.innerText = cancelText;

        // Set icon (re-render Lucide)
        iconEl.setAttribute('data-lucide', icon);
        confirmIconEl.setAttribute('data-lucide', confirmIcon);

        // Set color preset
        iconRing.className = 'obm-modal-icon-ring modal-' + type;

        // Set danger button style
        confirmBtnEl.classList.toggle('btn-danger', dangerBtn);

        // Show cancel button (confirm modals always have it)
        cancelBtnEl.style.display = '';

        // Activate modal
        modal.classList.remove('closing');
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');

        lucide.createIcons();
    });
}

/**
 * showAlertModal — Info-only modal with just an OK button (no cancel).
 * Use this INSTEAD of native alert() anywhere in the project.
 */
function showAlertModal({
    title = 'Notice',
    message = '',
    icon = 'info',
    type = 'info',
    okText = 'Got It',
    okIcon = 'check'
} = {}) {
    return new Promise((resolve) => {
        _modalResolve = () => resolve(true);

        const modal = document.getElementById('obmModal');
        const iconRing = document.getElementById('obmModalIconRing');
        const iconEl = document.getElementById('obmModalIcon');
        const titleEl = document.getElementById('obmModalTitle');
        const msgEl = document.getElementById('obmModalMessage');
        const confirmBtnTextEl = document.getElementById('obmModalConfirmText');
        const confirmBtnEl = document.getElementById('obmModalConfirm');
        const confirmIconEl = document.getElementById('obmModalConfirmIcon');
        const cancelBtnEl = document.getElementById('obmModalCancel');

        titleEl.innerText = title;
        msgEl.innerText = message;
        confirmBtnTextEl.innerText = okText;

        iconEl.setAttribute('data-lucide', icon);
        confirmIconEl.setAttribute('data-lucide', okIcon);

        iconRing.className = 'obm-modal-icon-ring modal-' + type;
        confirmBtnEl.classList.remove('btn-danger');

        // Hide cancel button for alert-style modals
        cancelBtnEl.style.display = 'none';

        modal.classList.remove('closing');
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');

        lucide.createIcons();
    });
}

/**
 * closeModal — Resolves the modal promise and runs close animation.
 * Called by the modal button onclick handlers.
 */
function closeModal(confirmed) {
    const modal = document.getElementById('obmModal');

    // Trigger closing animation
    modal.classList.add('closing');

    setTimeout(() => {
        modal.classList.remove('active', 'closing');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');

        if (_modalResolve) {
            _modalResolve(confirmed);
            _modalResolve = null;
        }
    }, 280);
}

// Close modal on backdrop click
document.addEventListener('click', (e) => {
    const modal = document.getElementById('obmModal');
    if (modal && modal.classList.contains('active') && e.target.classList.contains('obm-modal-backdrop')) {
        closeModal(false);
    }
});

// Close modal on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const modal = document.getElementById('obmModal');
        if (modal && modal.classList.contains('active')) {
            closeModal(false);
        }
    }
});

// Dynamic Dummy Database (OBM Premium Portfolio)
let photoDatabase = [
    { id: 1, name: 'OBM_Candid_Wedding_001.jpg', category: 'candid', url: 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80' },
    { id: 2, name: 'OBM_Portrait_Bridal_002.jpg', category: 'portrait', url: 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=800&q=80' },
    { id: 3, name: 'OBM_Traditional_Ritual_003.jpg', category: 'traditional', url: 'https://images.unsplash.com/photo-1606800052052-a08af7148866?auto=format&fit=crop&w=800&q=80' },
    { id: 4, name: 'OBM_Candid_Laughter_004.jpg', category: 'candid', url: 'https://images.unsplash.com/photo-1532712938310-34cb3982ef74?auto=format&fit=crop&w=800&q=80' },
    { id: 5, name: 'OBM_Portrait_Studio_005.jpg', category: 'portrait', url: 'https://images.unsplash.com/photo-1523438885200-e635ba2c371e?auto=format&fit=crop&w=800&q=80' },
    { id: 6, name: 'OBM_Traditional_Temple_006.jpg', category: 'traditional', url: 'https://images.unsplash.com/photo-1607190074257-dd4b7af0309f?auto=format&fit=crop&w=800&q=80' },
    { id: 7, name: 'OBM_Candid_Dance_007.jpg', category: 'candid', url: 'https://images.unsplash.com/photo-1549417229-aa67d3263c09?auto=format&fit=crop&w=800&q=80' },
    { id: 8, name: 'OBM_Portrait_Outdoor_008.jpg', category: 'portrait', url: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=800&q=80' }
];

// Track user selections (photo ids)
let selectedPhotoIds = new Set();

// Carousel State variables
let carouselCurrentIndex = 0;
let carouselIntervalId = null;

// Initialize Application
document.addEventListener('DOMContentLoaded', () => {
    // Restore from Cache if exists
    loadSelectionsFromCache();

    // Render Views & Components
    lucide.createIcons();
    renderCategories();
    initCarousel();
    setupDragAndDrop();

    // Set default theme from localStorage or default
    const cachedTheme = localStorage.getItem('obm_theme') || 'sapphire';
    setAppTheme(cachedTheme, null, true);

    // Setup swipe/slide event listeners
    initSlideGestures();

    // Keyboard controller in lightbox
    document.addEventListener('keydown', handleKeyboardShortcuts);

    // Bypass Login for quick access / preview
    bypassLoginForTesting();
});

// Developer login bypass
function bypassLoginForTesting() {
    const email = localStorage.getItem('obm_client_email') || 'sakil@obmstudio.com';
    const name = localStorage.getItem('obm_client_name') || 'Sakil Dev';
    
    // Auto-login bypassing form, but still show the gorgeous loading sequence!
    loadClientWorkspace(email, name);
}

// Progressive asset allocation and workspace synchronization loader
function loadClientWorkspace(email, username) {
    currentUser = username;
    localStorage.setItem('obm_client_name', currentUser);
    localStorage.setItem('obm_client_email', email);

    const loginView = document.getElementById('authView');
    const galleryView = document.getElementById('galleryView');
    const loadingScreen = document.getElementById('portalLoadingScreen');
    const loadingEmail = document.getElementById('portalLoadingEmail');
    const loadingStatus = document.getElementById('portalLoadingStatus');
    const progressBar = document.getElementById('portalProgressBar');
    const progressText = document.getElementById('portalProgressText');

    // DOM Log Steps
    const stepConnect = document.getElementById('step-connect');
    const stepQuery = document.getElementById('step-query');
    const stepDownload = document.getElementById('step-download');
    const stepRender = document.getElementById('step-render');

    // Setup initial text
    if (loadingEmail) loadingEmail.innerText = email;
    if (loginView) loginView.classList.remove('active');
    if (loadingScreen) loadingScreen.style.display = 'flex';

    // Reset log layout classes and icons
    const resetStep = (el, defaultText) => {
        if (!el) return;
        el.className = "flex items-center gap-2 opacity-50";
        el.innerHTML = `<i data-lucide="circle" class="w-3.5 h-3.5 text-gray-500"></i><span>${defaultText}</span>`;
    };
    resetStep(stepConnect, "Initialize studio handshake...");
    resetStep(stepQuery, `Query allocations for user email...`);
    resetStep(stepDownload, "Retrieve metadata & details...");
    resetStep(stepRender, "Build liquid interactive workspace...");
    lucide.createIcons();

    // Step sequence timeline triggers
    let progress = 0;
    if (progressBar) progressBar.style.width = '0%';
    if (progressText) progressText.innerText = '0%';

    // Step 1: Handshake active
    if (stepConnect) {
        stepConnect.className = "flex items-center gap-2 text-white font-semibold";
        stepConnect.innerHTML = `<i data-lucide="loader" class="w-3.5 h-3.5 text-[var(--theme-accent)] animate-spin"></i><span>Initialize studio handshake...</span>`;
    }
    lucide.createIcons();

    const interval = setInterval(() => {
        progress += 2;
        if (progressBar) progressBar.style.width = `${progress}%`;
        if (progressText) progressText.innerText = `${progress}%`;

        // Step triggers
        if (progress === 26) {
            // Check Handshake
            if (stepConnect) {
                stepConnect.className = "flex items-center gap-2 text-emerald-400 font-semibold";
                stepConnect.innerHTML = `<i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-500"></i><span class="text-gray-300">Studio handshake established</span>`;
            }
            
            // Query allocations
            if (stepQuery) {
                stepQuery.className = "flex items-center gap-2 text-white font-semibold";
                stepQuery.innerHTML = `<i data-lucide="loader" class="w-3.5 h-3.5 text-[var(--theme-accent)] animate-spin"></i><span>Allocating storage block for ${email}...</span>`;
            }
            if (loadingStatus) loadingStatus.innerText = "Querying allocated client assets";
            lucide.createIcons();
        } else if (progress === 50) {
            // Check query
            if (stepQuery) {
                stepQuery.className = "flex items-center gap-2 text-emerald-400 font-semibold";
                stepQuery.innerHTML = `<i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-500"></i><span class="text-gray-300">Resolved allocation block successfully</span>`;
            }

            // Download metadata
            if (stepDownload) {
                stepDownload.className = "flex items-center gap-2 text-white font-semibold";
                stepDownload.innerHTML = `<i data-lucide="loader" class="w-3.5 h-3.5 text-[var(--theme-accent)] animate-spin"></i><span>Syncing image assets catalog...</span>`;
            }
            if (loadingStatus) loadingStatus.innerText = "Downloading assets metadata";
            lucide.createIcons();
        } else if (progress === 76) {
            // Check download
            if (stepDownload) {
                stepDownload.className = "flex items-center gap-2 text-emerald-400 font-semibold";
                stepDownload.innerHTML = `<i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-500"></i><span class="text-gray-300">Retrieved 8 high-res asset files</span>`;
            }

            // Render workspace
            if (stepRender) {
                stepRender.className = "flex items-center gap-2 text-white font-semibold";
                stepRender.innerHTML = `<i data-lucide="loader" class="w-3.5 h-3.5 text-[var(--theme-accent)] animate-spin"></i><span>Compiling layout view parameters...</span>`;
            }
            if (loadingStatus) loadingStatus.innerText = "Rendering Client Workspace";
            lucide.createIcons();
        } else if (progress >= 100) {
            clearInterval(interval);
            
            if (stepRender) {
                stepRender.className = "flex items-center gap-2 text-emerald-400 font-semibold";
                stepRender.innerHTML = `<i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-500"></i><span class="text-gray-300">Interactive workspace is ready</span>`;
            }
            lucide.createIcons();

            setTimeout(() => {
                // Fade out loader screen
                if (loadingScreen) loadingScreen.style.display = 'none';
                if (galleryView) {
                    galleryView.classList.add('active');
                }

                // Render database logs
                document.getElementById('clientNameDisplay').innerText = currentUser;
                refreshGallery();
                initCarousel();
                showToast('success', 'Portal Connected', `Synchronized workspace allocations for ${email}.`);
            }, 600);
        }
    }, 50); // ~2.5 seconds total load duration
}

// ==========================================
// DYNAMIC THEME ENGINE (Ripple and Colors)
// ==========================================
function setAppTheme(themeName, event, skipRipple = false) {
    const root = document.documentElement;

    // If theme is switched via click, trigger ripple overlay animation
    if (event && !skipRipple) {
        createThemeRipple(event);
    }

    // Remove existing theme classes
    root.className = root.className.replace(/theme-\w+/g, '');
    root.classList.add(`theme-${themeName}`);
    activeTheme = themeName;
    localStorage.setItem('obm_theme', themeName);

    // Redraw icons and highlights
    updateThemeIndicators();
}

function createThemeRipple(e) {
    const container = document.getElementById('themeWaveContainer');
    const wave = document.createElement('div');
    wave.classList.add('theme-wave');

    // Set spawn coordinates
    const x = e.clientX;
    const y = e.clientY;
    wave.style.left = `${x - 5}px`;
    wave.style.top = `${y - 5}px`;

    container.appendChild(wave);

    // Trigger transition layout paint
    requestAnimationFrame(() => {
        wave.classList.add('expand');
    });

    // Cleanup
    setTimeout(() => {
        wave.remove();
    }, 600);
}

function updateThemeIndicators() {
    // Repaint selected nodes to match theme shifts
    updateCounter();
    if (activeTab === 'dashboard') {
        refreshDashboard();
    }
}

// ==========================================
// ACTION HISTORY & PERSISTENCE
// ==========================================
function saveSelectionsToCache() {
    localStorage.setItem('obm_selected_ids', JSON.stringify(Array.from(selectedPhotoIds)));
}

function loadSelectionsFromCache() {
    const cached = localStorage.getItem('obm_selected_ids');
    if (cached) {
        try {
            const ids = JSON.parse(cached);
            selectedPhotoIds = new Set(ids);
        } catch (e) {
            console.error("Selection cache load error", e);
        }
    }
}

// ==========================================
// AUTHENTICATION LOGIC
// ==========================================
function switchAuthTab(mode) {
    currentAuthMode = mode;
    const tabLogin = document.getElementById('tabLogin');
    const tabSignup = document.getElementById('tabSignup');
    const nameField = document.getElementById('nameField');
    const authBtnText = document.getElementById('authBtnText');
    const authSubtitle = document.getElementById('authSubtitle');

    tabLogin.className = "flex-1 py-2.5 rounded-lg text-sm font-semibold transition-all duration-300 text-gray-400 hover:text-white";
    tabSignup.className = "flex-1 py-2.5 rounded-lg text-sm font-semibold transition-all duration-300 text-gray-400 hover:text-white";

    if (mode === 'login') {
        tabLogin.className = "flex-1 py-2.5 rounded-lg text-sm font-semibold transition-all duration-300 bg-[var(--theme-accent)] text-black font-medium";
        nameField.classList.add('hidden');
        document.getElementById('authName').removeAttribute('required');
        authBtnText.innerText = "Unlock My Gallery";
        authSubtitle.innerText = "SELECT & PERSONALIZE YOUR SHOTS";
    } else {
        tabSignup.className = "flex-1 py-2.5 rounded-lg text-sm font-semibold transition-all duration-300 bg-[var(--theme-accent)] text-black font-medium";
        nameField.classList.remove('hidden');
        document.getElementById('authName').setAttribute('required', 'true');
        authBtnText.innerText = "Create Client Portal";
        authSubtitle.innerText = "INITIALIZE SECURE DISK SPACE";
    }
}

function handleAuth(event) {
    event.preventDefault();
    const nameInput = document.getElementById('authName').value || 'Premium Client';
    const emailInput = document.getElementById('authEmail').value || 'client@example.com';
    const codeInput = document.getElementById('authCode').value;

    const username = currentAuthMode === 'signup' ? nameInput : (nameInput ? nameInput : 'Premium Client');

    // Smooth loading sequence instead of instant view swap
    loadClientWorkspace(emailInput, username);
}

function logout() {
    currentUser = null;
    selectedPhotoIds.clear();
    localStorage.removeItem('obm_client_name');
    localStorage.removeItem('obm_client_email');
    localStorage.removeItem('obm_selected_ids');

    document.getElementById('galleryView').classList.remove('active');
    document.getElementById('authView').classList.add('active');
    document.getElementById('authForm').reset();
    switchAuthTab('login');

    showToast('info', 'Logged Out', 'Your local session was safely terminated.');
}

// ==========================================
// FEATURED CAROUSEL SLIDER ENGINE
// ==========================================
function initCarousel() {
    const track = document.getElementById('carouselTrack');

    // Grab first 4 photos for slider highlights
    const sliderItems = photoDatabase.slice(0, 4);

    if (sliderItems.length === 0) {
        track.innerHTML = `<div class="text-center py-10 w-full text-gray-500">No items available to highlight</div>`;
        return;
    }

    track.innerHTML = sliderItems.map((photo, i) => `
        <div class="w-full shrink-0 aspect-[21/9] md:aspect-[3/1] relative select-none rounded-2xl overflow-hidden slider-wrapper">
            <img src="${photo.url}" alt="${photo.name}" class="w-full h-full object-cover slider-img">
            <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent flex flex-col justify-end p-6 md:p-8">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-extrabold tracking-wider bg-[var(--theme-accent)] text-black">${photo.category}</span>
                    <span class="text-xs text-gray-400 font-medium">OBM Highlight</span>
                </div>
                <h4 class="text-lg md:text-2xl font-extrabold text-white tracking-wide truncate">${photo.name}</h4>
                <p class="text-xs text-gray-300 font-light mt-1 hidden sm:block">Frosted liquid designs with premium custom selection triggers.</p>
            </div>
        </div>
    `).join('');

    // Dots building
    const dotsContainer = document.getElementById('carouselDots');
    dotsContainer.innerHTML = sliderItems.map((_, i) => `
        <button onclick="jumpToSlide(${i})" id="carouselDot-${i}" class="w-2 h-2 rounded-full bg-white/20 hover:bg-white/55 transition-all duration-300"></button>
    `).join('');

    updateCarouselDots();
    startAutoSlide();
}

function updateCarouselDots() {
    const dots = document.querySelectorAll('#carouselDots button');
    dots.forEach((dot, idx) => {
        if (idx === carouselCurrentIndex) {
            dot.className = "w-5 h-2 rounded-full bg-[var(--theme-accent)] transition-all duration-300";
        } else {
            dot.className = "w-2 h-2 rounded-full bg-white/20 hover:bg-white/55 transition-all duration-300";
        }
    });
}

function slideCarouselNext() {
    const track = document.getElementById('carouselTrack');
    if (!track) return;
    const totalSlides = track.children.length;
    if (totalSlides === 0) return;

    carouselCurrentIndex = (carouselCurrentIndex + 1) % totalSlides;
    track.style.transform = `translateX(-${carouselCurrentIndex * 100}%)`;
    updateCarouselDots();
}

function slideCarouselPrev() {
    const track = document.getElementById('carouselTrack');
    if (!track) return;
    const totalSlides = track.children.length;
    if (totalSlides === 0) return;

    carouselCurrentIndex = (carouselCurrentIndex - 1 + totalSlides) % totalSlides;
    track.style.transform = `translateX(-${carouselCurrentIndex * 100}%)`;
    updateCarouselDots();
}

function jumpToSlide(idx) {
    const track = document.getElementById('carouselTrack');
    if (!track) return;
    carouselCurrentIndex = idx;
    track.style.transform = `translateX(-${carouselCurrentIndex * 100}%)`;
    updateCarouselDots();
    resetAutoSlideTimer();
}

// Timer handlers
function startAutoSlide() {
    carouselIntervalId = setInterval(slideCarouselNext, 6000);
}

function stopAutoSlide() {
    if (carouselIntervalId) clearInterval(carouselIntervalId);
}

function resetAutoSlideTimer() {
    stopAutoSlide();
    startAutoSlide();
}

// ==========================================
// DYNAMIC DRAG & DROP FILE UPLOAD
// ==========================================
function setupDragAndDrop() {
    const dropzone = document.getElementById('uploadDropzone');
    const fileInput = document.getElementById('fileUploadInput');
    if (!dropzone || !fileInput) return;

    dropzone.addEventListener('click', () => fileInput.click());

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('bg-[var(--theme-accent)]/10', 'border-[var(--theme-accent)]');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('bg-[var(--theme-accent)]/10', 'border-[var(--theme-accent)]');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('bg-[var(--theme-accent)]/10', 'border-[var(--theme-accent)]');
        const files = e.dataTransfer.files;
        handleFilesUpload(files);
    });

    fileInput.addEventListener('change', (e) => {
        const files = e.target.files;
        handleFilesUpload(files);
    });
}

function handleFilesUpload(files) {
    if (!files || files.length === 0) return;

    let loadedCount = 0;
    Array.from(files).forEach(file => {
        if (!file.type.startsWith('image/')) {
            showToast('alert', 'Invalid File Type', `${file.name} is not an image.`);
            return;
        }

        const url = URL.createObjectURL(file);
        const id = Date.now() + Math.floor(Math.random() * 1000);

        photoDatabase.unshift({
            id: id,
            name: file.name,
            category: 'uploads',
            url: url
        });
        loadedCount++;
    });

    if (loadedCount > 0) {
        showToast('success', `${loadedCount} Photos Loaded`, 'Added successfully to custom uploads.');

        // Switch view to uploads category to display newly uploaded files
        filterCategory('uploads');
        refreshGallery();
        initCarousel(); // Recalculate slider images
    }
}

// ==========================================
// CATEGORIES & GALLERY VIEW ENGINE
// ==========================================
function renderCategories() {
    // Standard category slots + dynamic custom upload bucket
    const categories = ['all', 'candid', 'portrait', 'traditional', 'uploads'];
    const container = document.getElementById('categoryContainer');
    if (!container) return;

    container.innerHTML = categories.map(cat => {
        const isActive = selectedCategory === cat;
        return `
            <button onclick="filterCategory('${cat}')" id="catBtn-${cat}" class="px-4 py-2 rounded-xl text-xs font-semibold capitalize transition-all duration-300 border ${isActive ? 'bg-[var(--theme-accent)] border-[var(--theme-accent)] text-black' : 'bg-white/5 text-gray-400 border-white/5 hover:border-white/15 hover:text-white'}">
                ${cat === 'uploads' ? 'Custom Uploads' : cat}
            </button>
        `;
    }).join('');
}

function filterCategory(category) {
    selectedCategory = category;
    renderCategories();
    renderGrid();
}

function refreshGallery() {
    renderGrid();
    updateCounter();
    renderCategories();
    if (activeTab === 'dashboard') {
        refreshDashboard();
    }
}

function renderGrid() {
    const grid = document.getElementById('imageGrid');
    if (!grid) return;
    const filteredData = selectedCategory === 'all'
        ? photoDatabase
        : photoDatabase.filter(p => p.category === selectedCategory);

    if (filteredData.length === 0) {
        grid.innerHTML = `
            <div class="col-span-full py-16 text-center glass-panel rounded-2xl border border-white/10 flex flex-col items-center justify-center p-8">
                <i data-lucide="inbox" class="w-10 h-10 text-gray-500 mb-3"></i>
                <p class="text-sm font-bold text-gray-300">Workspace slot is empty</p>
                <p class="text-xs text-gray-500 mt-1 max-w-xs leading-relaxed">No photos found here. You can upload custom local photos inside the drag box to populate this folder.</p>
            </div>
        `;
        lucide.createIcons();
        return;
    }

    grid.innerHTML = filteredData.map((photo) => {
        const isSelected = selectedPhotoIds.has(photo.id);
        return `
            <div id="photoCard-${photo.id}" class="group glass-card relative overflow-hidden flex flex-col ${isSelected ? 'selected' : ''}">
                <!-- Visual Container -->
                <div class="aspect-[3/4] overflow-hidden relative photo-zoom-trigger select-none" onclick="openLightbox(${photo.id})">
                    <img src="${photo.url}" alt="${photo.name}" loading="lazy" class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                    
                    <!-- Overlays Gradient and Actions -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-black/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-between p-3.5">
                        <div class="flex justify-end gap-1.5 z-20">
                            <!-- Dynamic Quick Heart select -->
                            <button onclick="toggleSelection(${photo.id}, event)" class="p-2 rounded-xl backdrop-blur-md bg-black/40 border border-white/10 text-white hover:bg-[var(--theme-accent)] hover:text-black transition-all cursor-pointer custom-cursor-hide" title="Select Photo">
                                <i data-lucide="heart" class="w-4 h-4 ${isSelected ? 'fill-black text-black' : ''}"></i>
                            </button>
                            <!-- Delete option -->
                            <button onclick="deletePhoto(${photo.id}, event)" class="p-2 rounded-xl backdrop-blur-md bg-red-500/10 border border-red-500/20 text-red-400 hover:bg-red-500 hover:text-white transition-all cursor-pointer custom-cursor-hide" title="Delete Photo">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>

                        <span class="text-[10px] text-gray-300 font-semibold tracking-wide truncate w-full pl-0.5 z-20">${photo.name}</span>
                    </div>
                </div>

                <!-- Mini Footer Bar for selections state -->
                <div class="p-3 bg-white/5 border-t border-white/5 flex items-center justify-between pointer-events-auto">
                    <span class="text-[9px] uppercase tracking-widest text-gray-500 font-bold">${photo.category}</span>
                    <button onclick="toggleSelection(${photo.id}, event)" class="text-xs flex items-center gap-1 font-bold ${isSelected ? 'text-[var(--theme-accent)]' : 'text-gray-400 hover:text-white'} transition-colors">
                        <i data-lucide="${isSelected ? 'check-circle-2' : 'circle'}" class="w-3.5 h-3.5"></i>
                        <span>${isSelected ? 'Selected' : 'Select'}</span>
                    </button>
                </div>
            </div>
        `;
    }).join('');

    lucide.createIcons();
    updateActionToolbar();
}

// ==========================================
// OPERATION ACTIONS (SELECT & DELETE)
// ==========================================
function toggleSelection(id, event) {
    if (event) event.stopPropagation(); // Block lightbox zoom click

    if (selectedPhotoIds.has(id)) {
        selectedPhotoIds.delete(id);
        showToast('info', 'Deselected Shot', 'Removed from selection list.');
    } else {
        selectedPhotoIds.add(id);
        showToast('success', 'Photo Selected', 'Added to final review list.');
    }

    saveSelectionsToCache();
    refreshGallery();

    // Sync lightbox details
    if (document.getElementById('lightboxModal').style.display === 'flex') {
        updateLightboxControls(id);
    }
}

function deletePhoto(id, event) {
    if (event) event.stopPropagation();

    const idx = photoDatabase.findIndex(p => p.id === id);
    if (idx === -1) return;

    const targetPhoto = photoDatabase[idx];

    // Animation fade trigger before array splicing
    const cardEl = document.getElementById(`photoCard-${id}`);
    if (cardEl) {
        cardEl.style.transform = 'scale(0.9) translateY(20px)';
        cardEl.style.opacity = '0';
    }

    setTimeout(() => {
        // Delete logic
        photoDatabase.splice(idx, 1);

        // Cache deletion action to history
        deletedPhotosHistory.push({
            photo: targetPhoto,
            originalIndex: idx,
            wasSelected: selectedPhotoIds.has(id)
        });

        // Clear selection if it was active
        selectedPhotoIds.delete(id);
        saveSelectionsToCache();

        refreshGallery();
        initCarousel(); // Recalculate hero slider highlights

        // Advanced interactive toast callback
        showToast('action', 'Photo Deleted', `Removed: ${targetPhoto.name}`, 'Undo', () => {
            undoLastDelete();
        });
    }, 300);
}

function undoLastDelete() {
    if (deletedPhotosHistory.length === 0) return;

    const restorePayload = deletedPhotosHistory.pop();
    const photo = restorePayload.photo;
    const originalIndex = restorePayload.originalIndex;

    // Splice back in original position
    photoDatabase.splice(originalIndex, 0, photo);

    // Re-select if it was previously chosen
    if (restorePayload.wasSelected) {
        selectedPhotoIds.add(photo.id);
        saveSelectionsToCache();
    }

    refreshGallery();
    initCarousel();

    showToast('success', 'Restored Photo', `Recovered: ${photo.name}`);
}

// Action Toolbar Selection Triggers
function updateActionToolbar() {
    const toolbar = document.getElementById('actionToolbar');
    const countLabel = document.getElementById('toolbarSelectedCount');
    if (!toolbar) return;

    // Only display action toolbar if in gallery view
    if (selectedPhotoIds.size > 0 && activeTab === 'gallery') {
        toolbar.classList.remove('hidden');
        toolbar.classList.add('flex');
        countLabel.innerText = selectedPhotoIds.size;
    } else {
        toolbar.classList.remove('flex');
        toolbar.classList.add('hidden');
    }
}

function bulkDeselect() {
    selectedPhotoIds.clear();
    saveSelectionsToCache();
    refreshGallery();
    showToast('info', 'Selections Cleared', 'All photo marks were removed.');
}

function bulkDelete() {
    if (selectedPhotoIds.size === 0) return;

    const selectedArray = Array.from(selectedPhotoIds);
    let deletedCount = 0;

    selectedArray.forEach(id => {
        const idx = photoDatabase.findIndex(p => p.id === id);
        if (idx !== -1) {
            const targetPhoto = photoDatabase[idx];
            photoDatabase.splice(idx, 1);

            deletedPhotosHistory.push({
                photo: targetPhoto,
                originalIndex: idx,
                wasSelected: true
            });
            deletedCount++;
        }
    });

    selectedPhotoIds.clear();
    saveSelectionsToCache();
    refreshGallery();
    initCarousel();

    showToast('action', 'Bulk Delete Completed', `${deletedCount} photos deleted.`, 'Undo All', () => {
        // Undo loop for this bulk slice
        for (let i = 0; i < deletedCount; i++) {
            undoLastDelete();
        }
    });
}

function updateCounter() {
    const total = photoDatabase.length;
    const currentCount = selectedPhotoIds.size;
    const counterEl = document.getElementById('selectionCounter');
    if (counterEl) {
        counterEl.innerText = `${currentCount} / ${total}`;
    }
}

// Global Reset trigger
async function triggerResetGallery() {
    const confirmed = await showConfirmModal({
        title: 'Reset Entire Workspace?',
        message: 'All your selections will be cleared and deleted photos will be restored to their original state. This cannot be reversed.',
        icon: 'rotate-ccw',
        type: 'danger',
        confirmText: 'Reset Workspace',
        cancelText: 'Keep Current',
        confirmIcon: 'rotate-ccw',
        dangerBtn: true
    });

    if (!confirmed) return;

    localStorage.removeItem('obm_selected_ids');
    selectedPhotoIds.clear();

    // Reinitialize basic database
    photoDatabase = [
        { id: 1, name: 'OBM_Candid_Wedding_001.jpg', category: 'candid', url: 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80' },
        { id: 2, name: 'OBM_Portrait_Bridal_002.jpg', category: 'portrait', url: 'https://images.unsplash.com/photo-1511285560929-80b456fea0bc?auto=format&fit=crop&w=800&q=80' },
        { id: 3, name: 'OBM_Traditional_Ritual_003.jpg', category: 'traditional', url: 'https://images.unsplash.com/photo-1606800052052-a08af7148866?auto=format&fit=crop&w=800&q=80' },
        { id: 4, name: 'OBM_Candid_Laughter_004.jpg', category: 'candid', url: 'https://images.unsplash.com/photo-1532712938310-34cb3982ef74?auto=format&fit=crop&w=800&q=80' },
        { id: 5, name: 'OBM_Portrait_Studio_005.jpg', category: 'portrait', url: 'https://images.unsplash.com/photo-1523438885200-e635ba2c371e?auto=format&fit=crop&w=800&q=80' },
        { id: 6, name: 'OBM_Traditional_Temple_006.jpg', category: 'traditional', url: 'https://images.unsplash.com/photo-1607190074257-dd4b7af0309f?auto=format&fit=crop&w=800&q=80' },
        { id: 7, name: 'OBM_Candid_Dance_007.jpg', category: 'candid', url: 'https://images.unsplash.com/photo-1549417229-aa67d3263c09?auto=format&fit=crop&w=800&q=80' },
        { id: 8, name: 'OBM_Portrait_Outdoor_008.jpg', category: 'portrait', url: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=800&q=80' }
    ];

    deletedPhotosHistory = [];
    selectedCategory = 'all';

    // Switch to gallery tab if on dashboard
    switchTab('gallery');

    refreshGallery();
    initCarousel();

    showToast('info', 'Workspace Restored', 'Seeding base dataset complete.');
}

// ==========================================
// LIQUID LIGHTBOX & SWIPE SYSTEM
// ==========================================
function openLightbox(photoId) {
    const index = photoDatabase.findIndex(p => p.id === photoId);
    if (index === -1) return;

    activeLightboxIndex = index;
    const photo = photoDatabase[index];

    const modal = document.getElementById('lightboxModal');
    modal.style.display = 'flex';

    // Lock background body scroll
    document.body.style.overflow = 'hidden';

    // Populate visual track
    document.getElementById('lightboxImage').src = photo.url;
    document.getElementById('lightboxFilename').innerText = photo.name;

    updateLightboxControls(photoId);
    buildLightboxThumbnails();

    // Trigger animation repaint
    const imgNode = document.getElementById('lightboxImage');
    imgNode.style.transform = 'scale(0.95)';
    setTimeout(() => {
        imgNode.style.transform = 'scale(1)';
    }, 50);
}

function updateLightboxControls(photoId) {
    const btn = document.getElementById('lightboxSelectBtn');
    const indexLabel = document.getElementById('lightboxIndex');
    const isSelected = selectedPhotoIds.has(photoId);

    indexLabel.innerText = `${activeLightboxIndex + 1} of ${photoDatabase.length}`;

    if (isSelected) {
        btn.className = "flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold bg-[var(--theme-accent)] text-black hover:opacity-90 active:scale-95 transition-all";
        btn.innerHTML = `<i data-lucide="check" class="w-4 h-4"></i> Selected`;
    } else {
        btn.className = "flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold bg-white/5 border border-white/10 text-white hover:bg-white/10 active:scale-95 transition-all";
        btn.innerHTML = `<i data-lucide="heart" class="w-4 h-4"></i> Add to Selection`;
    }

    btn.onclick = (e) => toggleSelection(photoId, e);
    lucide.createIcons();
}

function buildLightboxThumbnails() {
    const container = document.getElementById('lightboxThumbs');
    if (!container) return;
    container.innerHTML = photoDatabase.map((p, i) => {
        const isActive = i === activeLightboxIndex;
        return `
            <button onclick="jumpLightboxTo(${i})" class="w-10 h-10 rounded-lg overflow-hidden border-2 shrink-0 transition-transform duration-300 ${isActive ? 'border-[var(--theme-accent)] scale-105' : 'border-transparent opacity-50 hover:opacity-100'}">
                <img src="${p.url}" class="w-full h-full object-cover">
            </button>
        `;
    }).join('');
}

function closeLightbox() {
    document.getElementById('lightboxModal').style.display = 'none';
    document.body.style.overflow = '';
    activeLightboxIndex = null;
}

function navigateLightbox(direction) {
    if (activeLightboxIndex === null || photoDatabase.length === 0) return;

    let newIndex = activeLightboxIndex + direction;
    if (newIndex >= photoDatabase.length) newIndex = 0;
    if (newIndex < 0) newIndex = photoDatabase.length - 1;

    jumpLightboxTo(newIndex, direction);
}

function jumpLightboxTo(index, direction = 1) {
    if (index < 0 || index >= photoDatabase.length) return;

    activeLightboxIndex = index;
    const photo = photoDatabase[index];

    const imgNode = document.getElementById('lightboxImage');

    // Animation Slide transition
    imgNode.style.transform = `translateX(${direction * -40}px) scale(0.95)`;
    imgNode.style.opacity = '0';

    setTimeout(() => {
        imgNode.src = photo.url;
        document.getElementById('lightboxFilename').innerText = photo.name;

        updateLightboxControls(photo.id);
        buildLightboxThumbnails();

        imgNode.style.transform = 'translateX(0px) scale(1)';
        imgNode.style.opacity = '1';
    }, 180);
}

function lightboxDeleteCurrent() {
    if (activeLightboxIndex === null) return;
    const currentPhoto = photoDatabase[activeLightboxIndex];

    deletePhoto(currentPhoto.id);

    // If gallery becomes empty, close lightbox
    if (photoDatabase.length === 0) {
        closeLightbox();
    } else {
        // Shift indices safely
        if (activeLightboxIndex >= photoDatabase.length) {
            activeLightboxIndex = photoDatabase.length - 1;
        }
        jumpLightboxTo(activeLightboxIndex);
    }
}

// ==========================================
// SWIPE & TOUCH DRAG CONTROLLERS (LIQUID UI)
// ==========================================
function initSlideGestures() {
    // 1. Lightbox Track Swipe
    const lbTrack = document.getElementById('lightboxTrack');
    if (!lbTrack) return;
    let lbStartX = 0;
    let lbDiffX = 0;
    let lbIsDragging = false;

    lbTrack.addEventListener('pointerdown', (e) => {
        lbStartX = e.clientX;
        lbIsDragging = true;
        lbTrack.querySelector('img').classList.remove('transition-transform');
    });

    lbTrack.addEventListener('pointermove', (e) => {
        if (!lbIsDragging) return;
        lbDiffX = e.clientX - lbStartX;

        // Fluid translate drag response
        lbTrack.querySelector('img').style.transform = `translateX(${lbDiffX}px) rotate(${lbDiffX * 0.015}deg) scale(0.98)`;
    });

    const endLbDrag = (e) => {
        if (!lbIsDragging) return;
        lbIsDragging = false;

        const img = lbTrack.querySelector('img');
        img.classList.add('transition-transform');

        // Swipe validation threshold: 120 pixels
        if (Math.abs(lbDiffX) > 120) {
            if (lbDiffX > 0) {
                navigateLightbox(-1); // Swipe right -> prev
            } else {
                navigateLightbox(1);  // Swipe left -> next
            }
        } else {
            // Snap back
            img.style.transform = 'translateX(0px) rotate(0deg) scale(1)';
        }
        lbDiffX = 0;
    };

    lbTrack.addEventListener('pointerup', endLbDrag);
    lbTrack.addEventListener('pointerleave', endLbDrag);


    // 2. Dashboard Featured Carousel Swipe
    const carTrack = document.getElementById('carouselTrack');
    if (!carTrack) return;
    let carStartX = 0;
    let carDiffX = 0;
    let carIsDragging = false;
    let carStartTransformX = 0;

    carTrack.addEventListener('pointerdown', (e) => {
        stopAutoSlide();
        carStartX = e.clientX;
        carIsDragging = true;

        // Calculate current offset based on layout width and active index
        const width = carTrack.parentElement.clientWidth;
        carStartTransformX = -carouselCurrentIndex * width;

        carTrack.style.transition = 'none';
    });

    carTrack.addEventListener('pointermove', (e) => {
        if (!carIsDragging) return;
        carDiffX = e.clientX - carStartX;
        carTrack.style.transform = `translateX(${carStartTransformX + carDiffX}px)`;
    });

    const endCarDrag = (e) => {
        if (!carIsDragging) return;
        carIsDragging = false;

        carTrack.style.transition = 'transform 0.5s ease-out';

        const width = carTrack.parentElement.clientWidth;

        // Swipe boundary check
        if (Math.abs(carDiffX) > width * 0.2) {
            if (carDiffX > 0) {
                slideCarouselPrev();
            } else {
                slideCarouselNext();
            }
        } else {
            // Reset to snap
            carTrack.style.transform = `translateX(-${carouselCurrentIndex * 100}%)`;
        }

        carDiffX = 0;
        startAutoSlide();
    };

    carTrack.addEventListener('pointerup', endCarDrag);
    carTrack.addEventListener('pointerleave', endCarDrag);
}

function handleKeyboardShortcuts(e) {
    if (document.getElementById('lightboxModal').style.display !== 'flex') return;

    if (e.key === 'ArrowRight') {
        navigateLightbox(1);
    } else if (e.key === 'ArrowLeft') {
        navigateLightbox(-1);
    } else if (e.key === 'Escape') {
        closeLightbox();
    } else if (e.key === ' ') {
        e.preventDefault();
        if (activeLightboxIndex !== null) {
            const currentPhoto = photoDatabase[activeLightboxIndex];
            toggleSelection(currentPhoto.id);
        }
    }
}

// ==========================================
// ADVANCED TOAST NOTIFICATION ENGINE
// ==========================================
function showToast(type, title, message, actionText = '', actionCallback = null) {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = "toast-card glass-panel w-full p-4 rounded-2xl flex items-start gap-3 shadow-2xl pointer-events-auto relative overflow-hidden border border-white/10";

    // Build left border accent band based on notification type
    let indicatorBg = 'var(--theme-accent)';
    let iconMarkup = 'bell';

    if (type === 'success') {
        indicatorBg = '#10b981';
        iconMarkup = 'check-circle-2';
    } else if (type === 'alert') {
        indicatorBg = '#f43f5e';
        iconMarkup = 'alert-triangle';
    } else if (type === 'info') {
        indicatorBg = '#0ea5e9';
        iconMarkup = 'info';
    } else if (type === 'action') {
        indicatorBg = 'var(--theme-accent)';
        iconMarkup = 'history';
    }

    toast.innerHTML = `
        <!-- Color edge line -->
        <div class="absolute left-0 top-0 bottom-0 w-1.5" style="background-color: ${indicatorBg}"></div>
        
        <div class="p-1 rounded-lg bg-white/5 border border-white/10 shrink-0" style="color: ${indicatorBg}">
            <i data-lucide="${iconMarkup}" class="w-4 h-4"></i>
        </div>
        
        <div class="flex-grow text-left">
            <h4 class="text-xs font-bold text-white tracking-wide">${title}</h4>
            <p class="text-[11px] text-gray-400 mt-0.5 leading-relaxed">${message}</p>
            
            ${actionText ? `
                <button id="toastActionBtn" class="mt-2 text-[10px] font-extrabold uppercase tracking-wider text-[var(--theme-accent)] hover:underline flex items-center gap-1 transition-colors">
                    <span>${actionText}</span>
                    <i data-lucide="corner-down-left" class="w-3 h-3"></i>
                </button>
            ` : ''}
        </div>

        <button onclick="dismissToast(this.parentElement)" class="text-gray-500 hover:text-white shrink-0 p-0.5">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
        </button>

        <!-- Animating progress bar timer -->
        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-white/10">
            <div class="h-full" style="background-color: ${indicatorBg}; width: 100%; transition: width 4.5s linear;"></div>
        </div>
    `;

    container.appendChild(toast);
    lucide.createIcons();

    // Set Action Callback
    if (actionText && actionCallback) {
        const actionBtn = toast.querySelector('#toastActionBtn');
        actionBtn.addEventListener('click', () => {
            actionCallback();
            dismissToast(toast);
        });
    }

    // Start progress bar animation trigger
    setTimeout(() => {
        const progBar = toast.querySelector('.absolute.bottom-0 div');
        if (progBar) progBar.style.width = '0%';
    }, 50);

    // Auto-dismiss duration: 4.5 seconds
    const autoTimer = setTimeout(() => {
        dismissToast(toast);
    }, 4500);

    // Save timer reference on the element
    toast.dataset.timerId = autoTimer;
}

function dismissToast(toastNode) {
    if (!toastNode) return;

    // Clear auto timer if manually closed
    if (toastNode.dataset.timerId) {
        clearTimeout(toastNode.dataset.timerId);
    }

    toastNode.classList.add('removing');

    // Wait for slide animation
    setTimeout(() => {
        toastNode.remove();
    }, 300);
}

// ==========================================
// SUBMIT SELECTION & CONFETTI ENGINE
// ==========================================
function submitSelections() {
    if (selectedPhotoIds.size === 0) {
        showToast('alert', 'Zero Selections', 'Please check at least one photo first.');
        return;
    }

    const modal = document.getElementById('submitModal');
    const listContainer = document.getElementById('submitSelectedList');

    // Read chosen assets metadata
    const selectedListItems = photoDatabase.filter(p => selectedPhotoIds.has(p.id));

    listContainer.innerHTML = selectedListItems.map(photo => `
        <div class="flex items-center justify-between border-b border-white/5 pb-2.5 text-left">
            <div class="flex items-center gap-2.5">
                <img src="${photo.url}" class="w-8 h-8 rounded-md object-cover border border-white/10 shrink-0">
                <span class="text-xs font-bold text-gray-200 truncate max-w-[200px] sm:max-w-xs">${photo.name}</span>
            </div>
            <span class="px-2 py-0.5 rounded-full text-[9px] uppercase font-bold bg-white/5 border border-white/10 text-gray-400 capitalize">${photo.category}</span>
        </div>
    `).join('');

    modal.style.display = 'flex';
}

function closeSubmitModal() {
    document.getElementById('submitModal').style.display = 'none';
    stopConfetti();
}

function confirmSubmitChoice() {
    const selectedListItems = photoDatabase.filter(p => selectedPhotoIds.has(p.id));
    const selectionCount = selectedListItems.length;

    if (selectionCount === 0) return;

    // Close submit review modal
    document.getElementById('submitModal').style.display = 'none';

    // Show loading overlay
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.style.display = 'flex';

    // DOM References
    const globalBar = document.getElementById('globalUploadProgressBar');
    const globalText = document.getElementById('globalUploadProgressText');
    const currentItemBar = document.getElementById('currentItemProgressBar');
    const currentItemText = document.getElementById('currentItemName');
    const currentItemPercent = document.getElementById('currentItemPercent');
    const logContainer = document.getElementById('uploadLogContainer');

    // Reset states
    globalBar.style.width = '0%';
    globalText.innerText = '0%';
    currentItemBar.style.width = '0%';
    currentItemText.innerText = 'Establishing server connection...';
    currentItemPercent.innerText = '0%';
    logContainer.innerHTML = '';

    let currentItemIndex = 0;

    // Step-by-step sequential progressive simulation
    function uploadNextItem() {
        if (currentItemIndex >= selectionCount) {
            // Upload completes
            setTimeout(() => {
                // Hide loading overlay
                loadingOverlay.style.display = 'none';

                // Show success overlay
                const successOverlay = document.getElementById('successOverlay');
                document.getElementById('successPhotoCount').innerText = selectionCount;
                successOverlay.style.display = 'flex';

                // Trigger success confetti
                triggerConfettiBurst(document.getElementById('successConfetti'));

                // Clear selection stack
                selectedPhotoIds.clear();
                saveSelectionsToCache();
                refreshGallery();

                // Close lightbox if it was open
                closeLightbox();

                // Make sure we update dashboard tab to select gallery workspace
                switchTab('gallery');

                lucide.createIcons();
            }, 800);
            return;
        }

        const photo = selectedListItems[currentItemIndex];
        currentItemText.innerText = `Uploading ${photo.name} (${currentItemIndex + 1}/${selectionCount})`;
        currentItemBar.style.width = '0%';
        currentItemPercent.innerText = '0%';

        // Append activity log
        const logItem = document.createElement('div');
        logItem.id = `logItem-${photo.id}`;
        logItem.className = 'flex items-center justify-between text-gray-400 py-1 transition-all duration-300';
        logItem.innerHTML = `
            <div class="flex items-center gap-2">
                <i data-lucide="loader" class="w-3.5 h-3.5 text-[var(--theme-accent)] animate-spin"></i>
                <span class="truncate max-w-[180px] sm:max-w-xs font-semibold text-gray-300">${photo.name}</span>
            </div>
            <span class="text-[10px] text-gray-500 font-semibold italic">Uploading...</span>
        `;
        logContainer.appendChild(logItem);
        logContainer.scrollTop = logContainer.scrollHeight;
        lucide.createIcons();

        let progress = 0;
        // Simulating uploading segments of the current picture
        const interval = setInterval(() => {
            progress += 10;
            currentItemBar.style.width = `${progress}%`;
            currentItemPercent.innerText = `${progress}%`;

            // Update global progress bar
            const globalProgress = Math.round(
                ((currentItemIndex * 100) + progress) / selectionCount
            );
            globalBar.style.width = `${globalProgress}%`;
            globalText.innerText = `${globalProgress}%`;

            if (progress >= 100) {
                clearInterval(interval);

                // Mark log item as successfully sent
                logItem.className = 'flex items-center justify-between text-emerald-400 py-1 transition-all duration-300 font-semibold';
                logItem.innerHTML = `
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-500"></i>
                        <span class="truncate max-w-[180px] sm:max-w-xs text-gray-200">${photo.name}</span>
                    </div>
                    <span class="text-[10px] text-emerald-500 font-bold uppercase tracking-wider">Dispatched</span>
                `;
                lucide.createIcons();

                currentItemIndex++;
                setTimeout(uploadNextItem, 250); // 250ms pause between images
            }
        }, 80); // 80ms chunks -> ~800ms per image
    }

    // Start simulated sequence after server handshake delay
    setTimeout(uploadNextItem, 1000);
}

function closeSuccessOverlay() {
    document.getElementById('successOverlay').style.display = 'none';
    stopConfetti();
}

// Fun Confetti particles
let confettiInterval = null;
function triggerConfettiBurst(container) {
    if (!container) container = document.getElementById('confettiContainer');
    const colors = ['#3b82f6', '#10b981', '#f43f5e', '#f59e0b', '#a855f7'];

    confettiInterval = setInterval(() => {
        const conf = document.createElement('div');
        conf.className = 'confetti';

        // Random position & color styling
        conf.style.left = `${Math.random() * 100}%`;
        conf.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        conf.style.transform = `rotate(${Math.random() * 360}deg)`;

        // Randomize scale
        const scale = Math.random() * 0.7 + 0.5;
        conf.style.width = `${8 * scale}px`;
        conf.style.height = `${8 * scale}px`;

        container.appendChild(conf);

        setTimeout(() => {
            conf.remove();
        }, 3000);
    }, 100);
}

function stopConfetti() {
    if (confettiInterval) clearInterval(confettiInterval);
}

// --- CUSTOM HOVER CURSOR CONTROLLER ---
const customCursor = document.getElementById('customCursor');

document.addEventListener('mousemove', (e) => {
    if (!customCursor) return;
    customCursor.style.left = `${e.clientX}px`;
    customCursor.style.top = `${e.clientY}px`;

    const zoomTrigger = e.target.closest('.photo-zoom-trigger');

    if (zoomTrigger) {
        const onButton = e.target.closest('.custom-cursor-hide');
        if (onButton) {
            customCursor.classList.remove('opacity-100', 'scale-100');
            customCursor.classList.add('opacity-0', 'scale-50');
        } else {
            customCursor.classList.remove('opacity-0', 'scale-50');
            customCursor.classList.add('opacity-100', 'scale-100');
        }
    } else {
        customCursor.classList.remove('opacity-100', 'scale-100');
        customCursor.classList.add('opacity-0', 'scale-50');
    }
});


// ==========================================
// FULL-FLEDGED MANAGEMENT DASHBOARD ENGINE
// ==========================================

// --- Client Management Data Store ---
let clientDatabase = JSON.parse(localStorage.getItem('obm_client_db') || '[]');
let activeDashTab = 'overview';
let activeStatusFilter = 'all';
let uploadedFilesQueue = [];

// Seed with demo clients if empty
if (clientDatabase.length === 0) {
    clientDatabase = [
        { id: 'c1', name: 'Priya Sharma', email: 'priya@example.com', status: 'completed', blocked: false, selectedIds: [1, 3, 5], totalAllocated: 8, sentAt: '2026-07-15T10:30:00', flagged: true },
        { id: 'c2', name: 'Arun Kumar', email: 'arun@example.com', status: 'pending', blocked: false, selectedIds: [], totalAllocated: 8, sentAt: '2026-07-18T14:00:00', flagged: false },
        { id: 'c3', name: 'Meera Nair', email: 'meera@example.com', status: 'pending', blocked: true, selectedIds: [2, 7], totalAllocated: 8, sentAt: '2026-07-10T09:00:00', flagged: false },
    ];
    saveClientDB();
}

function saveClientDB() {
    localStorage.setItem('obm_client_db', JSON.stringify(clientDatabase));
}

function generateClientId() {
    return 'c' + Date.now() + Math.random().toString(36).substr(2, 4);
}

// --- Dashboard Sub-Tab Switching ---
function switchDashTab(tabId) {
    activeDashTab = tabId;
    const tabIds = ['overview', 'deleted', 'clients', 'upload', 'status'];

    tabIds.forEach(id => {
        const btn = document.getElementById(`dashTab-${id}`);
        const panel = document.getElementById(`dashPanel-${id}`);
        if (btn) {
            btn.className = `shrink-0 py-2 px-4 rounded-xl text-[11px] font-bold transition-all duration-300 flex items-center gap-1.5 active:scale-95 ${id === tabId ? 'bg-[var(--theme-accent)] text-black' : 'text-gray-400 hover:text-white'}`;
        }
        if (panel) {
            panel.classList.toggle('hidden', id !== tabId);
        }
    });

    // Refresh the active panel's data
    if (tabId === 'overview') refreshOverviewPanel();
    else if (tabId === 'deleted') refreshDeletedDetection();
    else if (tabId === 'clients') refreshClientManager();
    else if (tabId === 'upload') refreshUploadPanel();
    else if (tabId === 'status') refreshSelectionStatus();

    lucide.createIcons();
}

// --- TAB 1: OVERVIEW PANEL ---
function refreshOverviewPanel() {
    const total = photoDatabase.length;
    const selected = selectedPhotoIds.size;
    const deleted = deletedPhotosHistory.length;

    const el = (id) => document.getElementById(id);

    if (el('dashTotalPhotos')) el('dashTotalPhotos').innerText = total;
    if (el('dashSelectedPhotos')) el('dashSelectedPhotos').innerText = selected;
    if (el('dashDeletedPhotosCount')) el('dashDeletedPhotosCount').innerText = deleted;
    if (el('dashClientCount')) el('dashClientCount').innerText = clientDatabase.length;

    const ratio = total > 0 ? Math.round((selected / total) * 100) : 0;
    if (el('dashSelectionRatio')) el('dashSelectionRatio').innerText = `${ratio}%`;
    if (el('dashRatioBar')) el('dashRatioBar').style.width = `${ratio}%`;

    // Category breakdown
    const categories = ['candid', 'portrait', 'traditional', 'uploads'];
    const breakdownContainer = el('dashCategoryBreakdown');
    if (breakdownContainer) {
        breakdownContainer.innerHTML = categories.map(cat => {
            const totalCat = photoDatabase.filter(p => p.category === cat).length;
            const selectedCat = photoDatabase.filter(p => p.category === cat && selectedPhotoIds.has(p.id)).length;
            const catRatio = totalCat > 0 ? Math.round((selectedCat / totalCat) * 100) : 0;

            let colorClass = 'bg-[var(--theme-accent)]';
            if (cat === 'candid') colorClass = 'bg-sky-500';
            else if (cat === 'portrait') colorClass = 'bg-purple-500';
            else if (cat === 'traditional') colorClass = 'bg-emerald-500';
            else if (cat === 'uploads') colorClass = 'bg-amber-500';

            return `
                <div class="space-y-1">
                    <div class="flex justify-between text-[10px] text-gray-400 font-medium px-0.5">
                        <span class="capitalize">${cat === 'uploads' ? 'Custom Uploads' : cat}</span>
                        <span>${selectedCat} / ${totalCat} (${catRatio}%)</span>
                    </div>
                    <div class="w-full h-1 bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full ${colorClass} rounded-full" style="width: ${catRatio}%;"></div>
                    </div>
                </div>
            `;
        }).join('');
    }

    // JSON export
    const jsonContainer = el('dashSelectedIdsJson');
    if (jsonContainer) jsonContainer.innerText = JSON.stringify(Array.from(selectedPhotoIds));
}

// --- TAB 2: DELETED DETECTION ---
function refreshDeletedDetection() {
    const el = (id) => document.getElementById(id);

    // Selected photos (approved)
    const selectedPhotos = photoDatabase.filter(p => selectedPhotoIds.has(p.id));
    const unselectedPhotos = photoDatabase.filter(p => !selectedPhotoIds.has(p.id));

    if (el('detectSelectedCount')) el('detectSelectedCount').innerText = selectedPhotos.length;
    if (el('detectDeletedCount')) el('detectDeletedCount').innerText = unselectedPhotos.length;

    const selectedGrid = el('detectSelectedGrid');
    if (selectedGrid) {
        if (selectedPhotos.length === 0) {
            selectedGrid.innerHTML = `<div class="text-center py-6 text-gray-500"><p class="text-[11px]">No photos selected yet.</p></div>`;
        } else {
            selectedGrid.innerHTML = selectedPhotos.map(photo => `
                <div class="flex items-center gap-3 bg-emerald-500/5 border border-emerald-500/10 rounded-xl p-2.5 hover:bg-emerald-500/10 transition-colors">
                    <img src="${photo.url}" class="w-9 h-10 rounded-lg object-cover border border-emerald-500/20 shrink-0">
                    <div class="flex-grow min-w-0">
                        <h6 class="text-[11px] font-bold text-gray-200 truncate">${photo.name}</h6>
                        <span class="text-[8px] uppercase font-bold text-emerald-400">${photo.category}</span>
                    </div>
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                </div>
            `).join('');
        }
    }

    const deletedGrid = el('detectDeletedGrid');
    if (deletedGrid) {
        if (unselectedPhotos.length === 0) {
            deletedGrid.innerHTML = `<div class="text-center py-6 text-gray-500"><p class="text-[11px]">All photos are selected! 🎉</p></div>`;
        } else {
            deletedGrid.innerHTML = unselectedPhotos.map(photo => `
                <div class="flex items-center gap-3 bg-red-500/5 border border-red-500/10 rounded-xl p-2.5 hover:bg-red-500/10 transition-colors">
                    <img src="${photo.url}" class="w-9 h-10 rounded-lg object-cover border border-red-500/20 shrink-0">
                    <div class="flex-grow min-w-0">
                        <h6 class="text-[11px] font-bold text-gray-200 truncate">${photo.name}</h6>
                        <span class="text-[8px] uppercase font-bold text-red-400">${photo.category}</span>
                    </div>
                    <i data-lucide="x-circle" class="w-4 h-4 text-red-400 shrink-0"></i>
                </div>
            `).join('');
        }
    }

    // Permanently removed registry (from deletedPhotosHistory)
    const deleted = deletedPhotosHistory.length;
    if (el('dashDeletedCount')) el('dashDeletedCount').innerText = deleted;

    const registry = el('dashDeletedRegistry');
    if (registry) {
        if (deleted === 0) {
            registry.innerHTML = `
                <div class="text-center py-8 text-gray-500 flex flex-col items-center justify-center p-4">
                    <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500/20 mb-2 border border-emerald-500/10 p-1.5 rounded-full bg-emerald-500/5"></i>
                    <p class="text-xs font-semibold text-gray-300">Workspace fully intact</p>
                    <p class="text-[10px] text-gray-500 mt-0.5">No permanently removed assets.</p>
                </div>
            `;
        } else {
            registry.innerHTML = deletedPhotosHistory.map((item, idx) => {
                const photo = item.photo;
                return `
                    <div class="flex items-center justify-between bg-white/5 border border-white/5 rounded-xl p-3.5 hover:bg-white/10 transition-colors">
                        <div class="flex items-center gap-3">
                            <img src="${photo.url}" class="w-10 h-12 rounded-lg object-cover border border-white/10 shrink-0">
                            <div class="text-left">
                                <h5 class="text-xs font-bold text-gray-200 truncate max-w-[150px] sm:max-w-xs">${photo.name}</h5>
                                <span class="px-1.5 py-0.5 rounded text-[8px] uppercase font-bold bg-white/5 border border-white/10 text-gray-400 capitalize">${photo.category}</span>
                            </div>
                        </div>
                        <button onclick="restoreFromDashboard(${idx})" class="px-3 py-1.5 rounded-lg text-[10px] font-bold bg-[var(--theme-accent)] hover:opacity-90 active:scale-95 text-black flex items-center gap-1 transition-all custom-cursor-hide">
                            <i data-lucide="rotate-ccw" class="w-3 h-3 text-black"></i>
                            Restore
                        </button>
                    </div>
                `;
            }).join('');
        }
    }
}

// --- TAB 3: CLIENT MANAGER ---
function refreshClientManager() {
    const container = document.getElementById('clientManagerList');
    if (!container) return;

    if (clientDatabase.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 text-gray-500 flex flex-col items-center">
                <i data-lucide="user-x" class="w-10 h-10 text-gray-600 mb-3"></i>
                <p class="text-sm font-semibold text-gray-300">No Clients Registered</p>
                <p class="text-[11px] text-gray-500 mt-1">Use the form above to add your first client.</p>
            </div>
        `;
        return;
    }

    container.innerHTML = clientDatabase.map((client, idx) => {
        const statusColor = client.blocked ? 'red' : (client.status === 'completed' ? 'emerald' : 'amber');
        const statusLabel = client.blocked ? 'BLOCKED' : (client.status === 'completed' ? 'COMPLETED' : 'PENDING');
        const flagIcon = client.flagged ? 'flag' : 'flag-off';
        const flagColor = client.flagged ? 'text-amber-400' : 'text-gray-500';
        const selectedCount = client.selectedIds ? client.selectedIds.length : 0;
        const sentDate = client.sentAt ? new Date(client.sentAt).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' }) : 'N/A';

        return `
            <div class="glass-panel rounded-2xl p-4 border border-white/10 hover:border-white/20 transition-all ${client.blocked ? 'opacity-60' : ''}">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <!-- Client Info -->
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500/30 to-sky-500/30 border border-white/10 flex items-center justify-center text-white text-sm font-bold shrink-0">
                            ${client.name.charAt(0).toUpperCase()}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <h5 class="text-sm font-bold text-white truncate">${client.name}</h5>
                                <span class="px-1.5 py-0.5 rounded text-[8px] uppercase font-bold bg-${statusColor}-500/10 text-${statusColor}-400 border border-${statusColor}-500/20">${statusLabel}</span>
                                ${client.flagged ? '<span class="px-1.5 py-0.5 rounded text-[8px] uppercase font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">FLAGGED</span>' : ''}
                            </div>
                            <p class="text-[10px] text-gray-400 truncate">${client.email}</p>
                            <div class="flex items-center gap-3 mt-1 text-[9px] text-gray-500">
                                <span class="flex items-center gap-1"><i data-lucide="image" class="w-3 h-3"></i> ${client.totalAllocated} allocated</span>
                                <span class="flex items-center gap-1"><i data-lucide="heart" class="w-3 h-3"></i> ${selectedCount} selected</span>
                                <span class="flex items-center gap-1"><i data-lucide="calendar" class="w-3 h-3"></i> ${sentDate}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button onclick="toggleFlagClient(${idx})" title="${client.flagged ? 'Remove Flag' : 'Flag as Sent'}" class="p-2 rounded-lg border border-white/10 bg-white/5 hover:bg-white/10 transition-all active:scale-90 ${flagColor}">
                            <i data-lucide="${flagIcon}" class="w-3.5 h-3.5"></i>
                        </button>
                        <button onclick="toggleBlockClient(${idx})" title="${client.blocked ? 'Unblock' : 'Block'}" class="p-2 rounded-lg border border-white/10 bg-white/5 hover:bg-white/10 transition-all active:scale-90 ${client.blocked ? 'text-emerald-400' : 'text-red-400'}">
                            <i data-lucide="${client.blocked ? 'shield-check' : 'shield-ban'}" class="w-3.5 h-3.5"></i>
                        </button>
                        <button onclick="removeClient(${idx})" title="Remove Client" class="p-2 rounded-lg border border-red-500/20 bg-red-500/5 hover:bg-red-500/10 transition-all active:scale-90 text-red-400">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

                ${client.flagged ? `
                    <div class="mt-3 px-3 py-2 bg-amber-500/5 border border-amber-500/10 rounded-xl text-[10px] text-amber-300 flex items-center gap-2">
                        <i data-lucide="info" class="w-3.5 h-3.5 shrink-0"></i>
                        This client has been flagged as already selected/sent. They will see a "Thank you" screen instead of gallery access.
                    </div>
                ` : ''}
            </div>
        `;
    }).join('');

    lucide.createIcons();
}

function addClientManual() {
    const nameInput = document.getElementById('addClientName');
    const emailInput = document.getElementById('addClientEmail');
    if (!nameInput || !emailInput) return;

    const name = nameInput.value.trim();
    const email = emailInput.value.trim();

    if (!name || !email) {
        showToast('alert', 'Missing Fields', 'Please enter both name and email.');
        return;
    }

    // Check duplicate email
    if (clientDatabase.find(c => c.email.toLowerCase() === email.toLowerCase())) {
        showToast('alert', 'Duplicate Client', 'A client with this email already exists.');
        return;
    }

    clientDatabase.push({
        id: generateClientId(),
        name: name,
        email: email,
        status: 'pending',
        blocked: false,
        selectedIds: [],
        totalAllocated: photoDatabase.length,
        sentAt: new Date().toISOString(),
        flagged: false
    });

    saveClientDB();
    nameInput.value = '';
    emailInput.value = '';
    refreshClientManager();
    refreshOverviewPanel();
    showToast('success', 'Client Registered', `${name} has been added to the directory.`);
}

function toggleBlockClient(idx) {
    if (idx < 0 || idx >= clientDatabase.length) return;
    clientDatabase[idx].blocked = !clientDatabase[idx].blocked;
    const isBlocked = clientDatabase[idx].blocked;
    saveClientDB();
    refreshClientManager();
    showToast(isBlocked ? 'alert' : 'success', isBlocked ? 'Client Blocked' : 'Client Unblocked', `${clientDatabase[idx].name}'s portal access has been ${isBlocked ? 'revoked' : 'restored'}.`);
}

function toggleFlagClient(idx) {
    if (idx < 0 || idx >= clientDatabase.length) return;
    clientDatabase[idx].flagged = !clientDatabase[idx].flagged;
    if (clientDatabase[idx].flagged) {
        clientDatabase[idx].status = 'completed';
    }
    const isFlagged = clientDatabase[idx].flagged;
    saveClientDB();
    refreshClientManager();
    showToast(isFlagged ? 'info' : 'success', isFlagged ? 'Client Flagged' : 'Flag Removed', `${clientDatabase[idx].name} ${isFlagged ? 'marked as sent/completed' : 'un-flagged'}.`);
}

function removeClient(idx) {
    if (idx < 0 || idx >= clientDatabase.length) return;
    const name = clientDatabase[idx].name;
    clientDatabase.splice(idx, 1);
    saveClientDB();
    refreshClientManager();
    refreshOverviewPanel();
    showToast('alert', 'Client Removed', `${name} has been permanently removed.`);
}

// --- TAB 4: UPLOAD & SEND ---
function refreshUploadPanel() {
    // Populate client dropdown
    const select = document.getElementById('uploadTargetClient');
    if (select) {
        const currentVal = select.value;
        select.innerHTML = '<option value="">-- Select Client --</option>';
        clientDatabase.filter(c => !c.blocked).forEach(client => {
            const opt = document.createElement('option');
            opt.value = client.email;
            opt.textContent = `${client.name} (${client.email})`;
            select.appendChild(opt);
        });
        select.value = currentVal;
    }

    // Setup upload zone events
    const dropzone = document.getElementById('dashUploadDropzone');
    const fileInput = document.getElementById('dashFileUploadInput');

    if (dropzone && !dropzone.dataset.initialized) {
        dropzone.dataset.initialized = 'true';

        dropzone.addEventListener('click', () => fileInput.click());

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-[var(--theme-accent)]', 'bg-[var(--theme-accent)]/10');
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-[var(--theme-accent)]', 'bg-[var(--theme-accent)]/10');
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-[var(--theme-accent)]', 'bg-[var(--theme-accent)]/10');
            handleUploadFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener('change', (e) => {
            handleUploadFiles(e.target.files);
        });
    }
}

function handleUploadFiles(files) {
    if (!files || files.length === 0) return;
    if (files.length > 20) {
        showToast('alert', 'Too Many Files', 'Maximum 20 files per upload.');
        return;
    }

    uploadedFilesQueue = Array.from(files);
    const queueContainer = document.getElementById('dashUploadQueue');
    const queueList = document.getElementById('dashQueueList');
    const queueCount = document.getElementById('dashQueueCount');

    if (queueContainer) queueContainer.classList.remove('hidden');
    if (queueCount) queueCount.innerText = `${uploadedFilesQueue.length} files`;

    if (queueList) {
        queueList.innerHTML = uploadedFilesQueue.map((file, i) => `
            <div class="flex items-center gap-2 bg-white/5 border border-white/5 rounded-lg px-3 py-2 hover:bg-white/10 transition-colors">
                <i data-lucide="image" class="w-3.5 h-3.5 text-[var(--theme-accent)] shrink-0"></i>
                <span class="text-[11px] text-gray-300 truncate flex-grow">${file.name}</span>
                <span class="text-[9px] text-gray-500 shrink-0">${(file.size / 1024).toFixed(0)} KB</span>
                <button onclick="removeQueuedFile(${i})" class="text-red-400 hover:text-red-300 transition-colors">
                    <i data-lucide="x" class="w-3 h-3"></i>
                </button>
            </div>
        `).join('');
    }

    lucide.createIcons();
}

function removeQueuedFile(idx) {
    uploadedFilesQueue.splice(idx, 1);
    if (uploadedFilesQueue.length === 0) {
        const queueContainer = document.getElementById('dashUploadQueue');
        if (queueContainer) queueContainer.classList.add('hidden');
    }
    handleUploadFiles(uploadedFilesQueue);
    // Re-trigger display with reduced queue
    const queueList = document.getElementById('dashQueueList');
    const queueCount = document.getElementById('dashQueueCount');
    if (queueCount) queueCount.innerText = `${uploadedFilesQueue.length} files`;
    if (uploadedFilesQueue.length === 0 && queueList) {
        queueList.innerHTML = '';
        document.getElementById('dashUploadQueue')?.classList.add('hidden');
    }
}

function dispatchToClient() {
    const targetEmail = document.getElementById('uploadTargetClient')?.value;

    if (!targetEmail) {
        showToast('alert', 'No Client Selected', 'Choose a target client before dispatching.');
        return;
    }

    if (uploadedFilesQueue.length === 0) {
        showToast('alert', 'No Files Queued', 'Upload at least one image to dispatch.');
        return;
    }

    const client = clientDatabase.find(c => c.email === targetEmail);
    if (!client) {
        showToast('alert', 'Client Not Found', 'Invalid target client.');
        return;
    }

    // Simulate adding photos to client's allocation
    const newPhotoCount = uploadedFilesQueue.length;
    uploadedFilesQueue.forEach((file, i) => {
        const newId = Date.now() + i;
        const objectUrl = URL.createObjectURL(file);
        photoDatabase.push({
            id: newId,
            name: file.name,
            category: 'uploads',
            url: objectUrl
        });
    });

    client.totalAllocated += newPhotoCount;
    saveClientDB();

    // Clear queue
    uploadedFilesQueue = [];
    const queueContainer = document.getElementById('dashUploadQueue');
    if (queueContainer) queueContainer.classList.add('hidden');
    document.getElementById('dashQueueList').innerHTML = '';

    refreshGallery();
    initCarousel();
    showToast('success', 'Photos Dispatched!', `${newPhotoCount} images allocated to ${client.name}'s portal.`);
}

// --- TAB 5: SELECTION STATUS TRACKER ---
function refreshSelectionStatus() {
    const container = document.getElementById('selectionStatusGrid');
    if (!container) return;

    let filtered = clientDatabase;
    if (activeStatusFilter === 'pending') filtered = clientDatabase.filter(c => c.status === 'pending' && !c.blocked);
    else if (activeStatusFilter === 'completed') filtered = clientDatabase.filter(c => c.status === 'completed' || c.flagged);
    else if (activeStatusFilter === 'blocked') filtered = clientDatabase.filter(c => c.blocked);

    if (filtered.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 text-gray-500 flex flex-col items-center">
                <i data-lucide="inbox" class="w-10 h-10 text-gray-600 mb-3"></i>
                <p class="text-sm font-semibold text-gray-300">No Clients Match This Filter</p>
                <p class="text-[11px] text-gray-500 mt-1">Try switching filter or add new clients.</p>
            </div>
        `;
        lucide.createIcons();
        return;
    }

    container.innerHTML = filtered.map((client) => {
        const selectedCount = client.selectedIds ? client.selectedIds.length : 0;
        const totalAllocated = client.totalAllocated || photoDatabase.length;
        const selectRatio = totalAllocated > 0 ? Math.round((selectedCount / totalAllocated) * 100) : 0;

        let statusBadge = '';
        let borderColor = 'border-white/10';
        let progressColor = 'bg-amber-500';

        if (client.blocked) {
            statusBadge = `<span class="px-2 py-0.5 rounded-full text-[8px] font-bold uppercase bg-red-500/10 text-red-400 border border-red-500/20 flex items-center gap-1"><i data-lucide="shield-ban" class="w-2.5 h-2.5"></i> Blocked</span>`;
            borderColor = 'border-red-500/20';
            progressColor = 'bg-red-500';
        } else if (client.flagged || client.status === 'completed') {
            statusBadge = `<span class="px-2 py-0.5 rounded-full text-[8px] font-bold uppercase bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center gap-1"><i data-lucide="check-circle-2" class="w-2.5 h-2.5"></i> Completed</span>`;
            borderColor = 'border-emerald-500/20';
            progressColor = 'bg-emerald-500';
        } else {
            statusBadge = `<span class="px-2 py-0.5 rounded-full text-[8px] font-bold uppercase bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center gap-1"><i data-lucide="clock" class="w-2.5 h-2.5"></i> Pending</span>`;
        }

        // Show selected photo thumbnails (map IDs to photoDatabase)
        const selectedThumbs = (client.selectedIds || []).slice(0, 6).map(pid => {
            const photo = photoDatabase.find(p => p.id === pid);
            if (!photo) return '';
            return `<img src="${photo.url}" class="w-8 h-8 rounded-lg object-cover border border-white/10" title="${photo.name}">`;
        }).filter(Boolean).join('');

        const moreCount = selectedCount > 6 ? `<span class="w-8 h-8 rounded-lg bg-white/10 border border-white/10 flex items-center justify-center text-[9px] font-bold text-gray-300">+${selectedCount - 6}</span>` : '';

        return `
            <div class="glass-panel rounded-2xl p-5 border ${borderColor} hover:border-white/20 transition-all space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-500/30 to-sky-500/30 border border-white/10 flex items-center justify-center text-white text-sm font-bold shrink-0">
                            ${client.name.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <h5 class="text-sm font-bold text-white">${client.name}</h5>
                            <p class="text-[10px] text-gray-400">${client.email}</p>
                        </div>
                    </div>
                    ${statusBadge}
                </div>

                <!-- Progress Bar -->
                <div class="space-y-1">
                    <div class="flex justify-between text-[10px] text-gray-400 font-bold">
                        <span>Selection Progress</span>
                        <span>${selectedCount} / ${totalAllocated} photos (${selectRatio}%)</span>
                    </div>
                    <div class="w-full h-1.5 bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full ${progressColor} rounded-full transition-all duration-500" style="width: ${selectRatio}%;"></div>
                    </div>
                </div>

                ${selectedCount > 0 ? `
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[9px] text-gray-500 font-bold uppercase tracking-wider mr-1">Selected:</span>
                        ${selectedThumbs}
                        ${moreCount}
                    </div>
                ` : `
                    <div class="text-[10px] text-gray-500 italic flex items-center gap-1.5">
                        <i data-lucide="image-off" class="w-3 h-3"></i> No selections made yet
                    </div>
                `}

                ${client.flagged ? `
                    <div class="px-3 py-2 bg-amber-500/5 border border-amber-500/10 rounded-xl text-[10px] text-amber-300 flex items-center gap-2">
                        <i data-lucide="eye-off" class="w-3.5 h-3.5 shrink-0"></i>
                        Client sees "Thank You" page instead of gallery — photos returned to studio.
                    </div>
                ` : ''}
            </div>
        `;
    }).join('');

    lucide.createIcons();
}

function filterStatusView(filter) {
    activeStatusFilter = filter;
    const filters = ['all', 'pending', 'completed', 'blocked'];
    filters.forEach(f => {
        const btn = document.getElementById(`statusFilter-${f}`);
        if (btn) {
            btn.className = `px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all active:scale-95 ${f === filter ? 'bg-[var(--theme-accent)] text-black' : 'bg-white/5 text-gray-400 border border-white/10 hover:text-white'}`;
        }
    });
    refreshSelectionStatus();
}

// --- UNIFIED refreshDashboard (backward compat) ---
function refreshDashboard() {
    refreshOverviewPanel();
    // Also refresh the active sub-tab
    if (activeDashTab === 'deleted') refreshDeletedDetection();
    else if (activeDashTab === 'clients') refreshClientManager();
    else if (activeDashTab === 'upload') refreshUploadPanel();
    else if (activeDashTab === 'status') refreshSelectionStatus();
}

// --- MAIN TAB SWITCH (Gallery ↔ Dashboard) ---
function switchTab(tabId) {
    activeTab = tabId;
    const tabBtnGallery = document.getElementById('tabBtn-gallery');
    const tabBtnDashboard = document.getElementById('tabBtn-dashboard');

    const carouselSection = document.getElementById('carouselSection');
    const filtersSection = document.getElementById('filtersSection');
    const actionToolbar = document.getElementById('actionToolbar');
    const gridSection = document.getElementById('gridSection');
    const dashboardSection = document.getElementById('dashboardSection');

    if (!tabBtnGallery || !tabBtnDashboard) return;

    // Reset tab styling
    tabBtnGallery.className = "flex-grow sm:flex-initial py-2.5 px-5 rounded-xl text-xs font-bold transition-all duration-300 flex items-center justify-center gap-1.5 active:scale-95 text-gray-400 hover:text-white";
    tabBtnDashboard.className = "flex-grow sm:flex-initial py-2.5 px-5 rounded-xl text-xs font-bold transition-all duration-300 flex items-center justify-center gap-1.5 active:scale-95 text-gray-400 hover:text-white";

    if (tabId === 'gallery') {
        tabBtnGallery.className = "flex-grow sm:flex-initial py-2.5 px-5 rounded-xl text-xs font-bold transition-all duration-300 flex items-center justify-center gap-1.5 active:scale-95 bg-[var(--theme-accent)] text-black shadow-lg";
        
        if (carouselSection) carouselSection.classList.remove('hidden');
        if (filtersSection) filtersSection.classList.remove('hidden');
        if (gridSection) gridSection.classList.remove('hidden');
        if (dashboardSection) dashboardSection.classList.add('hidden');
        
        updateActionToolbar();
    } else {
        tabBtnDashboard.className = "flex-grow sm:flex-initial py-2.5 px-5 rounded-xl text-xs font-bold transition-all duration-300 flex items-center justify-center gap-1.5 active:scale-95 bg-[var(--theme-accent)] text-black shadow-lg";
        
        if (carouselSection) carouselSection.classList.add('hidden');
        if (filtersSection) filtersSection.classList.add('hidden');
        if (gridSection) gridSection.classList.add('hidden');
        if (actionToolbar) {
            actionToolbar.classList.add('hidden');
            actionToolbar.classList.remove('flex');
        }
        if (dashboardSection) dashboardSection.classList.remove('hidden');

        // Refresh all dashboard data
        refreshDashboard();
    }
    lucide.createIcons();
}

function restoreFromDashboard(historyIndex) {
    if (historyIndex < 0 || historyIndex >= deletedPhotosHistory.length) return;

    const restoredPayload = deletedPhotosHistory.splice(historyIndex, 1)[0];
    const photo = restoredPayload.photo;
    const originalIndex = restoredPayload.originalIndex;

    photoDatabase.splice(originalIndex, 0, photo);

    if (restoredPayload.wasSelected) {
        selectedPhotoIds.add(photo.id);
        saveSelectionsToCache();
    }

    refreshGallery();
    initCarousel();
    refreshDashboard();

    showToast('success', 'Asset Restored', `Recovered: ${photo.name}`);
}

function copySelectionsList() {
    const selectedListItems = photoDatabase.filter(p => selectedPhotoIds.has(p.id));
    if (selectedListItems.length === 0) {
        showToast('alert', 'Copy Failed', 'Select at least one photo first.');
        return;
    }

    const namesList = selectedListItems.map(p => p.name).join('\n');
    navigator.clipboard.writeText(namesList).then(() => {
        showToast('success', 'List Copied to Clipboard', `Copied ${selectedListItems.length} filenames.`);
    }).catch(err => {
        console.error("Clipboard copy error", err);
        showToast('alert', 'Copy Blocked', 'Could not access clipboard.');
    });
}
