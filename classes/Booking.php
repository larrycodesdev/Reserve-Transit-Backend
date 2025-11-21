<?php
    require_once __DIR__ . '/../config/functions.php';

    class Booking {
        private $db;

        public function __construct() {
            global $db;
            $this->db = $db;
        }

        public function bookTicket(int $tripId, string $passengerName, string $seatNumber, float $amount): array {
            $stmt = $this->db->prepare("INSERT INTO bookings (trip_id, passenger_name, seat_number, amount, created_at) VALUES (?, ?, ?, ?, ?)");
            $now = date('Y-m-d H:i:s');
            $stmt->execute([$tripId, $passengerName, $seatNumber, $amount, $now]);
            return $this->get($this->db->lastInsertId());
        }

        public function get(int $id): array {
            $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function list(int $tripId = null, int $limit = 50, int $offset = 0): array {
            $sql = "SELECT * FROM bookings";
            $params = [];
            if ($tripId !== null) {
                $sql .= " WHERE trip_id = ?";
                $params[] = $tripId;
            }
            $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function count() {
            global $db;
            $stmt = $db->query("SELECT COUNT(*) as total FROM bookings");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$row['total'];
        }

        public function today() {
            global $db;
            $today = date('Y-m-d');
            $stmt = $db->prepare("SELECT * FROM bookings WHERE DATE(created_at) = ?");
            $stmt->execute([$today]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function list($limit = 50, $offset = 0) {
            global $db;
            $stmt = $db->prepare("SELECT * FROM bookings ORDER BY created_at DESC LIMIT ? OFFSET ?");
            $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(2, (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
