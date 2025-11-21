<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/Trip.php';
    require_once __DIR__ . '/../../classes/Booking.php';
    require_once __DIR__ . '/../../middlewares/auth.php';

    $trip = new Trip();
    $booking = new Booking();

    try {
        $todayTrips = $trip->today();
        $todayBookings = $booking->today();

        echo json_encode([
            'trips' => $todayTrips,
            'bookings' => $todayBookings
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch today\'s data']);
    }
