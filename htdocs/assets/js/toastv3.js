/**
 * toastv3.js — Universal Standalone Toast Notification System
 * ============================================================
 * Creates animated, auto-dismissing toast messages matching styles.css.
 * Pure Vanilla JavaScript implementation (Zero external dependencies).
 */

function showToast(type, title, message, duration) {
    if (!duration) duration = 4000;

    var knownTypes = ['success', 'error', 'warning', 'info', 'help', 'sapphire', 'gold', 'purple', 'alert', 'action'];
    
    // Smart parameter format detector for backward compatibility:
    // If the first argument is not a known type (e.g. it is a custom title), swap parameters.
    if (typeof type === 'string' && knownTypes.indexOf(type.toLowerCase()) === -1) {
        var actualType = (typeof message === 'string' && knownTypes.indexOf(message.toLowerCase()) !== -1) ? message : 'info';
        var actualTitle = type;
        var actualMessage = title;
        
        type = actualType;
        title = actualTitle;
        message = actualMessage;
    }

    var lowerType = type ? type.toLowerCase() : 'info';
    if (lowerType === 'help') lowerType = 'sapphire';

    var container = document.getElementById('toast-container');
    if (!container) {
        // Auto-create toast container if not found in DOM
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container toast-pos-bottom-right';
        document.body.appendChild(container);
    }

    // Map variant type to appropriate Lucide icon name
    var iconName = 'bell';
    if (lowerType === 'success') iconName = 'check-circle-2';
    else if (lowerType === 'error' || lowerType === 'alert') iconName = 'alert-triangle';
    else if (lowerType === 'warning') iconName = 'alert-circle';
    else if (lowerType === 'sapphire' || lowerType === 'info') iconName = 'info';
    else if (lowerType === 'gold') iconName = 'star';
    else if (lowerType === 'purple') iconName = 'message-square';

    // Generate toast element using Vanilla DOM
    var item = document.createElement('div');
    item.className = 'toast-item toast-theme-' + lowerType;
    
    item.innerHTML = 
        '<div class="toast-body">' +
        '  <div class="toast-icon-box">' +
        '    <i data-lucide="' + iconName + '" class="w-5 h-5"></i>' +
        '  </div>' +
        '  <div class="toast-content">' +
        '    <h4 class="toast-title">' + (title || '') + '</h4>' +
        '    <p class="toast-msg">' + (message || '') + '</p>' +
        '  </div>' +
        '  <button class="toast-close-btn" title="Dismiss">&times;</button>' +
        '</div>' +
        '<div class="toast-progress-bar" style="animation-duration: ' + duration + 'ms"></div>';

    container.appendChild(item);

    // Reinitialize Lucide icons inside the new toast
    if (window.lucide) {
        lucide.createIcons();
    }

    // Trigger slide-in transition animation
    setTimeout(function () {
        item.classList.add('toast-show');
    }, 15);

    // Close helper function
    function closeToast() {
        item.classList.remove('toast-show');
        item.classList.add('toast-hide');
        setTimeout(function () {
            if (item.parentNode) {
                item.parentNode.removeChild(item);
            }
        }, 350);
    }

    // Close button trigger
    var closeBtn = item.querySelector('.toast-close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', closeToast);
    }

    // Auto-dismiss timer
    setTimeout(function () {
        if (item.parentNode) {
            closeToast();
        }
    }, duration);
}

// ─── Convenience Aliases ──────────────────────────────────
const toast = {
    success: (t, m, d) => showToast('success', t, m, d),
    error:   (t, m, d) => showToast('error',   t, m, d),
    warning: (t, m, d) => showToast('warning', t, m, d),
    info:    (t, m, d) => showToast('sapphire', t, m, d),
};
