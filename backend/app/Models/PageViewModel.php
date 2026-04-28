<?php
require_once __DIR__ . '/../../config/connection.php';

class PageViewModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function recordView($sessionId, $pageUrl, $referrer = null) {
        return $this->db->insert('page_views', [
            'session_id' => $sessionId,
            'page_url' => $pageUrl,
            'referrer' => $referrer,
            'viewed_at' => date('Y-m-d H:i:s'),
            'duration_seconds' => 0,
            'is_bounce' => 1,
        ]);
    }

    public function updateDuration($sessionId, $pageUrl, $durationSeconds, $isBounce) {
        $row = $this->db->selectOne(
            "SELECT id FROM page_views WHERE session_id = ? AND page_url = ? ORDER BY viewed_at DESC LIMIT 1",
            [$sessionId, $pageUrl]
        );

        if ($row) {
            $this->db->update(
                'page_views',
                [
                    'duration_seconds' => $durationSeconds,
                    'is_bounce' => $isBounce ? 1 : 0,
                ],
                'id = :where_id',
                ['where_id' => $row['id']]
            );
            return true;
        }

        return false;
    }
}
