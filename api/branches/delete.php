<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/Branch.php';
    require_once __DIR__ . '/../../middlewares/auth.php';
    require_once __DIR__ . '/../../middlewares/super_admin.php';

    superAdminMiddleware();

    $branchId = $_GET['id'] ?? null;

    if (!$branchId) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    $branch = new Branch();

    try {
        $branch->delete($branchId);
        echo json_encode(['message' => 'Branch deleted successfully']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete branch']);
    }
