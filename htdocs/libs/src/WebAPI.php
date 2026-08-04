<?php

namespace Aether;

use Exception;
use Dotenv\Dotenv;

/**
 * WebAPI Bootstrap Class
 * =====================
 * PSR-4 Namespace: Aether\WebAPI
 * Bootstraps config load, dotenv variables, database connections, and secure session params.
 */
class WebAPI
{
    /**
     * WebAPI Constructor.
     */
    public function __construct()
    {
        global $__site_config;

        $configPath = $this->resolveConfigPath();

        // 1. Load config.json file (Legacy support)
        if ($configPath && file_exists($configPath)) {
            $__site_config = file_get_contents($configPath);
        }

        // 2. Initialize Dotenv (.env validation layer)
        $dotenvRoot = HTDOCS_ROOT . '/..'; // Root project dir (where .env lives)
        if (file_exists($dotenvRoot . '/.env')) {
            $dotenv = Dotenv::createImmutable($dotenvRoot);
            $dotenv->load();

            // Strict Validation Layer: Ensure critical DB credentials exist
            $dotenv->required(['DB_HOST', 'DB_USER', 'DB_NAME'])->notEmpty();
        }

        // Establish DB connection early
        Database::getConnection();

        // --- Secure Session Cookie Configuration ---
        // Dynamically detects HTTPS to allow session cookies on local HTTP development.
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        $cookieParams = session_get_cookie_params();
        
        session_set_cookie_params([
            'lifetime' => $cookieParams['lifetime'],
            'path'     => $cookieParams['path'],
            'domain'   => $cookieParams['domain'],
            'secure'   => $isSecure,
            'httponly' => true,      // Prevent session hijacking via XSS
            'samesite' => 'Lax'       // Protection against CSRF
        ]);
    }

    /**
     * Start the session and authorize if token exists.
     */
    public function initiateSession(): void
    {
        Session::start();

        if (Session::isset('session_token')) {
            try {
                Session::$usersession = UserSession::authorize(Session::get('session_token'));
            } catch (Exception $e) {
                // If authorization fails, clear the invalid token
                Session::delete('session_token');
            }
        }
    }

    /**
     * Resolves the path to config.json.
     */
    private function resolveConfigPath(): ?string
    {
        $candidates = [
            HTDOCS_ROOT . '/../project/config.json',
            HTDOCS_ROOT . '/../config.json',
            HTDOCS_ROOT . '/config.json',
        ];

        foreach ($candidates as $candidate) {
            $real = realpath($candidate);
            if ($real && file_exists($real)) {
                return $real;
            }
        }

        return null;
    }
}
