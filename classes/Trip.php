<?php
    require_once __DIR__ . '/../config/functions.php';

    class Trip {
        private $db;

        public function __construct() {
            global $db;
            $this->db = $db;
        }

        public function create(array $data): array {
            $stmt = $this->db->prepare("INSERT INTO trips (branch_id, destination, departure_time, arrival_time, seats, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $now = date('Y-m-d H:i:s');
            $stmt->execute([
                $data['branch_id'],
                $data['destination'],
                $data['departure_time'],
                $data['arrival_time'],
                $data['seats'],
                $now,
                $now
            ]);
            return $this->get($this->db->lastInsertId());
        }

        public function get(int $id): array {
            $stmt = $this->db->prepare("SELECT * FROM trips WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function list(int $limit = 50, int $offset = 0): array {
            $stmt = $this->db->prepare("SELECT * FROM trips ORDER BY departure_time DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function update(int $id, array $data): array {
            $stmt = $this->db->prepare("UPDATE trips SET branch_id=?, destination=?, departure_time=?, arrival_time=?, seats=?, updated_at=? WHERE id=?");
            $now = date('Y-m-d H:i:s');
            $stmt->execute([
                $data['branch_id'],
                $data['destination'],
                $data['departure_time'],
                $data['arrival_time'],
                $data['seats'],
                $now,
                $id
            ]);
            return $this->get($id);
        }

        public function delete(int $id): bool {
            $stmt = $this->db->prepare("DELETE FROM trips WHERE id = ?");
            return $stmt->execute([$id]);
        }

        public function count() {
            global $db;
            $stmt = $db->query("SELECT COUNT(*) as total FROM trips");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row['total'];
        }

        public function today() {
            global $db;
            $today = date('Y-m-d');
            $stmt = $db->prepare("SELECT * FROM trips WHERE DATE(departure_date) = ?");
            $stmt->execute([$today]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function list($limit = 50, $offset = 0) {
            global $db;
            $stmt = $db->prepare("SELECT * FROM trips ORDER BY departure_date DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
