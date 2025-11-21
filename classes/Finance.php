<?php
    require_once __DIR__ . '/../config/functions.php';

    class Finance {
        private $db;

        public function __construct() {
            global $db;
            $this->db = $db;
        }

        public function recordPayment(int $bookingId, float $amount, string $method): array {
            $stmt = $this->db->prepare("INSERT INTO finances (booking_id, amount, method, created_at) VALUES (?, ?, ?, ?)");
            $now = date('Y-m-d H:i:s');
            $stmt->execute([$bookingId, $amount, $method, $now]);
            return $this->get($this->db->lastInsertId());
        }

        public function get(int $id): array {
            $stmt = $this->db->prepare("SELECT * FROM finances WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function list(int $limit = 50, int $offset = 0): array {
            $stmt = $this->db->prepare("SELECT * FROM finances ORDER BY created_at DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
