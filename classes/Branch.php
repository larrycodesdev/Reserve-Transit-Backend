<?php
    require_once __DIR__ . '/../config/functions.php';

    class Branch {
        private $db;

        public function __construct() {
            global $db;
            $this->db = $db;
        }

        // Create a branch
        public function create(string $name, string $location, string $manager): array {
            $stmt = $this->db->prepare("INSERT INTO branches (name, location, manager, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
            $now = date('Y-m-d H:i:s');
            $stmt->execute([$name, $location, $manager, $now, $now]);
            return $this->get($this->db->lastInsertId());
        }

        // Get single branch
        public function get(int $id): array {
            $stmt = $this->db->prepare("SELECT * FROM branches WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // List branches with pagination
        public function list(int $limit = 50, int $offset = 0): array {
            $stmt = $this->db->prepare("SELECT * FROM branches ORDER BY id DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Update branch
        public function update(int $id, array $data): array {
            $stmt = $this->db->prepare("UPDATE branches SET name = ?, location = ?, manager = ?, updated_at = ? WHERE id = ?");
            $now = date('Y-m-d H:i:s');
            $stmt->execute([$data['name'], $data['location'], $data['manager'], $now, $id]);
            return $this->get($id);
        }

        // Delete branch
        public function delete(int $id): bool {
            $stmt = $this->db->prepare("DELETE FROM branches WHERE id = ?");
            return $stmt->execute([$id]);
        }
    }
