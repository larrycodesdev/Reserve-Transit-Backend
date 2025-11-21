<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/Booking.php';
    require_once __DIR__ . '/../../classes/Trip.php';
    require_once __DIR__ . '/../../classes/Finance.php';
    require_once __DIR__ . '/../../middlewares/auth.php';

    $booking = new Booking();
    $trip = new Trip();
    $finance = new Finance();

    try {
        $totalBookings = $booking->count();
        $totalTrips = $trip->count();
        $totalRevenue = $finance->totalRevenue();

        echo json_encode([
            'total_bookings' => $totalBookings,
            'total_trips' => $totalTrips,
            'total_revenue' => $totalRevenue
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch stats']);
    }
