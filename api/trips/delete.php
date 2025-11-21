<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/Trip.php';
    require_once __DIR__ . '/../../middlewares/auth.php';
    require_once __DIR__ . '/../../middlewares/super_admin.php';

    superAdminMiddleware();

    $tripId = $_GET['id'] ?? null;

    if (!$tripId) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    $trip = new Trip();

    try {
        $trip->delete($tripId);
        echo json_encode(['message' => 'Trip deleted successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete trip']);
    }
