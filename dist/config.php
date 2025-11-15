<?php
/**
 * Database Configuration for Tempo Admin System
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'incjzljm_tempo_app_main');
define('DB_PASS', 'tempoappmain2025'); // CHANGE THIS
define('DB_NAME', 'incjzljm_tempo_app_main');
define('DB_PORT', 3306);

// Admin credentials
define('ADMIN_USERNAME', '------'); // CHANGE THIS
define('ADMIN_PASSWORD', '-------'); // CHANGE THIS

// Session configuration
define('SESSION_NAME', 'tempo_admin_session');
define('SESSION_LIFETIME', 86400); // 24 hours in seconds

// Timezone
date_default_timezone_set('Europe/Bucharest');

/**
 * Get database connection
 */
function getDbConnection() {
    static $conn = null;

    if ($conn === null) {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

            if ($conn->connect_error) {
                error_log("Database connection failed: " . $conn->connect_error);
                return false;
            }

            $conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            return false;
        }
    }

    return $conn;
}

/**
 * Test database connection
 */
function testDbConnection() {
    $conn = getDbConnection();
    if (!$conn) {
        return false;
    }

    $result = $conn->query("SELECT 1");
    return $result !== false;
}

/**
 * Initialize session
 */
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        session_start();
    }
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    initSession();
    return isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true;
}

/**
 * Verify admin credentials
 */
function verifyAdminCredentials($username, $password) {
    return $username === ADMIN_USERNAME && $password === ADMIN_PASSWORD;
}

/**
 * Require authentication (redirect if not authenticated)
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: /admin-login.php');
        exit;
    }
}

/**
 * Send JSON response
 */
function sendJson($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>
