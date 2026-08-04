<?php

/**
 * Save Live Event API Controller (Admin Only)
 * ===========================================
 * POST /api/admin/save_live_event
 */

use App\LiveEvent;
use Aether\Database;

$save_live_event = function () {
    $this->requireAuth();

    $data = array_merge($this->_request, $this->getJsonPayload());

    $title = $data['title'] ?? 'Wedding Broadcast';
    $subtitle = $data['subtitle'] ?? 'Live Stream';
    $code = $data['code'] ?? 'OBM026';
    $stream_url = $data['stream_url'] ?? 'assets/wedding.jpg';
    $viewers = (int)($data['viewers'] ?? 142);
    $chat_enabled = (int)($data['chat_enabled'] ?? 1);
    $quality = $data['quality'] ?? '1080p';

    $db = Database::getConnection();

    $active = LiveEvent::getActive();
    if ($active) {
        $stmt = $db->prepare("UPDATE `live_event` SET `title` = ?, `subtitle` = ?, `code` = ?, `stream_url` = ?, `viewers` = ?, `chat_enabled` = ?, `quality` = ? WHERE `id` = ?");
        $success = $stmt->execute([$title, $subtitle, $code, $stream_url, $viewers, $chat_enabled, $quality, $active->id]);
    } else {
        $stmt = $db->prepare("INSERT INTO `live_event` (`title`, `subtitle`, `code`, `stream_url`, `viewers`, `chat_enabled`, `quality`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([$title, $subtitle, $code, $stream_url, $viewers, $chat_enabled, $quality]);
    }

    if ($success) {
        $this->response($this->json([
            'success' => true,
            'message' => 'Live event settings updated in database.'
        ]), 200);
    } else {
        $this->response($this->json([
            'success' => false,
            'message' => 'Failed to save live event settings.'
        ]), 500);
    }
};
