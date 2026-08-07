<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title ?? 'OBM Studio | A Decade of Stories, Captured in Light') ?></title>
<meta name="description" content="<?= htmlspecialchars($description ?? 'OBM Studio — Award-winning photography & cinema production. A family studio built from love, craft, and a decade of dedication.') ?>">

<!-- Favicons -->
<link rel="apple-touch-icon" sizes="180x180" href="<?= get_config('base_path') ?>assets/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?= get_config('base_path') ?>assets/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?= get_config('base_path') ?>assets/favicon/favicon-16x16.png">
<link rel="manifest" href="<?= get_config('base_path') ?>assets/favicon/site.webmanifest">
<link rel="mask-icon" href="<?= get_config('base_path') ?>assets/favicon/safari-pinned-tab.svg" color="#00d2ff">
<link rel="shortcut icon" href="<?= get_config('base_path') ?>favicon.ico">
<meta name="msapplication-TileColor" content="#0a0e18">
<meta name="msapplication-config" content="<?= get_config('base_path') ?>assets/favicon/browserconfig.xml">
<meta name="theme-color" content="#0a0e18">


<!-- Tailwind CSS -->
<script>(function () { var w = console.warn; console.warn = function () { if (arguments[0] && typeof arguments[0] === 'string' && arguments[0].includes('cdn.tailwindcss.com')) return; w.apply(console, arguments); }; })();</script>
<script src="https://cdn.tailwindcss.com"></script>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

<!-- Global Theme Engine -->
<script src="<?= get_config('base_path') ?>theme.js"></script>

<!-- OBM Global Styles -->
<link rel="stylesheet" href="<?= get_config('base_path') ?>styles.css">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">

<!-- Dynamic Page-Specific CSS -->
<?php
$pageCss = HTDOCS_ROOT . '/assets/css/' . Session::currentScript() . '.css';
if (Session::currentScript() !== 'index' && file_exists($pageCss)): ?>
<link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/<?= Session::currentScript() ?>.css">
<?php endif; ?>

<!-- Standalone stylesheets for special views -->
<?php if (Session::currentScript() === 'photo-selection'): ?>
<link rel="stylesheet" href="<?= get_config('base_path') ?>photo-selection.css">
<?php endif; ?>
