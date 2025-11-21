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

        public function totalRevenue() {
            global $db;
            $stmt = $db->query("SELECT SUM(amount) as total FROM payments WHERE status = 'paid'");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float)($row['total'] ?? 0);
        }

        public function overview() {
            global $db;
            $stmt = $db->query("
                SELECT 
                    COUNT(*) as total_transactions,
                    SUM(amount) as total_amount,
                    SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as received,
                    SUM(CASE WHEN status != 'paid' THEN amount ELSE 0 END) as pending
                FROM payments
            ");
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
