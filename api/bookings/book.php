<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/Booking.php';
    require_once __DIR__ . '/../../middlewares/auth.php';

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['trip_id'], $input['passenger_name'], $input['passenger_email'], $input['seats'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request body']);
        exit;
    }

    $booking = new Booking();

    try {
        $newBooking = $booking->create(
            $input['trip_id'],
            $input['passenger_name'],
            $input['passenger_email'],
            $input['seats']
        );
        http_response_code(201);
        echo json_encode($newBooking);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create booking']);
    }
