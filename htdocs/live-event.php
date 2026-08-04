<?php

/**
 * Live Event Broadcast Controller
 */

require_once 'libs/load.php';

use Aether\Session;
use App\LiveEvent;

// Load active live event from MySQL database
$activeEvent = LiveEvent::getActive();
$eventDetails = $activeEvent ? $activeEvent->getDetails() : null;

// Render view
Session::renderView('live-event', [
    'title' => 'Live Event Stream | OBM Studio',
    'event' => $eventDetails
]);
