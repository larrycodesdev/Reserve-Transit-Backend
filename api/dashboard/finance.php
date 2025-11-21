<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/Finance.php';
    require_once __DIR__ . '/../../middlewares/auth.php';

    $finance = new Finance();

    try {
        $overview = $finance->overview();
        echo json_encode($overview);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch finance overview']);
    }
