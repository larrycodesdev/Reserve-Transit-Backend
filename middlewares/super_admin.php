<?php
    function superAdminMiddleware() {
        return function() {
            if (!isset($_REQUEST['userRole']) || $_REQUEST['userRole'] !== 'super_admin') {
                http_response_code(403);
                echo json_encode(['error' => 'Insufficient permissions']);
                exit;
            }
        };
    }
