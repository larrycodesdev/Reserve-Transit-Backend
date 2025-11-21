<?php
    require_once __DIR__ . '/../config/functions.php';

    class AdminUser {
        private $db;

        public function __construct() {
            global $db;
            $this->db = $db;
        }

        public function createAdmin(string $email, string $password, string $fullName, string $role): array {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("INSERT INTO users (email, password_hash, full_name, role, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, 1, ?, ?)");
            $now = date('Y-m-d H:i:s');
            $stmt->execute([$email, $hash, $fullName, $role, $now, $now]);
            $id = $this->db->lastInsertId();
            return $this->get($id);
        }

        public function get($id): array {
            $stmt = $this->db->prepare("SELECT id, email, full_name, role, is_active FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function list(int $limit = 50, int $offset = 0): array {
            $stmt = $this->db->prepare("SELECT id, email, full_name, role, is_active FROM users ORDER BY id DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, $limit, PDO::PARAM_INT);
            $stmt->bindValue(2, $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function updateRole(int $id, string $role): bool {
            $stmt = $this->db->prepare("UPDATE users SET role = ?, updated_at = ? WHERE id = ?");
            return $stmt->execute([$role, date('Y-m-d H:i:s'), $id]);
        }

        public function setPassword(int $id, string $password): bool {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("UPDATE users SET password_hash = ?, updated_at = ? WHERE id = ?");
            return $stmt->execute([$hash, date('Y-m-d H:i:s'), $id]);
        }
    }
