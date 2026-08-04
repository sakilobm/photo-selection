<?php

/**
 * Modern Framework Loader (PSR-4)
 * ===============================
 * Bootstraps the framework using Composer autoloader.
 */

// Define absolute path to project root
define('HTDOCS_ROOT', __DIR__ . '/..');

// Load Composer autoloader (PSR-4 + Dependencies)
$loader = require_once __DIR__ . '/../vendor/autoload.php';

// --- Vendor-style Class Aliasing (Global access) ---
class_alias('Aether\Session', 'Session');
class_alias('Aether\User', 'User');
class_alias('Aether\UserSession', 'UserSession');
class_alias('Aether\Database', 'Database');
class_alias('Aether\WebAPI', 'WebAPI');
class_alias('Aether\SupabaseClient', 'SupabaseClient');
class_alias('Aether\Mailer', 'Mailer');

// Global config singleton (Legacy support)
global $__site_config;

// Bootstrap: Initialize WebAPI (Loads .env, validates, connects DB, initiates session)
use Aether\WebAPI;

$wapi = new WebAPI();
$wapi->initiateSession();

/**
 * Universal config reader – Checks .env first, then config.json.
 *
 * @param string $key     The key to read (can be ENV_CONSTANT or json_key)
 * @param mixed  $default Default value if not found
 * @return mixed
 */
function get_config(string $key, $default = null)
{
    // 1. Try Environment Variables
    $envValue = $_ENV[strtoupper($key)] ?? $_SERVER[strtoupper($key)] ?? false;
    if ($envValue !== false) return $envValue;

    // 2. Try global config JSON string ($__site_config)
    global $__site_config;
    if (!empty($__site_config)) {
        $array = json_decode($__site_config, true);
        if (isset($array[$key])) return $array[$key];
    }

    return $default;
}

/**
 * Direct include helper for small partials.
 * For full views, use Aether\Session::renderView().
 */
function load_template(string $name): void
{
    include HTDOCS_ROOT . "/_templates/{$name}.php";
}
