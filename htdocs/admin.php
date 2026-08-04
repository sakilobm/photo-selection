<?php

/**
 * Administrative Dashboard Controller
 */

require_once 'libs/load.php';

use Aether\Session;

// Ensure administrator is authenticated
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    if (Session::isset('session_token')) {
        $sess = Session::getUserSession();
        if ($sess) {
            $sess->destroy();
        }
        Session::delete('session_token');
    }
    header('Location: ' . Session::url('login'));
    exit;
}

Session::ensureLogin();

// Render view
Session::renderView('admin', [
    'title' => 'Studio Command Center | OBM Studio'
], '_empty');
