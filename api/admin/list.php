<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/AdminUser.php';
    require_once __DIR__ . '/../../middlewares/auth.php';
    require_once __DIR__ . '/../../middlewares/super_admin.php';

    // Only super_admins can list admins
    superAdminMiddleware();

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    $adminUser = new AdminUser();

    try {
        $users = $adminUser->list($limit, $offset);
        echo json_encode($users);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to list users']);
    }
