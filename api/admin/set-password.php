<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/AdminUser.php';
    require_once __DIR__ . '/../../middlewares/auth.php';
    require_once __DIR__ . '/../../middlewares/super_admin.php';

    superAdminMiddleware();

    $input = json_decode(file_get_contents('php://input'), true);
    $userId = $_GET['id'] ?? null;

    if (!$userId || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    $adminUser = new AdminUser();

    try {
        $adminUser->setPassword($userId, $input['password']);
        echo json_encode(['message' => 'Password updated']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to set password']);
    }
