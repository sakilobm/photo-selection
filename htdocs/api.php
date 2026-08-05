<?php

/**
 * api.php — Modern API Entry Point
 * Routes: /api/{method} or /api/{namespace}/{method}
 */

require_once 'libs/load.php';

use Aether\API;
use Aether\Session;

$api = new API();

// 1. Register Global Middleware (demonstrates the new feature)
$api->addMiddleware(function ($api, $next) {
    // Shared logic: e.g. logging or common headers
    header('X-Framework: Aether-Catalyst');
    return $next();
});

// 2. Auth Middleware – Protect endpoints based on namespace permissions
$api->addMiddleware(function ($api, $next) {
    $namespace = $_GET['namespace'] ?? '';
    if ($namespace === 'admin') {
        // Only admin user sessions allowed
        if (!Session::isAuthenticated()) {
            $api->response($api->json(['error' => 'Unauthorized Administrator Access']), 401);
        }
    } elseif ($namespace === 'photos') {
        // Admin or active authenticated client allowed
        if (!Session::isAuthenticated() && !Session::isset('client_id')) {
            $api->response($api->json(['error' => 'Unauthorized Client Access']), 401);
        }
    } elseif ($namespace !== 'auth') {
        // Protect all other namespaces for admin only
        if (!Session::isAuthenticated()) {
            $api->response($api->json(['error' => 'Unauthorized Access']), 401);
        }
    }
    return $next();
});

// 3. Process the API request
try {
    $api->processApi();
} catch (Exception $e) {
    $api->die($e);
}
