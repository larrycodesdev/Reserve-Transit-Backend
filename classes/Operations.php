<?php
    require_once __DIR__ . '/../config/functions.php';

    class Operations {
        private $db;

        public function __construct() {
            global $db;
            $this->db = $db;
        }

        // Example: record daily operation
        public function recordOperation(int $branchId, string $type, float $amount, string $notes = ''): array {
            $stmt = $this->db->prepare("INSERT INTO operations (branch_id, type, amount, notes, created_at) VALUES (?, ?, ?, ?, ?)");
            $now = date('Y-m-d H:i:s');
            $stmt->execute([$branchId, $type, $amount, $notes, $now]);
            return $this->get($this->db->lastInsertId());
        }

        public function get(int $id): array {
            $stmt = $this->db->prepare("SELECT * FROM operations WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // List operations
        public function list(int $branchId = null, int $limit = 50, int $offset = 0): array {
            $sql = "SELECT * FROM operations";
            $params = [];
            if ($branchId !== null) {
                $sql .= " WHERE branch_id = ?";
                $params[] = $branchId;
            }
            $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
