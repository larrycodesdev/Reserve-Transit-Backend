<?php
    require_once __DIR__ . '/../config/functions.php';

    class AdminUser {
        private $db;

        public function __construct() {
            global $db;
            $this->db = $db;
        }

        /**
         * List admins with pagination
         */
        public function listAdmins(int $limit = 50, int $offset = 0): array {
            $stmt = $this->db->prepare("SELECT id, email, full_name, role, created_at, updated_at, is_active 
                                        FROM users 
                                        WHERE role IN ('admin', 'super_admin') 
                                        ORDER BY created_at DESC 
                                        LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * Create a new admin
         */
        public function createAdmin(string $email, string $password, string $fullName, string $role): array {
            // Check if email exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception("Email already exists");
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $now = date('Y-m-d H:i:s');

            $stmt = $this->db->prepare("INSERT INTO users (email, password_hash, full_name, role, created_at, updated_at, is_active)
                                        VALUES (?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$email, $passwordHash, $fullName, $role, $now, $now]);

            $id = $this->db->lastInsertId();

            return [
                'id' => $id,
                'email' => $email,
                'full_name' => $fullName,
                'role' => $role,
                'created_at' => $now,
                'updated_at' => $now,
                'is_active' => true
            ];
        }

        /**
         * Update user's role
         */
        public function updateRole(string $userId, string $role): bool {
            // Prevent demoting last super_admin
            if ($role !== 'super_admin') {
                $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role='super_admin'");
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                $stmt = $this->db->prepare("SELECT role FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user && $user['role'] === 'super_admin' && $count <= 1) {
                    throw new Exception("Cannot demote the last super_admin");
                }
            }

            $stmt = $this->db->prepare("UPDATE users SET role = ?, updated_at = ? WHERE id = ?");
            $stmt->execute([$role, date('Y-m-d H:i:s'), $userId]);
            return true;
        }

        /**
         * Set a user's password
         */
        public function setPassword(string $userId, string $password): bool {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("UPDATE users SET password_hash = ?, updated_at = ? WHERE id = ?");
            $stmt->execute([$hash, date('Y-m-d H:i:s'), $userId]);
            return true;
        }

        /**
         * Get user by ID
         */
        public function getByID(string $userId): ?array {
            $stmt = $this->db->prepare("SELECT id, email, full_name, role, is_active, created_at, updated_at FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
        }
    }
