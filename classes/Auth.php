<?php
    require_once __DIR__ . '/../config/functions.php';
    require_once __DIR__ . '/AdminUser.php';
    require_once __DIR__ . '/../vendor/autoload.php'; // for firebase/php-jwt

    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;

    class Auth {
        private $db;
        private $adminUser;
        private $jwtSecret = 'YOUR_SECRET_KEY'; // Change for production
        private $jwtExpire = 3600; // 1 hour

        public function __construct() {
            global $db;
            $this->db = $db;
            $this->adminUser = new AdminUser();
        }

        /**
         * Login user
         */
        public function login(string $email, string $password): array {
            $stmt = $this->db->prepare("SELECT id, password_hash, full_name, role, is_active FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !$user['is_active']) {
                throw new Exception("Invalid credentials");
            }

            if (!password_verify($password, $user['password_hash'])) {
                throw new Exception("Invalid credentials");
            }

            $token = $this->generateJWT($user['id'], $user['role']);

            return [
                'access_token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'email' => $email,
                    'full_name' => $user['full_name'],
                    'role' => $user['role']
                ]
            ];
        }

        /**
         * Register new admin
         */
        public function register(string $email, string $password, string $fullName): array {
            return $this->adminUser->createAdmin($email, $password, $fullName, 'admin');
        }

        /**
         * Generate JWT
         */
        public function generateJWT(string $userId, string $role): string {
            $payload = [
                'iss' => 'yourdomain.com',
                'iat' => time(),
                'exp' => time() + $this->jwtExpire,
                'sub' => $userId,
                'role' => $role
            ];
            return JWT::encode($payload, $this->jwtSecret, 'HS256');
        }

        /**
         * Validate JWT
         */
        public function validateJWT(string $token): array {
            try {
                $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
                return [
                    'userId' => $decoded->sub,
                    'role' => $decoded->role
                ];
            } catch (\Exception $e) {
                throw new Exception("Invalid token");
            }
        }

        /**
         * Change password
         */
        public function changePassword(string $userId, string $oldPassword, string $newPassword): bool {
            $stmt = $this->db->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("User not found");
            }

            if (!password_verify($oldPassword, $user['password_hash'])) {
                throw new Exception("Old password does not match");
            }

            $hash = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("UPDATE users SET password_hash = ?, updated_at = ? WHERE id = ?");
            $stmt->execute([$hash, date('Y-m-d H:i:s'), $userId]);
            return true;
        }
    }
