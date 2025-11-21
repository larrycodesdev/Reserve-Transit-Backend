<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/Booking.php';
    require_once __DIR__ . '/../../middlewares/auth.php';
    require_once __DIR__ . '/../../middlewares/super_admin.php';

    superAdminMiddleware();

    $booking = new Booking();

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $status = $_GET['status'] ?? null;

    try {
        $bookings = $booking->list($limit, $offset, $status);
        echo json_encode($bookings);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to list bookings']);
    }
