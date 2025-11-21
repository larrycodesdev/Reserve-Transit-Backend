<?php
    require_once __DIR__ . '/../config/functions.php';
    require_once __DIR__ . '/../classes/Auth.php';

    function authMiddleware() {
        return function() {
            $headers = getallheaders();
            if (!isset($headers['Authorization'])) {
                http_response_code(401);
                echo json_encode(['error' => 'Missing Authorization header']);
                exit;
            }

            $authHeader = $headers['Authorization'];
            if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid Authorization format']);
                exit;
            }

            $token = $matches[1];
            $auth = new Auth();
            $user = $auth->validateToken($token);
            if (!$user) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid token']);
                exit;
            }

            // Attach user info to global request
            $_REQUEST['userID'] = $user['id'];
            $_REQUEST['userRole'] = $user['role'];
        };
    }
