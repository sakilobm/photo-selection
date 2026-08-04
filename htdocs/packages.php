<?php

/**
 * Packages & Pricing Page Controller
 */

require_once 'libs/load.php';

use Aether\Session;
use App\Package;

// Fetch packages dynamically from MySQL database
$packagesList = Package::getAll();

// Convert features JSON strings to PHP arrays
foreach ($packagesList as &$p) {
    if (is_string($p['features'])) {
        $p['features'] = json_decode($p['features'], true) ?: [];
    }
}

// Render view using templates
Session::renderView('packages', [
    'title'    => 'Packages & Pricing | OBM Studio',
    'packages' => $packagesList
]);
