<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/AdminUser.php';
    require_once __DIR__ . '/../../middlewares/auth.php';
    require_once __DIR__ . '/../../middlewares/super_admin.php';

    superAdminMiddleware();

    $input = json_decode(file_get_contents('php://input'), true);
    $userId = $_GET['id'] ?? null;

    if (!$userId || !isset($input['role'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    // Prevent changing own role
    $currentUserId = $_SESSION['user_id'] ?? null;
    if ($currentUserId == $userId) {
        http_response_code(403);
        echo json_encode(['error' => 'You cannot change your own role']);
        exit;
    }

    $adminUser = new AdminUser();

    try {
        $adminUser->updateRole($userId, $input['role']);
        echo json_encode(['message' => 'Role updated']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update role']);
    }
