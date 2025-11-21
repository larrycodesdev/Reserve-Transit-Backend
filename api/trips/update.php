<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/Trip.php';
    require_once __DIR__ . '/../../middlewares/auth.php';
    require_once __DIR__ . '/../../middlewares/super_admin.php';

    superAdminMiddleware();

    $tripId = $_GET['id'] ?? null;
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$tripId || !isset($input['from_branch_id'], $input['to_branch_id'], $input['departure_time'], $input['arrival_time'], $input['price'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    $trip = new Trip();

    try {
        $updated = $trip->update(
            $tripId,
            $input['from_branch_id'],
            $input['to_branch_id'],
            $input['departure_time'],
            $input['arrival_time'],
            $input['price']
        );
        echo json_encode($updated);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update trip']);
    }
