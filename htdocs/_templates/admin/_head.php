<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — <?= htmlspecialchars(get_config('project_title', 'Dashboard')) ?></title>

<!-- Favicons -->
<link rel="apple-touch-icon" sizes="180x180" href="<?= get_config('base_path') ?>assets/favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?= get_config('base_path') ?>assets/favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?= get_config('base_path') ?>assets/favicon/favicon-16x16.png">
<link rel="shortcut icon" href="<?= get_config('base_path') ?>favicon.ico">

<!-- Admin CSS -->
<link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/index.css">
<link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/admin.css">
<link rel="stylesheet" href="<?= get_config('base_path') ?>assets/css/toastv3.css">

<!-- Tailwind CSS -->
<script>(function () { var w = console.warn; console.warn = function () { if (arguments[0] && typeof arguments[0] === 'string' && arguments[0].includes('cdn.tailwindcss.com')) return; w.apply(console, arguments); }; })();</script>
<script src="https://cdn.tailwindcss.com"></script>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

<!-- Global Theme Engine -->
<script src="<?= get_config('base_path') ?>theme.js"></script>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800;900&display=swap" rel="stylesheet">