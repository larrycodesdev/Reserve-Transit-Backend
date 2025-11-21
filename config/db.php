<?php
    /**
     * Database Connection File
     * Automatically switches between LOCAL and PRODUCTION DB credentials
     */

    $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';

    // -------------------------
    // LOCAL ENVIRONMENT
    // -------------------------
    if (
        $serverName === 'localhost' ||
        $serverName === '127.0.0.1' ||
        str_contains($serverName, '.test') ||
        str_contains($serverName, '.local')
    ) {
        $DB_HOST = "localhost";
        $DB_NAME = "reservetransit";
        $DB_USER = "root";
        $DB_PASS = "";
        $DB_DEBUG = true; // show DB errors locally
    }

    // -------------------------
    // PRODUCTION (cPanel)
    // -------------------------
    else {
        $DB_HOST = "localhost"; // Most cPanel uses localhost
        $DB_NAME = "cpanel_db_name";      // e.g. cpaneluser_reservetransit
        $DB_USER = "cpanel_db_user";      // e.g. cpaneluser_admin
        $DB_PASS = "cpanel_db_password";  // Set from cPanel MySQL
        $DB_DEBUG = false; // hide errors in production
    }

    try {
        $db = new PDO(
            "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
            $DB_USER,
            $DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Symfony-style exception mode
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false, // Native prepared statements
            ]
        );
    } catch (PDOException $e) {
        if ($DB_DEBUG) {
            die("Database Connection Failed: " . $e->getMessage());
        }
        die("Database connection error. Please try again later.");
    }
