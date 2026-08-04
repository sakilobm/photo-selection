<?php

/**
 * Client Photo Selection Portal Controller
 */

require_once 'libs/load.php';

use Aether\Session;

// Render view
Session::renderView('photo-selection', [
    'title' => 'Client Photo Selection Portal | OBM Studio'
]);
