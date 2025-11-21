<?php
    require_once __DIR__ . '/../../config/functions.php';
    require_once __DIR__ . '/../../classes/AdminUser.php';
    require_once __DIR__ . '/../../middlewares/auth.php';
    require_once __DIR__ . '/../../middlewares/super_admin.php';

    superAdminMiddleware();

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['email'], $input['password'], $input['full_name'], $input['role'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request body']);
        exit;
    }

    $adminUser = new AdminUser();

    try {
        $user = $adminUser->createAdmin($input['email'], $input['password'], $input['full_name'], $input['role']);
        http_response_code(201);
        echo json_encode($user);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create admin']);
    }
