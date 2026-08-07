<?php
use App\ClientPortal;
use App\LiveEvent;
use App\Package;
use App\Album;

// Fetch live database values
$portals = ClientPortal::getAll();
$activeEvent = LiveEvent::getActive();
$packages = Package::getAll();

// Map portals to include associated photo counts and selection items
$mappedPortals = [];
$totalAllocatedPhotos = 0;
$totalSelectedPhotos = 0;
$totalDeletedPhotos = 0;

foreach ($portals as $portalData) {
    $portalObj = new ClientPortal((int)$portalData['id']);
    $photos = $portalObj->getPhotos();
    
    $approvedCount = 0;
    $rejectedCount = 0;
    $deletedCount = 0;
    $photosList = [];
    
    foreach ($photos as $p) {
        $mappedPhoto = [
            'id' => (int)$p['id'],
            'name' => $p['name'],
            'category' => strtoupper($p['category']),
            'size' => '4.5 MB',
            'thumb' => $p['url'],
            'selected' => (int)$p['selected'] === 1,
            'deleted' => (int)$p['deleted'] === 1
        ];
        
        if ($p['deleted']) {
            $deletedCount++;
            $totalDeletedPhotos++;
        } elseif ($p['selected']) {
            $approvedCount++;
            $totalSelectedPhotos++;
        } else {
            $rejectedCount++;
        }
        $totalAllocatedPhotos++;
        $photosList[] = $mappedPhoto;
    }
    
    $mappedPortals[] = [
        'code' => $portalData['code'],
        'clientName' => $portalData['client_name'],
        'email' => $portalData['email'],
        'eventDate' => $portalData['event_date'],
        'maxSelection' => (int)$portalData['max_selection'],
        'status' => $portalData['blocked'] ? 'Blocked' : ($portalData['flagged'] ? 'Completed' : 'Pending'),
        'flag' => $portalData['blocked'] ? 'BLOCKED' : ($portalData['flagged'] ? 'COMPLETED' : 'PENDING'),
        'blocked' => (int)$portalData['blocked'] === 1,
        'flagged' => (int)$portalData['flagged'] === 1,
        'addedDate' => $portalData['added_date'],
        'totalPhotos' => count($photos),
        'selectedPhotos' => $approvedCount,
        'photos' => [
            'approved' => array_values(array_filter($photosList, fn($x) => $x['selected'])),
            'rejected' => array_values(array_filter($photosList, fn($x) => !$x['selected'] && !$x['deleted'])),
            'deleted' => array_values(array_filter($photosList, fn($x) => $x['deleted']))
        ]
    ];
}

// Map packages and decode features JSON list
$mappedPackages = [];
foreach ($packages as $pkg) {
    $features = is_string($pkg['features']) ? json_decode($pkg['features'], true) : $pkg['features'];
    $mappedPackages[] = [
        'id' => $pkg['id'],
        'name' => $pkg['name'],
        'price' => (int)$pkg['price'],
        'badge' => $pkg['badge'],
        'desc' => $pkg['desc'],
        'popular' => $pkg['id'] === 'gold',
        'features' => $features ?? []
    ];
}

$eventDetails = $activeEvent ? $activeEvent->getDetails() : [
    'title' => 'Wedding Broadcast',
    'subtitle' => 'Live Stream',
    'code' => 'OBM026',
    'stream_url' => 'assets/wedding.jpg',
    'viewers' => 142,
    'chat_enabled' => 1,
    'quality' => '1080p',
    'status' => 'LIVE'
];
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio Command Center | OBM Studio</title>

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?= get_config('base_path') ?>assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= get_config('base_path') ?>assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= get_config('base_path') ?>assets/favicon/favicon-16x16.png">
    <link rel="shortcut icon" href="<?= get_config('base_path') ?>favicon.ico">

    <!-- Tailwind CSS -->
    <script>(function () { var w = console.warn; console.warn = function () { if (arguments[0] && typeof arguments[0] === 'string' && arguments[0].includes('cdn.tailwindcss.com')) return; w.apply(console, arguments); }; })();</script>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Global Theme Engine -->
    <script src="<?= get_config('base_path') ?>theme.js"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">
    
    <!-- Toast Styling -->
    <link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/toastv3.css">

<script>
  // Pass MySQL datasets directly to front-end JS variables
  window.__OBM_ADMIN_DATA = {
      portals: <?= json_encode($mappedPortals) ?>,
      liveEvent: <?= json_encode($eventDetails) ?>,
      packages: <?= json_encode($mappedPackages) ?>
  };
</script>

<style>
  /* ═══════════════════════════════════════════
     EXACT STUDIO DASHBOARD STYLES
  ═══════════════════════════════════════════ */
  body {
    background: radial-gradient(circle at 50% -20%, #2e1065 0%, #0f172a 45%, #030712 100%) !important;
    min-height: 100vh;
    color: #f3f4f6;
  }

  .dashboard-aurora-bg {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 0;
    overflow: hidden;
  }

  .dash-blob {
    position: absolute;
    border-radius: 50%;
    filter: blur(100px);
    opacity: 0.25;
    animation: blobFloat 25s infinite alternate ease-in-out;
  }

  .dash-blob-1 { top: -10%; left: 20%; width: 500px; height: 500px; background: #6366f1; }
  .dash-blob-2 { top: 30%; right: 10%; width: 600px; height: 600px; background: #a855f7; animation-delay: -5s; }
  .dash-blob-3 { bottom: -10%; left: 30%; width: 550px; height: 550px; background: #06b6d4; animation-delay: -10s; }

  @keyframes blobFloat {
    0% { transform: translate(0px, 0px) scale(1); }
    50% { transform: translate(40px, -40px) scale(1.1); }
    100% { transform: translate(-30px, 30px) scale(0.95); }
  }

  .top-glass-bar {
    position: fixed;
    top: 16px;
    left: 50%;
    transform: translateX(-50%);
    width: calc(100% - 48px);
    max-width: 1400px;
    z-index: 50;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(25px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 12px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
  }

  .dashboard-container {
    position: relative;
    z-index: 10;
    max-width: 1400px;
    margin: 0 auto;
    padding: 110px 24px 60px 24px;
  }

  .view-toggle-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(12px);
  }

  .view-toggle-btn {
    padding: 8px 18px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.6);
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
  }

  .view-toggle-btn.active {
    background: var(--theme-accent, #3b82f6);
    color: #ffffff;
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4);
  }

  .hero-banner-card {
    background: linear-gradient(135deg, rgba(30, 27, 75, 0.5) 0%, rgba(15, 23, 42, 0.7) 100%);
    backdrop-filter: blur(30px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 24px;
    padding: 32px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
  }

  .hero-banner-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 400px;
    height: 100%;
    background: radial-gradient(circle at 100% 0%, rgba(168, 85, 247, 0.25) 0%, transparent 70%);
    pointer-events: none;
  }

  .dashboard-nav-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px;
    border-radius: 16px;
    background: rgba(3, 7, 18, 0.6);
    border: 1px solid rgba(255, 255, 255, 0.08);
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
    margin-top: 24px;
  }

  .dashboard-nav-bar::-webkit-scrollbar { display: none; }

  .dash-tab-btn {
    flex-shrink: 0;
    padding: 10px 18px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.65);
    background: transparent;
    border: 1px solid transparent;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
  }

  .dash-tab-btn:hover {
    color: #ffffff;
    background: rgba(255, 255, 255, 0.06);
    transform: translateY(-1px);
  }

  .dash-tab-btn.active {
    background: #3b82f6;
    color: #ffffff;
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4);
  }

  .kpi-card {
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 20px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.05);
    transition: all 0.35s ease;
  }

  .kpi-card:hover {
    transform: translateY(-4px);
    border-color: rgba(255, 255, 255, 0.2);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
  }

  .kpi-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 20px;
  }

  .glass-panel-card {
    background: rgba(15, 23, 42, 0.45);
    backdrop-filter: blur(24px) saturate(180%);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 24px;
    padding: 28px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.08);
    transition: all 0.3s ease;
  }

  .glass-panel-card:hover { border-color: rgba(255, 255, 255, 0.16); }

  .dash-input {
    width: 100%;
    padding: 12px 16px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #ffffff;
    font-size: 13px;
    outline: none;
    transition: all 0.3s ease;
  }

  .dash-input:focus {
    border-color: #3b82f6;
    background: rgba(255, 255, 255, 0.08);
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.25);
  }

  /* Status Border Highlights */
  .client-card-completed {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(15, 23, 42, 0.55) 100%) !important;
    border-left: 5px solid #10b981 !important;
  }
  .client-card-flagged {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(15, 23, 42, 0.55) 100%) !important;
    border-left: 5px solid #f59e0b !important;
  }
  .client-card-pending {
    background: linear-gradient(135deg, rgba(56, 189, 248, 0.06) 0%, rgba(15, 23, 42, 0.5) 100%) !important;
    border-left: 5px solid #0284c7 !important;
  }
  .client-card-unassigned {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.08) 0%, rgba(15, 23, 42, 0.55) 100%) !important;
    border-left: 5px solid #6366f1 !important;
  }
  .client-card-blocked {
    background: linear-gradient(135deg, rgba(244, 63, 94, 0.12) 0%, rgba(15, 23, 42, 0.65) 100%) !important;
    border-left: 5px solid #f43f5e !important;
    opacity: 0.88;
  }

  /* Tab pane visibility */
  .dash-pane { display: none; }
  .dash-pane.active-pane { display: block; }

  .cm-filter-pill {
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.6);
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.06);
    transition: all 0.25s ease;
    cursor: pointer;
  }

  .cm-filter-pill:hover, .cm-filter-pill.active {
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
    border-color: rgba(var(--theme-accentRGB), 0.3);
  }

  .cm-filter-pill.active {
    background: var(--theme-accent);
    color: #000;
    font-weight: 800;
  }
  html.theme-light body {
    background: #f8fafc !important;
    color: #0f172a !important;
  }

  html.theme-light .top-glass-bar {
    background: rgba(255, 255, 255, 0.75) !important;
    border-color: rgba(0, 0, 0, 0.08) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04) !important;
  }

  html.theme-light .top-glass-bar a,
  html.theme-light .top-glass-bar span,
  html.theme-light .top-glass-bar p,
  html.theme-light .top-glass-bar h3 {
    color: #0f172a !important;
  }

  html.theme-light .hero-banner-card {
    background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%) !important;
    border-color: rgba(0, 0, 0, 0.08) !important;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.04), inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
  }

  html.theme-light .hero-banner-card h1,
  html.theme-light .hero-banner-card h2,
  html.theme-light .hero-banner-card h3 {
    color: #0f172a !important;
  }

  html.theme-light .hero-banner-card p {
    color: #475569 !important;
  }

  html.theme-light .dashboard-nav-bar {
    background: rgba(255, 255, 255, 0.8) !important;
    border-color: rgba(0, 0, 0, 0.06) !important;
  }

  html.theme-light .dash-tab-btn {
    color: #475569 !important;
  }

  html.theme-light .dash-tab-btn:hover {
    color: #0f172a !important;
    background: rgba(0, 0, 0, 0.03) !important;
  }

  html.theme-light .dash-tab-btn.active {
    background: var(--theme-accent, #3b82f6) !important;
    color: #ffffff !important;
    border-color: transparent !important;
  }

  html.theme-light .kpi-card {
    background: rgba(255, 255, 255, 0.85) !important;
    border-color: rgba(0, 0, 0, 0.06) !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03), inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
  }

  html.theme-light .kpi-card:hover {
    border-color: rgba(0, 0, 0, 0.12) !important;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06) !important;
  }

  html.theme-light .kpi-card h3 {
    color: #0f172a !important;
  }

  html.theme-light .kpi-card span {
    color: #475569 !important;
  }

  html.theme-light .mode-toggle-btn {
    background: rgba(0, 0, 0, 0.03) !important;
    border-color: rgba(0, 0, 0, 0.08) !important;
    color: #475569 !important;
  }

  html.theme-light .mode-toggle-btn:hover {
    background: rgba(0, 0, 0, 0.06) !important;
    color: #0f172a !important;
  }

  html.theme-light .mode-toggle-btn.active {
    background: var(--theme-accent, #3b82f6) !important;
    color: #ffffff !important;
    border-color: transparent !important;
  }

  html.theme-light .cm-filter-pill {
    background: rgba(0, 0, 0, 0.03) !important;
    border-color: rgba(0, 0, 0, 0.06) !important;
    color: #475569 !important;
  }

  html.theme-light .cm-filter-pill:hover,
  html.theme-light .cm-filter-pill.active {
    background: rgba(0, 0, 0, 0.06) !important;
    color: #0f172a !important;
  }

  html.theme-light .cm-filter-pill.active {
    background: var(--theme-accent) !important;
    color: #ffffff !important;
  }

  html.theme-light .glass-panel-card {
    background: rgba(255, 255, 255, 0.85) !important;
    border-color: rgba(0, 0, 0, 0.06) !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03), inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
  }

  html.theme-light .glass-panel-card:hover {
    border-color: rgba(0, 0, 0, 0.12) !important;
  }

  html.theme-light .glass-panel-card h4,
  html.theme-light .glass-panel-card span.font-black,
  html.theme-light .glass-panel-card span.text-sm {
    color: #0f172a !important;
  }

  html.theme-light .glass-panel-card p {
    color: #475569 !important;
  }

  html.theme-light .dash-input {
    background: #ffffff !important;
    border-color: rgba(0, 0, 0, 0.1) !important;
    color: #0f172a !important;
  }

  html.theme-light .dash-input:focus {
    border-color: var(--theme-accent, #3b82f6) !important;
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.15) !important;
  }

  html.theme-light .client-card-completed {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.04) 0%, #ffffff 100%) !important;
  }
  html.theme-light .client-card-flagged {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, #ffffff 100%) !important;
  }
  html.theme-light .client-card-pending {
    background: linear-gradient(135deg, rgba(56, 189, 248, 0.04) 0%, #ffffff 100%) !important;
  }
  html.theme-light .client-card-unassigned {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.04) 0%, #ffffff 100%) !important;
  }
  html.theme-light .client-card-blocked {
    background: linear-gradient(135deg, rgba(244, 63, 94, 0.05) 0%, #ffffff 100%) !important;
  }

  /* ═══ REUSABLE MODAL DIALOG STYLES ═══ */
  .obm-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
  }

  .obm-modal-overlay.active {
    opacity: 1;
    pointer-events: auto;
  }

  .obm-modal-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(3, 7, 18, 0.75);
    backdrop-filter: blur(16px);
  }

  .obm-modal-container {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 440px;
    background: #0f172a;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 24px;
    padding: 32px;
    text-align: center;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.6);
    transform: scale(0.95);
    transition: transform 0.3s ease;
  }

  .obm-modal-overlay.active .obm-modal-container {
    transform: scale(1);
  }

  .obm-modal-icon-ring {
    position: relative;
    width: 64px;
    height: 64px;
    margin: 0 auto 20px auto;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .obm-modal-icon-circle {
    position: relative;
    z-index: 2;
    width: 64px;
    height: 64px;
    border-radius: 20px;
    background: rgba(245, 158, 11, 0.12);
    border: 1px solid rgba(245, 158, 11, 0.3);
    color: #f59e0b;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .obm-modal-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.25rem;
    font-weight: 800;
    color: #ffffff;
    margin-bottom: 8px;
  }

  .obm-modal-message {
    font-size: 0.875rem;
    color: #94a3b8;
    line-height: 1.5;
    margin-bottom: 24px;
  }

  .obm-modal-actions {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .obm-modal-btn {
    flex: 1;
    padding: 12px 20px;
    border-radius: 14px;
    font-size: 0.875rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    border: none;
  }

  .obm-modal-btn-cancel {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #cbd5e1;
  }

  .obm-modal-btn-cancel:hover {
    background: rgba(255, 255, 255, 0.12);
    color: #ffffff;
  }

  .obm-modal-btn-confirm {
    background: #ef4444;
    color: #ffffff;
    box-shadow: 0 4px 20px rgba(239, 68, 68, 0.35);
  }

  .obm-modal-btn-confirm:hover {
    background: #dc2626;
  }

  /* Modal Light Theme Overrides */
  html.theme-light .obm-modal-backdrop {
    background: rgba(248, 250, 252, 0.8) !important;
  }

  html.theme-light .obm-modal-container {
    background: #ffffff !important;
    border-color: rgba(0, 0, 0, 0.08) !important;
    box-shadow: 0 25px 60px rgba(0, 0, 0, 0.12) !important;
  }

  html.theme-light .obm-modal-title {
    color: #0f172a !important;
  }

  html.theme-light .obm-modal-message {
    color: #475569 !important;
  }

  html.theme-light .obm-modal-btn-cancel {
    background: rgba(0, 0, 0, 0.04) !important;
    border-color: rgba(0, 0, 0, 0.08) !important;
    color: #475569 !important;
  }

  html.theme-light .obm-modal-btn-cancel:hover {
    background: rgba(0, 0, 0, 0.08) !important;
    color: #0f172a !important;
  }

  /* ═══ DEEP LIGHT MODE CONTRAST FIXES ═══ */
  html.theme-light .text-white {
    color: #0f172a !important;
  }

  html.theme-light .text-slate-300 {
    color: #334155 !important;
  }

  html.theme-light .text-slate-400 {
    color: #64748b !important;
  }

  html.theme-light .bg-slate-900 {
    background: #e2e8f0 !important;
  }

  html.theme-light .border-white\/5,
  html.theme-light .border-white\/10,
  html.theme-light .border-white\/20 {
    border-color: rgba(0, 0, 0, 0.08) !important;
  }

  html.theme-light .bg-white\/5 {
    background: rgba(0, 0, 0, 0.04) !important;
  }

  html.theme-light .bg-white\/10 {
    background: rgba(0, 0, 0, 0.06) !important;
  }

  html.theme-light .theme-mode-toggle {
    background: rgba(0, 0, 0, 0.05) !important;
    border-color: rgba(0, 0, 0, 0.08) !important;
  }

  html.theme-light .view-toggle-pill {
    background: rgba(0, 0, 0, 0.04) !important;
    border-color: rgba(0, 0, 0, 0.08) !important;
  }

  html.theme-light .view-toggle-btn {
    color: #475569 !important;
  }

  html.theme-light .view-toggle-btn.active {
    background: var(--theme-accent, #3b82f6) !important;
    color: #ffffff !important;
  }

  /* ═══ ALL TABS COMPLETE DEEP LIGHT MODE OVERRIDES ═══ */
  html.theme-light input.bg-transparent {
    color: #0f172a !important;
  }

  html.theme-light select.dash-input {
    background-color: #ffffff !important;
    border-color: rgba(0, 0, 0, 0.1) !important;
    color: #0f172a !important;
  }

  html.theme-light select.dash-input option {
    background-color: #ffffff !important;
    color: #0f172a !important;
  }

  html.theme-light textarea.dash-input {
    background-color: #ffffff !important;
    border-color: rgba(0, 0, 0, 0.1) !important;
    color: #0f172a !important;
  }

  html.theme-light input.dash-input::placeholder,
  html.theme-light textarea.dash-input::placeholder {
    color: #94a3b8 !important;
  }

  html.theme-light .border-slate-800\/80 {
    border-color: rgba(0, 0, 0, 0.08) !important;
  }

  html.theme-light #upload-dropzone {
    border-color: rgba(0, 0, 0, 0.15) !important;
    background: rgba(0, 0, 0, 0.02) !important;
  }

  html.theme-light #upload-dropzone:hover {
    border-color: var(--theme-accent, #3b82f6) !important;
    background: rgba(0, 0, 0, 0.04) !important;
  }

  html.theme-light #upload-dropzone p.text-white {
    color: #0f172a !important;
  }

  html.theme-light .btn-ghost {
    background: rgba(0, 0, 0, 0.04) !important;
    border-color: rgba(0, 0, 0, 0.08) !important;
    color: #334155 !important;
  }

  html.theme-light .btn-ghost:hover {
    background: rgba(0, 0, 0, 0.08) !important;
    color: #0f172a !important;
  }

  html.theme-light .w-9.h-9.bg-white\/5 {
    background: rgba(0, 0, 0, 0.04) !important;
    border-color: rgba(0, 0, 0, 0.08) !important;
    color: #475569 !important;
  }

  html.theme-light .w-9.h-9.bg-white\/5:hover {
    background: rgba(0, 0, 0, 0.08) !important;
    color: #0f172a !important;
  }

  html.theme-light .bg-black {
    background-color: #f1f5f9 !important;
  }

  html.theme-light label.text-slate-300 {
    color: #334155 !important;
  }

  html.theme-light label.text-slate-400,
  html.theme-light span.text-slate-500 {
    color: #64748b !important;
  }

  /* ═══ VIBRANT LIGHT MODE COLOR ACCENTS ═══ */
  html.theme-light .text-blue-300 {
    color: #2563eb !important;
  }
  html.theme-light .text-cyan-400 {
    color: #0891b2 !important;
  }
  html.theme-light .text-purple-400 {
    color: #7e22ce !important;
  }
  html.theme-light .text-amber-400 {
    color: #d97706 !important;
  }
  html.theme-light .text-emerald-400 {
    color: #059669 !important;
  }
  html.theme-light .text-rose-400 {
    color: #e11d48 !important;
  }
  html.theme-light .text-slate-500 {
    color: #475569 !important;
  }
  html.theme-light .kpi-icon-box {
    background: rgba(0, 0, 0, 0.04) !important;
    border-color: rgba(0, 0, 0, 0.08) !important;
  }
</style>
</head>
<body class="antialiased selection:bg-cyan-500 selection:text-slate-950 overflow-x-hidden">

<!-- ══════ FLOATING LIQUID AURORA BACKGROUND ══════ -->
<div class="dashboard-aurora-bg">
  <div class="dash-blob dash-blob-1"></div>
  <div class="dash-blob dash-blob-2"></div>
  <div class="dash-blob dash-blob-3"></div>
</div>

<!-- ══════ TOP GLASS HEADER BAR ══════ -->
<header class="top-glass-bar">
  <a href="<?= Session::url('index') ?>" class="flex items-center gap-3 no-underline">
    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-cyan-400 via-purple-500 to-amber-400 flex items-center justify-center shadow-lg shadow-cyan-500/30">
      <i data-lucide="sliders" class="w-4 h-4 text-slate-950 font-black"></i>
    </div>
    <div>
      <span class="font-extrabold text-lg tracking-tight nav-logo-text font-['Outfit'] text-white">OBM STUDIO</span>
      <span class="block text-[9px] text-[var(--theme-accent)] font-bold tracking-widest uppercase -mt-1">PREMIUM PORTAL</span>
    </div>
  </a>

  <!-- Active Session Pill -->
  <div class="hidden sm:flex items-center gap-3 px-4 py-2 rounded-full bg-white/5 border border-white/10">
    <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-amber-400 to-rose-500 flex items-center justify-center text-slate-950 font-black text-xs">
      OBM
    </div>
    <div class="text-left leading-tight">
      <span class="text-[9px] uppercase font-bold text-slate-400 block">ACTIVE SESSION</span>
      <span class="text-xs font-bold text-white block -mt-1">Studio Command Admin</span>
    </div>
  </div>

  <!-- Right Controls: Theme switcher & Logout -->
  <div class="flex items-center gap-4">
    <!-- DUAL-LAYER THEME SWITCHER -->
    <div class="global-theme-switcher shrink-0">
      <div class="theme-mode-toggle">
        <button class="mode-toggle-btn active" data-mode="dark" onclick="OBMTheme.setMode('dark')" title="Dark Mode">Dark</button>
        <button class="mode-toggle-btn" data-mode="light" onclick="OBMTheme.setMode('light')" title="Light Mode">Light</button>
      </div>
    </div>

    <!-- Switch to Workspace button -->
    <div class="view-toggle-pill">
      <a href="<?= Session::url('photo-selection') ?>" class="view-toggle-btn">
        <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i> Gallery Workspace
      </a>
      <button class="view-toggle-btn active">
        <i data-lucide="sliders" class="w-3.5 h-3.5"></i> Studio Dashboard
      </button>
    </div>

    <button onclick="handleLogout()" class="p-2.5 rounded-xl bg-white/5 hover:bg-white/10 text-rose-400 transition-colors" title="Lock Dashboard">
      <i data-lucide="log-out" class="w-4 h-4"></i>
    </button>
  </div>
</header>

<!-- ══════ MAIN DASHBOARD CONTAINER ══════ -->
<main class="dashboard-container space-y-8">

  <!-- ── HERO COMMAND BANNER CARD ── -->
  <div class="hero-banner-card" data-reveal>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <div class="flex items-center gap-2 mb-2">
          <span class="px-3 py-1 rounded-full bg-blue-500/10 border border-blue-400/30 text-blue-300 text-[10px] font-extrabold uppercase tracking-widest">
            STUDIO COMMAND CENTER
          </span>
          <span class="text-[10px] text-slate-400 font-mono">v3.5 Live Sync</span>
        </div>
        <h1 class="text-3xl md:text-5xl font-black text-white font-['Outfit'] tracking-tight">
          Workspace &amp; Client Management
        </h1>
        <p class="text-xs md:text-sm text-slate-300 mt-2 max-w-2xl">
          Full-spectrum workspace analytics, packages &amp; pricing configuration, live broadcast control room, album review, and asset operations.
        </p>
      </div>

      <div class="flex items-center gap-2.5 px-4 py-2 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-emerald-400 text-xs font-extrabold uppercase tracking-widest whitespace-nowrap self-start md:self-auto">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
        LIVE DATABASE SYNC
      </div>
    </div>

    <!-- SCALABLE HORIZONTAL PILL TAB BAR -->
    <nav class="dashboard-nav-bar">
      <button class="dash-tab-btn active" id="tab-overview" onclick="switchDashTab('overview', this)">
        <i data-lucide="bar-chart-3" class="w-4 h-4"></i> Overview
      </button>
      <button class="dash-tab-btn" id="tab-packages" onclick="switchDashTab('packages', this)">
        <i data-lucide="tag" class="w-4 h-4 text-amber-400"></i> Packages &amp; Rates
      </button>
      <button class="dash-tab-btn" id="tab-portals" onclick="switchDashTab('portals', this)">
        <i data-lucide="users" class="w-4 h-4 text-cyan-400"></i> Client Directory
      </button>
      <button class="dash-tab-btn" id="tab-selection" onclick="switchDashTab('selection', this)">
        <i data-lucide="check-square" class="w-4 h-4 text-emerald-400"></i> Selection Tracker
      </button>
      <button class="dash-tab-btn" id="tab-live" onclick="switchDashTab('live', this)">
        <i data-lucide="radio" class="w-4 h-4 text-rose-400"></i> Live Broadcast
      </button>
      <button class="dash-tab-btn" id="tab-upload" onclick="switchDashTab('upload', this)">
        <i data-lucide="upload-cloud" class="w-4 h-4 text-blue-400"></i> Upload &amp; Send
      </button>
    </nav>
  </div>

  <!-- ── KPI STATS CARDS GRID ── -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5" data-reveal>
    <div class="kpi-card">
      <div class="kpi-icon-box bg-blue-500/10 border border-blue-400/30 text-blue-400">
        <i data-lucide="image" class="w-5 h-5"></i>
      </div>
      <div>
        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">TOTAL IMAGES</span>
        <h3 class="text-2xl font-black text-white font-['Outfit']" id="kpi-assets"><?= (int)$totalAllocatedPhotos ?></h3>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon-box bg-purple-500/10 border border-purple-400/30 text-purple-400">
        <i data-lucide="heart" class="w-5 h-5"></i>
      </div>
      <div>
        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">SELECTED</span>
        <h3 class="text-2xl font-black text-white font-['Outfit']" id="kpi-selected"><?= (int)$totalSelectedPhotos ?></h3>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon-box bg-rose-500/10 border border-rose-400/30 text-rose-400">
        <i data-lucide="trash-2" class="w-5 h-5"></i>
      </div>
      <div>
        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">DELETED</span>
        <h3 class="text-2xl font-black text-white font-['Outfit']" id="kpi-deleted"><?= (int)$totalDeletedPhotos ?></h3>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon-box bg-cyan-500/10 border border-cyan-400/30 text-cyan-400">
        <i data-lucide="users" class="w-5 h-5"></i>
      </div>
      <div>
        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">PORTALS</span>
        <h3 class="text-2xl font-black text-white font-['Outfit']" id="kpi-clients"><?= count($portals) ?></h3>
      </div>
    </div>
  </div>

  <!-- ── TAB CONTENT PANELS ── -->

  <!-- TAB 1: OVERVIEW -->
  <div id="dash-pane-overview" class="dash-pane active-pane space-y-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" data-reveal>
      <div class="glass-panel-card space-y-4">
        <div class="flex items-center justify-between border-b border-white/10 pb-3">
          <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="pie-chart" class="w-4 h-4 text-cyan-400"></i> Selection Ratio
          </h3>
          <span class="text-xs text-slate-400 font-mono">Real-time</span>
        </div>
        <div class="space-y-2">
          <div class="text-4xl font-black text-white font-['Outfit'] flex items-baseline gap-2">
            <?php
            $ratio = $totalAllocatedPhotos > 0 ? round(($totalSelectedPhotos / $totalAllocatedPhotos) * 100) : 0;
            echo $ratio;
            ?>% <span class="text-xs text-slate-400 font-normal">of photos selected</span>
          </div>
          <div class="w-full h-3 rounded-full bg-slate-900 overflow-hidden">
            <div class="h-full bg-gradient-to-r from-cyan-400 to-purple-500 rounded-full" style="width: <?= (int)$ratio ?>%"></div>
          </div>
        </div>
      </div>

      <div class="glass-panel-card space-y-4">
        <div class="flex items-center justify-between border-b border-white/10 pb-3">
          <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="layers" class="w-4 h-4 text-purple-400"></i> Active Category Breakdown
          </h3>
          <span class="text-xs text-slate-400 font-mono">Real-time DB counts</span>
        </div>
        <div class="space-y-3 text-xs">
          <div class="flex justify-between items-center py-1 border-b border-white/5">
            <span class="text-slate-300">Candid Photography</span>
            <span class="font-mono text-cyan-400 font-bold" id="breakdown-candid">0 / 0</span>
          </div>
          <div class="flex justify-between items-center py-1 border-b border-white/5">
            <span class="text-slate-300">Portrait Photography</span>
            <span class="font-mono text-purple-400 font-bold" id="breakdown-portrait">0 / 0</span>
          </div>
          <div class="flex justify-between items-center py-1">
            <span class="text-slate-300">Traditional Ceremony</span>
            <span class="font-mono text-amber-400 font-bold" id="breakdown-traditional">0 / 0</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- TAB 2: PACKAGES & RATES -->
  <div id="dash-pane-packages" class="dash-pane space-y-6">
    <div class="glass-panel-card" data-reveal>
      <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
            <i data-lucide="tag" class="w-5 h-5"></i>
          </div>
          <div>
            <h3 class="text-lg font-black text-white font-['Outfit']">Packages & Pricing Manager</h3>
            <p class="text-xs text-slate-400">Edit titles, prices, descriptions, badges, and features for all signature wedding tiers.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Dynamically Injected Package Cards -->
    <div id="packages-editor-grid" class="grid grid-cols-1 xl:grid-cols-2 gap-6" data-reveal="scale"></div>
  </div>

  <!-- TAB 3: CLIENT MANAGER -->
  <div id="dash-pane-portals" class="dash-pane space-y-6">
    <div class="glass-panel-card" data-reveal>
      <div class="flex items-center gap-3 mb-1">
        <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-400/30 flex items-center justify-center text-cyan-400">
          <i data-lucide="users" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="text-lg font-black text-white font-['Outfit']">Client Directory</h3>
          <p class="text-xs text-slate-400">Manage all registered client portals. Lock galleries, check selections, and seed login passcodes.</p>
        </div>
      </div>
    </div>

    <!-- Quick Register Client -->
    <div class="glass-panel-card" data-reveal>
      <div class="text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-3">QUICK REGISTER CLIENT</div>
      <div class="flex flex-col sm:flex-row gap-3">
        <input type="text" id="cm-client-name" placeholder="Client Name" class="dash-input flex-1">
        <input type="email" id="cm-client-email" placeholder="Email Address" class="dash-input flex-1">
        <button onclick="cmAddClient()" class="btn-primary text-xs py-2.5 px-6 flex items-center gap-2 shrink-0">
          <i data-lucide="user-plus" class="w-4 h-4"></i> Add Client Portal
        </button>
      </div>
    </div>

    <!-- Filter Buttons -->
    <div class="flex items-center justify-between flex-wrap gap-3 py-1" data-reveal>
      <div class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">FILTER BY STATUS:</div>
      <div class="flex items-center gap-2 overflow-x-auto" id="cm-filter-pills">
        <button onclick="setCMFilter('all', this)" class="cm-filter-pill active" data-filter="all">All</button>
        <button onclick="setCMFilter('unassigned', this)" class="cm-filter-pill" data-filter="unassigned">Unassigned</button>
        <button onclick="setCMFilter('pending', this)" class="cm-filter-pill" data-filter="pending">Pending</button>
        <button onclick="setCMFilter('completed', this)" class="cm-filter-pill" data-filter="completed">Completed</button>
        <button onclick="setCMFilter('blocked', this)" class="cm-filter-pill" data-filter="blocked">Blocked</button>
      </div>
    </div>

    <!-- Client Cards list -->
    <div id="cm-client-list" class="space-y-4" data-reveal></div>
  </div>

  <!-- TAB 4: SELECTION TRACKER -->
  <div id="dash-pane-selection" class="dash-pane space-y-6">
    <div class="glass-panel-card" data-reveal>
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-400/30 flex items-center justify-center text-emerald-400">
            <i data-lucide="check-square" class="w-5 h-5"></i>
          </div>
          <div>
            <h3 class="text-lg font-black text-white font-['Outfit']">Photo Selection Progress Tracker</h3>
            <p class="text-xs text-slate-400">Track client selection progress and list selected file references.</p>
          </div>
        </div>
        <div class="relative shrink-0">
          <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400"></i>
          <input type="text" id="st-search-input" oninput="renderSelectionTracker()" class="dash-input text-xs pl-9 py-2.5 w-64" placeholder="Search client name...">
        </div>
      </div>
    </div>

    <!-- Selection Trackers list -->
    <div id="st-tracker-list" class="space-y-4" data-reveal></div>
  </div>

  <!-- TAB 5: LIVE BROADCAST -->
  <div id="dash-pane-live" class="dash-pane space-y-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" data-reveal>
      <div class="glass-panel-card space-y-4">
        <h3 class="text-base font-bold text-white flex items-center gap-2">
          <i data-lucide="radio" class="w-4 h-4 text-rose-400"></i> Live Broadcast Control Room
        </h3>
        <div>
          <label class="block text-xs font-semibold text-slate-300 mb-1">Live Event Passcode</label>
          <input type="text" id="live-code-input" class="dash-input font-mono uppercase" value="<?= htmlspecialchars($eventDetails['code'] ?? 'OBM026') ?>">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-300 mb-1">Live Stream Stream URL (Poster Preview)</label>
          <input type="text" id="live-stream-url" class="dash-input font-mono" value="<?= htmlspecialchars($eventDetails['stream_url'] ?? 'assets/wedding.jpg') ?>">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-300 mb-1">Event Header Title</label>
          <input type="text" id="live-title" class="dash-input" value="<?= htmlspecialchars($eventDetails['title'] ?? 'Wedding Broadcast') ?>">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-300 mb-1">Event Subtitle</label>
          <input type="text" id="live-subtitle" class="dash-input" value="<?= htmlspecialchars($eventDetails['subtitle'] ?? 'Live Stream') ?>">
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-300 mb-1">Current Active Viewers Count</label>
          <input type="number" id="live-viewers" class="dash-input" value="<?= (int)($eventDetails['viewers'] ?? 142) ?>">
        </div>
        <div class="flex items-center gap-2.5 py-1">
          <input type="checkbox" id="live-chat-toggle" <?= ($eventDetails['chat_enabled'] ?? 1) ? 'checked' : '' ?> class="w-4 h-4 rounded text-blue-500 focus:ring-0">
          <label for="live-chat-toggle" class="text-xs font-semibold text-slate-300 cursor-pointer">Enable Live Chat Box</label>
        </div>
        <div>
          <label class="block text-xs font-semibold text-slate-300 mb-1">Stream Quality Setting</label>
          <select id="live-quality-select" class="dash-input">
            <option value="1080p" <?= ($eventDetails['quality'] ?? '') === '1080p' ? 'selected' : '' ?>>1080p Full HD</option>
            <option value="720p" <?= ($eventDetails['quality'] ?? '') === '720p' ? 'selected' : '' ?>>720p HD</option>
            <option value="480p" <?= ($eventDetails['quality'] ?? '') === '480p' ? 'selected' : '' ?>>480p Standard</option>
            <option value="Auto" <?= ($eventDetails['quality'] ?? '') === 'Auto' ? 'selected' : '' ?>>Auto Quality</option>
          </select>
        </div>
        <button onclick="saveLiveEventSettings()" class="btn-primary text-xs py-2.5 px-5 w-full justify-center">
          <i data-lucide="save" class="w-4 h-4"></i> Save Broadcast Configurations
        </button>
      </div>

      <div class="glass-panel-card space-y-4 flex flex-col justify-between">
        <div>
          <h3 class="text-base font-bold text-white flex items-center gap-2 mb-3">
            <i data-lucide="external-link" class="w-4 h-4 text-cyan-400"></i> Room Stream Preview
          </h3>
          <div class="aspect-video rounded-xl bg-black overflow-hidden relative border border-white/10">
            <img src="<?= get_config('base_path') . htmlspecialchars($eventDetails['stream_url'] ?? 'assets/wedding.jpg') ?>" class="w-full h-full object-cover opacity-60">
            <div class="absolute top-3 left-3 badge badge-gold text-[9px] uppercase tracking-widest">Active Signal Feed</div>
          </div>
        </div>
        <a href="<?= Session::url('live-event') ?>" target="_blank" class="btn-ghost w-full justify-center text-xs py-3 mt-4">
          <i data-lucide="external-link" class="w-4 h-4"></i> Open Client Live Event Screen
        </a>
      </div>
    </div>
  </div>

  <!-- TAB 6: UPLOAD & SEND -->
  <div id="dash-pane-upload" class="dash-pane space-y-6">
    <div class="glass-panel-card" data-reveal>
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-400/30 flex items-center justify-center text-blue-400">
          <i data-lucide="send" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="text-lg font-black text-white font-['Outfit']">Upload & Allocate Photos to Client</h3>
          <p class="text-xs text-slate-400">Upload wedding snapshots directly to the specific client's portal in MySQL database.</p>
        </div>
      </div>
    </div>

    <!-- Upload Form -->
    <div class="glass-panel-card space-y-5" data-reveal>
      <div>
        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-2">TARGET CLIENT</label>
        <select id="upload-client-select" class="dash-input text-sm py-3">
          <option value="">-- Select Target Client Portal --</option>
        </select>
      </div>

      <div>
        <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-widest mb-2">PHOTO CATEGORY</label>
        <select id="upload-category-select" class="dash-input text-sm py-3">
          <option value="candid">Candid Photography</option>
          <option value="portrait">Portrait Shots</option>
          <option value="traditional">Traditional Rituals</option>
        </select>
      </div>

      <!-- Drag & Drop Zone -->
      <div id="upload-dropzone" class="relative border-2 border-dashed border-white/15 rounded-2xl py-12 px-8 text-center transition-all cursor-pointer hover:border-white/30 hover:bg-white/3" onclick="document.getElementById('upload-file-input').click()" ondragover="event.preventDefault(); this.classList.add('border-blue-400','bg-blue-500/5')" ondragleave="this.classList.remove('border-blue-400','bg-blue-500/5')" ondrop="handleFileDrop(event)">
        <i data-lucide="cloud-upload" class="w-10 h-10 mx-auto text-slate-500 mb-3"></i>
        <p class="text-sm font-bold text-white mb-1">Drag photos or click to browse</p>
        <p class="text-xs text-slate-500">Supports JPG, PNG, WEBP • Max 20 files at once</p>
        <input type="file" id="upload-file-input" multiple accept="image/jpeg,image/png,image/webp" class="hidden" onchange="handleFileSelect(event)">
      </div>

      <!-- Queued Files list -->
      <div id="upload-queue-section" class="hidden">
        <div class="flex items-center justify-between mb-3">
          <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">QUEUED FILES</span>
          <span id="upload-queue-count" class="text-xs font-bold text-blue-400">0 files</span>
        </div>
        <div id="upload-queue-list" class="space-y-2 max-h-[300px] overflow-y-auto pr-1"></div>
      </div>

      <!-- Upload Button -->
      <button id="upload-dispatch-btn" onclick="dispatchUpload()" class="w-full py-4 rounded-xl text-sm font-black uppercase tracking-wider flex items-center justify-center gap-3 transition-all disabled:opacity-40 disabled:cursor-not-allowed" style="background: var(--theme-accent); color: #ffffff !important;" disabled>
        <i data-lucide="rocket" class="w-5 h-5"></i> Dispatch to Client Portal
      </button>
    </div>
  </div>

</main>

<!-- Reusable Confirmation Modal -->
<div id="obmModal" class="obm-modal-overlay" aria-hidden="true">
  <div class="obm-modal-backdrop"></div>
  <div class="obm-modal-container">
    <div class="obm-modal-icon-ring" id="obmModalIconRing">
      <div class="obm-modal-icon-glow"></div>
      <div class="obm-modal-icon-circle">
        <i data-lucide="alert-triangle" id="obmModalIcon" class="w-7 h-7"></i>
      </div>
    </div>
    <div class="obm-modal-content">
      <h3 id="obmModalTitle" class="obm-modal-title">Are you sure?</h3>
      <p id="obmModalMessage" class="obm-modal-message">This action is irreversible.</p>
    </div>
    <div class="obm-modal-actions">
      <button id="obmModalCancel" class="obm-modal-btn obm-modal-btn-cancel" onclick="closeModal(false)">
        <i data-lucide="x" class="w-3.5 h-3.5"></i> Cancel
      </button>
      <button id="obmModalConfirm" class="obm-modal-btn obm-modal-btn-confirm" onclick="closeModal(true)">
        <i data-lucide="check" class="w-3.5 h-3.5" id="obmModalConfirmIcon"></i> Confirm
      </button>
    </div>
  </div>
</div>

<script src="<?= get_config('base_path') ?>assets/js/toastv3.js"></script>
<script>
  let uploadQueue = [];
  let cmCurrentFilter = 'all';
  let stCurrentFilter = 'all';
  let _modalResolve = null;

  // Local state initialized directly from window.__OBM_ADMIN_DATA (populated by MySQL PHP)
  const OBMStore = {
      data: window.__OBM_ADMIN_DATA || { portals: [], liveEvent: {}, packages: [] },
      
      addClientPortal(portal) {
          this.data.portals.unshift(portal);
      },
      
      getClientByCode(code) {
          return this.data.portals.find(p => p.code === code);
      },
      
      toggleClientFlag(code) {
          const client = this.getClientByCode(code);
          if (client) {
              client.flagged = !client.flagged;
              client.status = client.flagged ? 'Completed' : 'Pending';
              client.flag = client.flagged ? 'COMPLETED' : 'PENDING';
          }
      },
      
      toggleClientBlock(code) {
          const client = this.getClientByCode(code);
          if (client) {
              client.blocked = !client.blocked;
              client.status = client.blocked ? 'Blocked' : (client.flagged ? 'Completed' : 'Pending');
              client.flag = client.blocked ? 'BLOCKED' : (client.flagged ? 'COMPLETED' : 'PENDING');
          }
      },
      
      deleteClientPortal(code) {
          this.data.portals = this.data.portals.filter(p => p.code !== code);
      },
      
      getActiveClients() {
          return this.data.portals.filter(p => !p.blocked);
      }
  };

  document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) lucide.createIcons();

    const ro = new IntersectionObserver((entries) => {
      entries.forEach((e, i) => {
        if (e.isIntersecting) {
          setTimeout(() => e.target.classList.add('revealed'), i * 60);
          ro.unobserve(e.target);
        }
      });
    }, { threshold: 0.02 });
    document.querySelectorAll('[data-reveal]').forEach(el => ro.observe(el));

    // Show initial data
    renderAllData();
    switchDashTab('overview', document.getElementById('tab-overview'));
  });

  // Reusable Confirmation Modal helper
  function showConfirmModal({ title = 'Are you sure?', message = '', type = 'warning' } = {}) {
      return new Promise((resolve) => {
          _modalResolve = resolve;
          const modal = document.getElementById('obmModal');
          document.getElementById('obmModalTitle').innerText = title;
          document.getElementById('obmModalMessage').innerText = message;
          modal.classList.add('active');
          document.body.classList.add('modal-open');
      });
  }

  function closeModal(confirmed) {
      const modal = document.getElementById('obmModal');
      modal.classList.remove('active');
      document.body.classList.remove('modal-open');
      if (_modalResolve) {
          _modalResolve(confirmed);
          _modalResolve = null;
      }
  }

  function switchDashTab(tabId, btn) {
      document.querySelectorAll('.dash-pane').forEach(p => p.classList.remove('active-pane'));
      document.querySelectorAll('.dash-tab-btn').forEach(b => b.classList.remove('active'));
      
      const pane = document.getElementById(`dash-pane-${tabId}`);
      if (pane) pane.classList.add('active-pane');
      if (btn) btn.classList.add('active');
  }

  function renderAllData() {
      // KPIs
      document.getElementById('kpi-clients').textContent = OBMStore.data.portals.length;
      
      const totalSelected = OBMStore.data.portals.reduce((sum, p) => sum + (p.selectedPhotos || 0), 0);
      document.getElementById('kpi-selected').textContent = totalSelected;
      
      // Calculate breakdown of photo categories across all portals
      let candidCount = 0, candidSelected = 0;
      let portraitCount = 0, portraitSelected = 0;
      let traditionalCount = 0, traditionalSelected = 0;
      let deletedCount = 0;

      OBMStore.data.portals.forEach(portal => {
          const approved = portal.photos?.approved || [];
          const rejected = portal.photos?.rejected || [];
          const deleted = portal.photos?.deleted || [];

          deletedCount += deleted.length;

          approved.forEach(p => {
              if (p.category === 'CANDID') { candidCount++; candidSelected++; }
              if (p.category === 'PORTRAIT') { portraitCount++; portraitSelected++; }
              if (p.category === 'TRADITIONAL') { traditionalCount++; traditionalSelected++; }
          });

          rejected.forEach(p => {
              if (p.category === 'CANDID') candidCount++;
              if (p.category === 'PORTRAIT') portraitCount++;
              if (p.category === 'TRADITIONAL') traditionalCount++;
          });
      });

      document.getElementById('kpi-deleted').textContent = deletedCount;
      document.getElementById('breakdown-candid').textContent = `${candidSelected} / ${candidCount} selected`;
      document.getElementById('breakdown-portrait').textContent = `${portraitSelected} / ${portraitCount} selected`;
      document.getElementById('breakdown-traditional').textContent = `${traditionalSelected} / ${traditionalCount} selected`;

      renderPackageManagement();
      renderClientManager();
      renderSelectionTracker();
      populateUploadClientDropdown();
  }

  function getStatusBadge(client) {
      if (client.blocked) return '<span class="px-2.5 py-0.5 rounded-full bg-rose-500/10 border border-rose-400/30 text-rose-400 text-[9px] font-extrabold uppercase tracking-wider">BLOCKED</span>';
      if (client.flagged) return '<span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-emerald-400 text-[9px] font-extrabold uppercase tracking-wider">COMPLETED</span>';
      if (client.totalPhotos === 0) return '<span class="px-2.5 py-0.5 rounded-full bg-indigo-500/10 border border-indigo-400/30 text-indigo-400 text-[9px] font-extrabold uppercase tracking-wider">UNASSIGNED</span>';
      return '<span class="px-2.5 py-0.5 rounded-full bg-amber-500/10 border border-amber-400/30 text-amber-400 text-[9px] font-extrabold uppercase tracking-wider">PENDING</span>';
  }

  function getAvatarColor(name) {
      const colors = ['from-cyan-400 to-blue-500', 'from-purple-400 to-pink-500', 'from-emerald-400 to-teal-500', 'from-amber-400 to-orange-500', 'from-rose-400 to-red-500'];
      let hash = 0;
      for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
      return colors[Math.abs(hash) % colors.length];
  }

  function getClientCardClass(p) {
      if (p.blocked) return 'client-card-blocked border-l-4 border-l-rose-500';
      if (p.flagged) return 'client-card-completed border-l-4 border-l-emerald-400';
      if (p.totalPhotos === 0) return 'client-card-unassigned border-l-4 border-l-indigo-400';
      return 'client-card-pending border-l-4 border-l-sky-400';
  }

  function setCMFilter(filter, btn) {
      cmCurrentFilter = filter;
      document.querySelectorAll('#cm-filter-pills button').forEach(b => b.classList.remove('active'));
      if (btn) btn.classList.add('active');
      renderClientManager();
  }

  function renderClientManager() {
      const container = document.getElementById('cm-client-list');
      if (!container) return;

      const filtered = OBMStore.data.portals.filter(p => {
          if (cmCurrentFilter === 'unassigned') return p.totalPhotos === 0;
          if (cmCurrentFilter === 'pending') return !p.blocked && !p.flagged && p.totalPhotos > 0;
          if (cmCurrentFilter === 'completed') return p.flagged && !p.blocked;
          if (cmCurrentFilter === 'blocked') return p.blocked;
          return true;
      });

      if (filtered.length === 0) {
          container.innerHTML = `<div class="glass-panel-card text-center py-10"><p class="text-sm text-slate-400">No client portals match this filter.</p></div>`;
          return;
      }

      container.innerHTML = filtered.map(p => {
          const initial = (p.clientName || 'C')[0].toUpperCase();
          const avatarGradient = getAvatarColor(p.clientName);
          const allocated = p.totalPhotos;
          const selected = p.selectedPhotos;
          const cardClass = getClientCardClass(p);

          const actionButtons = `
              <button onclick="cmToggleFlag('${p.code}')" class="w-9 h-9 rounded-xl ${p.flagged ? 'bg-yellow-500/20 border-yellow-400/50 text-yellow-300' : 'bg-white/5 border-white/10 text-slate-400 hover:text-white'} border flex items-center justify-center transition-colors" title="${p.flagged ? 'Mark Pending' : 'Mark Completed'}">
                <i data-lucide="flag" class="w-4 h-4"></i>
              </button>
              <button onclick="cmToggleBlock('${p.code}')" class="w-9 h-9 rounded-xl ${p.blocked ? 'bg-rose-500/20 border-rose-400/50 text-rose-300' : 'bg-white/5 border-white/10 text-slate-400 hover:text-white'} border flex items-center justify-center transition-colors" title="${p.blocked ? 'Unblock Client' : 'Block Access'}">
                <i data-lucide="ban" class="w-4 h-4"></i>
              </button>
              <button onclick="cmDeleteClient('${p.code}')" class="w-9 h-9 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 flex items-center justify-center text-rose-400 transition-colors" title="Delete Client">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
              </button>
          `;

          return `
            <div class="glass-panel-card ${cardClass} transition-all hover:border-white/30">
              <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                  <div class="w-11 h-11 rounded-full bg-gradient-to-tr ${avatarGradient} flex items-center justify-center text-slate-950 font-black text-lg shrink-0 shadow-lg">
                    ${initial}
                  </div>
                  <div>
                    <div class="flex items-center gap-2 flex-wrap">
                      <span class="text-sm font-black text-white">${p.clientName}</span>
                      ${getStatusBadge(p)}
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">${p.email}</p>
                    <div class="flex items-center gap-3 mt-1 text-[10px] text-slate-500">
                      <span class="flex items-center gap-1"><i data-lucide="image" class="w-3 h-3"></i> ${allocated} allocated</span>
                      <span class="flex items-center gap-1"><i data-lucide="heart" class="w-3 h-3"></i> ${selected} selected</span>
                      <span class="flex items-center gap-1"><i data-lucide="key" class="w-3 h-3"></i> Code: <strong>${p.code}</strong></span>
                    </div>
                  </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                  ${actionButtons}
                </div>
              </div>
            </div>`;
      }).join('');

      if (window.lucide) lucide.createIcons();
  }

  async function cmAddClient() {
      const name = document.getElementById('cm-client-name').value.trim();
      const email = document.getElementById('cm-client-email').value.trim();
      if (!name || !email) { showToast('Missing Fields', 'Please fill name and email.', 'error'); return; }
      
      const code = name.replace(/[^a-zA-Z]/g, '').toUpperCase().slice(0, 4) + Math.floor(100 + Math.random() * 900);

      try {
          const response = await fetch('/api/admin/create_portal', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ code, client_name: name, email, max_selection: 100 })
          });
          const res = await response.json();
          if (res.success) {
              OBMStore.addClientPortal({ code, clientName: name, email, totalPhotos: 0, selectedPhotos: 0, blocked: false, flagged: false });
              document.getElementById('cm-client-name').value = '';
              document.getElementById('cm-client-email').value = '';
              renderAllData();
              showToast('Portal Created', `Registered passcode key ${code}`, 'success');
          } else {
              showToast('Error', res.message, 'error');
          }
      } catch (err) {
          showToast('Error', 'Server communication failure.', 'error');
      }
  }

  async function cmToggleFlag(code) {
      try {
          const response = await fetch('/api/admin/toggle_flag', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ code })
          });
          const res = await response.json();
          if (res.success) {
              OBMStore.toggleClientFlag(code);
              renderAllData();
              showToast('Status Updated', 'Portal flag changed.', 'success');
          }
      } catch (err) {
          showToast('Error', 'Failed to toggle flag.', 'error');
      }
  }

  async function cmToggleBlock(code) {
      try {
          const response = await fetch('/api/admin/toggle_block', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ code })
          });
          const res = await response.json();
          if (res.success) {
              OBMStore.toggleClientBlock(code);
              renderAllData();
              showToast('Access Updated', 'Portal block status updated.', 'success');
          }
      } catch (err) {
          showToast('Error', 'Failed to toggle access block.', 'error');
      }
  }

  async function cmDeleteClient(code) {
      const confirmed = await showConfirmModal({
          title: 'Delete Client Portal?',
          message: 'Permanently remove this client and all allocated image files from MySQL database?',
          type: 'danger'
      });
      if (!confirmed) return;

      try {
          const response = await fetch('/api/admin/delete_portal', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ code })
          });
          const res = await response.json();
          if (res.success) {
              OBMStore.deleteClientPortal(code);
              renderAllData();
              showToast('Portal Deleted', 'The portal was deleted successfully.', 'success');
          }
      } catch (err) {
          showToast('Error', 'Failed to delete portal.', 'error');
      }
  }

  function renderSelectionTracker() {
      const container = document.getElementById('st-tracker-list');
      if (!container) return;

      const search = document.getElementById('st-search-input').value.toLowerCase().trim();
      const filtered = OBMStore.data.portals.filter(p => {
          if (search && !p.clientName.toLowerCase().includes(search)) return false;
          return true;
      });

      if (filtered.length === 0) {
          container.innerHTML = `<div class="glass-panel-card text-center py-10"><p class="text-sm text-slate-400">No active selections found.</p></div>`;
          return;
      }

      container.innerHTML = filtered.map(p => {
          const approvedCount = p.photos?.approved?.length || p.selectedPhotos || 0;
          const totalCount = p.totalPhotos || 0;
          const ratio = totalCount > 0 ? Math.round((approvedCount / totalCount) * 100) : 0;
          const listHtml = (p.photos?.approved || []).map(photo => `
              <div class="flex items-center gap-2 bg-white/5 p-2 rounded-lg border border-white/5">
                <img src="<?= get_config('base_path') ?>${photo.thumb}" class="w-10 h-10 object-cover rounded-md border border-white/10 shrink-0">
                <div class="text-left">
                  <span class="block text-xs font-bold text-white">${photo.name}</span>
                  <span class="block text-[8px] text-slate-500 uppercase">${photo.category}</span>
                </div>
              </div>
          `).join('');

          return `
            <div class="glass-panel-card space-y-4">
              <div class="flex justify-between items-center flex-wrap gap-2">
                <div>
                  <h4 class="text-sm font-black text-white">${p.clientName} selections</h4>
                  <p class="text-[10px] text-slate-400">Passcode: <strong>${p.code}</strong></p>
                </div>
                <div class="text-right">
                  <span class="text-sm font-extrabold text-[var(--theme-accent)]">${approvedCount} / ${totalCount} chosen</span>
                  <span class="block text-[9px] text-slate-500">Progress: ${ratio}%</span>
                </div>
              </div>
              <div class="w-full h-2 rounded-full bg-slate-900 overflow-hidden">
                <div class="h-full bg-emerald-500" style="width: ${ratio}%"></div>
              </div>
              
              ${approvedCount > 0 ? `
              <div class="space-y-1.5 pt-2">
                <label class="text-[9px] uppercase font-bold text-slate-400 tracking-wider">SELECTED PHOTOS CATALOG</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-3">
                  ${listHtml}
                </div>
              </div>` : '<p class="text-xs text-slate-500 italic">No photo selections submitted yet.</p>'}
            </div>`;
      }).join('');

      if (window.lucide) lucide.createIcons();
  }

  function renderPackageManagement() {
      const container = document.getElementById('packages-editor-grid');
      if (!container) return;

      container.innerHTML = OBMStore.data.packages.map(p => {
          let accentColorClass = 'text-slate-400';
          let accentBg = 'bg-slate-500/10';
          let borderClass = 'border-slate-800/80';
          let iconName = 'tag';
          
          if (p.id === 'gold') {
              accentColorClass = 'text-amber-400';
              accentBg = 'bg-amber-500/10';
              borderClass = 'border-amber-500/30';
              iconName = 'flame';
          } else if (p.id === 'platinum') {
              accentColorClass = 'text-purple-400';
              accentBg = 'bg-purple-500/10';
              borderClass = 'border-purple-500/30';
              iconName = 'gem';
          } else if (p.id === 'imperial') {
              accentColorClass = 'text-cyan-400';
              accentBg = 'bg-cyan-500/10';
              borderClass = 'border-cyan-500/30';
              iconName = 'crown';
          }

          const featuresHtml = (p.features || []).map((feat, fIdx) => `
            <div class="flex items-center gap-2" id="feat-container-${p.id}-${fIdx}">
              <input type="text" class="dash-input text-xs flex-1 pkg-feature-input" data-pkg-id="${p.id}" value="${escapeHtml(feat)}" placeholder="Highlight item...">
              <button type="button" onclick="removeFeatureItem('${p.id}', ${fIdx})" class="p-2.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 transition-colors" title="Delete Highlight">
                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
              </button>
            </div>
          `).join('');

          return `
            <div class="glass-panel-card space-y-4 flex flex-col justify-between ${borderClass} relative">
              <div class="space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800/80">
                  <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg ${accentBg} ${accentColorClass} flex items-center justify-center">
                      <i data-lucide="${iconName}" class="w-4.5 h-4.5"></i>
                    </div>
                    <input type="text" id="pkg-name-${p.id}" class="bg-transparent border-none text-sm font-black text-white focus:outline-none focus:ring-1 focus:ring-slate-700 rounded px-1.5 py-0.5 w-40" value="${escapeHtml(p.name)}" placeholder="Package Name">
                  </div>
                  <span class="text-[9px] font-mono opacity-40 uppercase">ID: ${p.id}</span>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                  <div class="space-y-1">
                    <label class="text-[9px] uppercase tracking-wider text-slate-500 font-bold">Price (₹ INR)</label>
                    <div class="relative">
                      <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-500">₹</span>
                      <input type="number" id="pkg-price-${p.id}" class="dash-input pl-6 text-xs font-bold text-white font-mono" value="${p.price}" placeholder="e.g. 65000">
                    </div>
                  </div>
                  <div class="space-y-1">
                    <label class="text-[9px] uppercase tracking-wider text-slate-500 font-bold">Pill Badge</label>
                    <input type="text" id="pkg-badge-${p.id}" class="dash-input text-xs text-white" value="${escapeHtml(p.badge || '')}" placeholder="e.g. Cinematic & Drone">
                  </div>
                </div>
                
                <div class="space-y-1">
                  <label class="text-[9px] uppercase tracking-wider text-slate-500 font-bold">Subtitle Description</label>
                  <textarea id="pkg-desc-${p.id}" rows="2" class="dash-input text-xs text-slate-300 resize-none" placeholder="Ideal for traditional rituals...">${escapeHtml(p.desc || '')}</textarea>
                </div>

                <div class="space-y-2">
                  <div class="flex items-center justify-between">
                    <label class="text-[9px] uppercase tracking-wider text-slate-500 font-bold">Package Highlights</label>
                    <button type="button" onclick="addFeatureItem('${p.id}')" class="text-[9px] uppercase font-black text-blue-400 flex items-center gap-1 hover:opacity-80 transition-opacity">
                      <i data-lucide="plus" class="w-3 h-3"></i> Add Highlight
                    </button>
                  </div>
                  <div class="space-y-2 max-h-48 overflow-y-auto pr-1" id="pkg-features-list-${p.id}">
                    ${featuresHtml}
                  </div>
                </div>
              </div>
              
              <button type="button" onclick="savePackageData('${p.id}')" class="btn-primary w-full py-3 text-xs font-bold flex items-center justify-center gap-1.5 ${p.id === 'gold' ? 'btn-gold' : ''}">
                <i data-lucide="save" class="w-4 h-4"></i> Save Package Details
              </button>
            </div>`;
      }).join('');

      if (window.lucide) lucide.createIcons();
  }

  function addFeatureItem(pkgId) {
      const list = document.getElementById(`pkg-features-list-${pkgId}`);
      if (!list) return;
      
      const newIndex = list.children.length;
      const itemDiv = document.createElement('div');
      itemDiv.className = 'flex items-center gap-2';
      itemDiv.id = `feat-container-${pkgId}-${newIndex}`;
      itemDiv.innerHTML = `
        <input type="text" class="dash-input text-xs flex-1 pkg-feature-input" data-pkg-id="${pkgId}" value="" placeholder="New highlight item...">
        <button type="button" onclick="removeFeatureItem('${pkgId}', ${newIndex})" class="p-2.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 transition-colors" title="Delete Highlight">
          <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
        </button>
      `;
      list.appendChild(itemDiv);
      if (window.lucide) lucide.createIcons();
  }

  function removeFeatureItem(pkgId, fIdx) {
      const el = document.getElementById(`feat-container-${pkgId}-${fIdx}`);
      if (el) el.remove();
  }

  async function savePackageData(pkgId) {
      const pkg = OBMStore.data.packages.find(p => p.id === pkgId);
      if (!pkg) return;

      const name = document.getElementById(`pkg-name-${pkgId}`).value.trim();
      const price = parseInt(document.getElementById(`pkg-price-${pkgId}`).value) || 0;
      const badge = document.getElementById(`pkg-badge-${pkgId}`).value.trim();
      const desc = document.getElementById(`pkg-desc-${pkgId}`).value.trim();
      
      const featureInputs = document.querySelectorAll(`.pkg-feature-input[data-pkg-id="${pkgId}"]`);
      const features = Array.from(featureInputs).map(inp => inp.value.trim()).filter(val => val !== '');

      try {
          const response = await fetch('/api/admin/save_package', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ id: pkgId, name, price, badge, desc, features })
          });
          const res = await response.json();
          if (res.success) {
              pkg.name = name;
              pkg.price = price;
              pkg.badge = badge;
              pkg.desc = desc;
              pkg.features = features;
              renderPackageManagement();
              showToast('Saved', `"${name}" package configurations saved.`, 'success');
          }
      } catch (err) {
          showToast('Error', 'Failed to save package settings.', 'error');
      }
  }

  async function saveLiveEventSettings() {
      const title = document.getElementById('live-title').value.trim();
      const subtitle = document.getElementById('live-subtitle').value.trim();
      const code = document.getElementById('live-code-input').value.toUpperCase().trim();
      const stream_url = document.getElementById('live-stream-url').value.trim();
      const viewers = parseInt(document.getElementById('live-viewers').value) || 0;
      const chat_enabled = document.getElementById('live-chat-toggle').checked ? 1 : 0;
      const quality = document.getElementById('live-quality-select').value;

      try {
          const response = await fetch('/api/admin/save_live_event', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ title, subtitle, code, stream_url, viewers, chat_enabled, quality })
          });
          const res = await response.json();
          if (res.success) {
              showToast('Saved', 'Broadcast settings updated.', 'success');
          } else {
              showToast('Error', res.message, 'error');
          }
      } catch (err) {
          showToast('Error', 'Failed to connect to backend.', 'error');
      }
  }

  function populateUploadClientDropdown() {
      const select = document.getElementById('upload-client-select');
      if (!select) return;
      select.innerHTML = '<option value="">-- Select Target Client Portal --</option>' + 
          OBMStore.data.portals.map(p => `<option value="${p.code}">${p.clientName} (${p.email})</option>`).join('');
  }

  function handleFileSelect(e) {
      addFilesToQueue(e.target.files);
  }

  function handleFileDrop(e) {
      e.preventDefault();
      document.getElementById('upload-dropzone').classList.remove('border-blue-400','bg-blue-500/5');
      addFilesToQueue(e.dataTransfer.files);
  }

  function addFilesToQueue(files) {
      for (const file of files) {
          uploadQueue.push({
              id: Date.now() + Math.random(),
              name: file.name,
              size: file.size,
              status: 'queued',
              progress: 0,
              fileData: file
          });
      }
      renderUploadQueue();
  }

  function removeFromQueue(id) {
      uploadQueue = uploadQueue.filter(f => f.id !== id);
      renderUploadQueue();
  }

  function renderUploadQueue() {
      const section = document.getElementById('upload-queue-section');
      const list = document.getElementById('upload-queue-list');
      const count = document.getElementById('upload-queue-count');
      const dispatchBtn = document.getElementById('upload-dispatch-btn');
      
      const clientSelect = document.getElementById('upload-client-select').value;

      if (uploadQueue.length === 0) {
          section.classList.add('hidden');
          dispatchBtn.disabled = true;
          return;
      }

      section.classList.remove('hidden');
      count.textContent = `${uploadQueue.length} files`;
      dispatchBtn.disabled = !clientSelect || uploadQueue.length === 0;

      list.innerHTML = uploadQueue.map(file => `
        <div class="flex items-center justify-between bg-white/5 p-3 rounded-xl border border-white/5" data-file-id="${file.id}">
          <div class="flex items-center gap-3 flex-grow min-w-0">
            <i data-lucide="image" class="w-4 h-4 text-slate-400 shrink-0"></i>
            <div class="min-w-0 flex-grow text-left">
              <span class="block text-xs font-semibold text-white truncate">${file.name}</span>
              <div class="w-32 h-1 bg-white/10 rounded-full mt-1.5 overflow-hidden">
                <div class="h-full bg-gradient-to-r from-blue-400 to-cyan-400 transition-all duration-200" style="width: ${file.progress}%"></div>
              </div>
            </div>
          </div>
          <div class="text-[10px] text-slate-500 font-mono pr-3">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
          ${file.status !== 'done' ? `
          <button onclick="removeFromQueue(${file.id})" class="p-1 rounded-lg hover:bg-white/10 text-rose-400">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
          </button>` : '<span class="text-xs text-emerald-400 font-bold">✓ Sent</span>'}
        </div>
      `).join('');

      if (window.lucide) lucide.createIcons();
  }

  function dispatchUpload() {
      const clientCode = document.getElementById('upload-client-select').value;
      const category = document.getElementById('upload-category-select').value;
      if (!clientCode) { showToast('No Client', 'Please select a target client.', 'error'); return; }
      if (uploadQueue.length === 0) { showToast('No Files', 'Queue is empty.', 'error'); return; }

      const dispatchBtn = document.getElementById('upload-dispatch-btn');
      dispatchBtn.disabled = true;
      dispatchBtn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Dispatching...';
      if (window.lucide) lucide.createIcons();

      let idx = 0;
      async function uploadNext() {
          if (idx >= uploadQueue.length) {
              showToast('Upload Complete', `Successfully uploaded ${uploadQueue.length} files.`, 'success');
              uploadQueue = [];
              renderUploadQueue();
              dispatchBtn.disabled = false;
              dispatchBtn.innerHTML = '<i data-lucide="rocket" class="w-5 h-5"></i> Dispatch to Client Portal';
              if (window.lucide) lucide.createIcons();
              return;
          }

          const fileObj = uploadQueue[idx];
          if (fileObj.status === 'done') {
              idx++;
              uploadNext();
              return;
          }

          fileObj.status = 'uploading';
          fileObj.progress = 0;
          renderUploadQueue();

          const formData = new FormData();
          formData.append('code', clientCode);
          formData.append('category', category);
          formData.append('file', fileObj.fileData);

          try {
              const xhr = new XMLHttpRequest();
              xhr.open('POST', '/api/admin/upload_photos', true);

              xhr.upload.onprogress = function(e) {
                  if (e.lengthComputable) {
                      fileObj.progress = Math.round((e.loaded / e.total) * 100);
                      const el = document.querySelector(`[data-file-id="${fileObj.id}"] .bg-gradient-to-r`);
                      if (el) el.style.width = fileObj.progress + '%';
                  }
              };

              xhr.onload = function() {
                  if (xhr.status === 200) {
                      fileObj.status = 'done';
                      fileObj.progress = 100;
                      renderUploadQueue();
                      idx++;
                      setTimeout(uploadNext, 100);
                  } else {
                      fileObj.status = 'error';
                      showToast('Upload Failed', `Failed to upload ${fileObj.name}`, 'error');
                      dispatchBtn.disabled = false;
                      dispatchBtn.innerHTML = '<i data-lucide="rocket" class="w-5 h-5"></i> Dispatch to Client Portal';
                  }
              };

              xhr.onerror = function() {
                  fileObj.status = 'error';
                  showToast('Connection Error', `Failed to send ${fileObj.name}`, 'error');
                  dispatchBtn.disabled = false;
                  dispatchBtn.innerHTML = '<i data-lucide="rocket" class="w-5 h-5"></i> Dispatch to Client Portal';
              };

              xhr.send(formData);
          } catch (err) {
              fileObj.status = 'error';
              showToast('Error', 'Upload failed.', 'error');
              dispatchBtn.disabled = false;
          }
      }

      uploadNext();
  }

  function handleLogout() {
      window.location.href = '/admin?logout=1';
  }

  function escapeHtml(str) {
      if (!str) return '';
      return str.replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
  }

  // Enable/disable dispatch button when client is selected
  document.addEventListener('change', (e) => {
      if (e.target.id === 'upload-client-select') {
          const btn = document.getElementById('upload-dispatch-btn');
          if (btn) btn.disabled = !e.target.value || uploadQueue.length === 0;
      }
  });
</script>

<div class="toast-container toast-pos-bottom-right" id="toast-container"></div>
</body>
</html>
