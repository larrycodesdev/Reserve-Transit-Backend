<?php
    /**
     * Global Functions + Core Initialization
     * --------------------------------------
     * - Loads database connection
     * - Auto-loads class files
     * - Common helper functions used across backend
     */

    require_once __DIR__ . '/db.php'; // Provides $db (PDO)

    // -----------------------------------------------
    // AUTO-LOAD ALL CLASSES (Your Classes Folder)
    // -----------------------------------------------
    spl_autoload_register(function ($className) {
        $classFile = __DIR__ . '/../classes/' . $className . '.php';
        if (file_exists($classFile)) {
            require_once $classFile;
        }
    });

    // -----------------------------------------------
    // UNIVERSAL JSON RESPONSE
    // -----------------------------------------------
    function jsonResponse($status = 200, $data = [], $error = null)
    {
        http_response_code($status);
        header('Content-Type: application/json');

        echo json_encode([
            'success' => $error ? false : true,
            'data'    => $error ? null : $data,
            'error'   => $error,
        ]);

        exit;
    }

    // -----------------------------------------------
    // GET RAW BODY (used for POST/PUT JSON)
    // -----------------------------------------------
    function getJsonBody()
    {
        return json_decode(file_get_contents("php://input"), true);
    }

    // -----------------------------------------------
    // SANITIZE STRINGS
    // -----------------------------------------------
    function clean($str)
    {
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    }

    // -----------------------------------------------
    // GENERATE RANDOM TRACKING NUMBER
    // -----------------------------------------------
    function generateTrackingNumber()
    {
        return strtoupper(bin2hex(random_bytes(4))); // 8-char code
    }

    // -----------------------------------------------
    // GENERATE UUID v4
    // -----------------------------------------------
    function uuid4()
    {
        $data = random_bytes(16);

        // version 4
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        // variant
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // -----------------------------------------------
    // SIMPLE JWT GENERATOR (HS256)
    // -----------------------------------------------
    function jwtCreate($payload, $secret, $expireSeconds = 86400)
    {
        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload['exp'] = time() + $expireSeconds;

        $base64Header = rtrim(strtr(base64_encode(json_encode($header)), '+/', '-_'), '=');
        $base64Payload = rtrim(strtr(base64_encode(json_encode($payload)), '+/', '-_'), '=');

        $signature = hash_hmac('sha256', "$base64Header.$base64Payload", $secret, true);
        $base64Signature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        return "$base64Header.$base64Payload.$base64Signature";
    }

    // -----------------------------------------------
    // SIMPLE JWT VALIDATION
    // -----------------------------------------------
    function jwtValidate($token, $secret)
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }

        [$header, $payload, $signature] = $parts;
        $check = hash_hmac('sha256', "$header.$payload", $secret, true);
        $base64Check = rtrim(strtr(base64_encode($check), '+/', '-_'), '=');

        if (!hash_equals($base64Check, $signature)) {
            return false;
        }

        $payloadData = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

        if (!isset($payloadData['exp']) || time() > $payloadData['exp']) {
            return false;
        }

        return $payloadData; // valid
    }
