<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/Trip.php';
    require_once __DIR__ . '/../../middlewares/auth.php';

    $trip = new Trip();

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $status = $_GET['status'] ?? null;

    try {
        $trips = $trip->list($limit, $offset, $status);
        echo json_encode($trips);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to list trips']);
    }
