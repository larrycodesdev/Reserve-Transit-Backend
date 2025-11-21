<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/Trip.php';
    require_once __DIR__ . '/../../middlewares/auth.php';
    require_once __DIR__ . '/../../middlewares/super_admin.php';

    superAdminMiddleware();

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['from_branch_id'], $input['to_branch_id'], $input['departure_time'], $input['arrival_time'], $input['price'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request body']);
        exit;
    }

    $trip = new Trip();

    try {
        $newTrip = $trip->create(
            $input['from_branch_id'],
            $input['to_branch_id'],
            $input['departure_time'],
            $input['arrival_time'],
            $input['price']
        );
        http_response_code(201);
        echo json_encode($newTrip);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create trip']);
    }
