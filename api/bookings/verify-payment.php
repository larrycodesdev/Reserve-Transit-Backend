<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/Booking.php';
    require_once __DIR__ . '/../../middlewares/auth.php';

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['booking_id'], $input['payment_reference'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request body']);
        exit;
    }

    $booking = new Booking();

    try {
        $verified = $booking->verifyPayment($input['booking_id'], $input['payment_reference']);
        echo json_encode($verified);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to verify payment']);
    }
