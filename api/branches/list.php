<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/Branch.php';
    require_once __DIR__ . '/../../middlewares/auth.php';

    $branch = new Branch();

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    try {
        $branches = $branch->list($limit, $offset);
        echo json_encode($branches);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to list branches']);
    }
