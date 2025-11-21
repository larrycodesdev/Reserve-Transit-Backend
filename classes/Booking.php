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
    }
