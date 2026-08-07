/**
 * toastv3.js — Toast Notification System
 * ========================================
 * Creates animated, auto-dismissing toast messages matching styles.css specifications.
 *
 * Requires: jQuery, Lucide Icons
 */

function showToast(type, title, message, duration = 4000) {
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
    // Map 'help' to 'sapphire'
    if (lowerType === 'help') lowerType = 'sapphire';

    var $panel = $('#toast-container');
    if (!$panel.length) {
        console.warn('toastv3: #toast-container not found in DOM.');
        return;
    }

    // Map variant type to appropriate Lucide icon name
    var iconName = 'bell';
    if (lowerType === 'success') iconName = 'check-circle-2';
    else if (lowerType === 'error' || lowerType === 'alert') iconName = 'alert-triangle';
    else if (lowerType === 'warning') iconName = 'alert-circle';
    else if (lowerType === 'sapphire' || lowerType === 'info') iconName = 'info';
    else if (lowerType === 'gold') iconName = 'star';
    else if (lowerType === 'purple') iconName = 'message-square';

    // Generate toast item with modern styling classes matching styles.css
    var $item = $('<div class="toast-item toast-theme-' + lowerType + '"></div>');
    var $toast = $(
        '<div class="toast-body">' +
        '  <div class="toast-icon-box">' +
        '    <i data-lucide="' + iconName + '" class="w-5 h-5"></i>' +
        '  </div>' +
        '  <div class="toast-content">' +
        '    <h4 class="toast-title">' + title + '</h4>' +
        '    <p class="toast-msg">' + message + '</p>' +
        '  </div>' +
        '  <button class="toast-close-btn" title="Dismiss">&times;</button>' +
        '</div>' +
        '<div class="toast-progress-bar" style="animation-duration: ' + duration + 'ms"></div>'
    );

    $item.append($toast);
    $panel.append($item);

    // Reinitialize Lucide icons inside the new toast
    if (window.lucide) {
        lucide.createIcons();
    }

    // Trigger the slide-in transition animation
    setTimeout(function () {
        $item.addClass('toast-show');
    }, 15);

    // Close function helper
    function closeToast() {
        $item.removeClass('toast-show').addClass('toast-hide');
        setTimeout(function () {
            $item.remove();
        }, 350);
    }

    // Close button trigger
    $item.find('.toast-close-btn').on('click', closeToast);

    // Auto-dismiss trigger
    setTimeout(function () {
        if ($item.parent().length) {
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
