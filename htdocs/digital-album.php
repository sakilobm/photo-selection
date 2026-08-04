<?php

/**
 * Digital Album Showcase Controller
 */

require_once 'libs/load.php';

use Aether\Session;
use App\Album;

// Fetch albums dynamically from MySQL database
$albums = Album::getAll();

// Render view
Session::renderView('digital-album', [
    'title'  => 'Digital Album Showcase | OBM Studio',
    'albums' => $albums
]);
