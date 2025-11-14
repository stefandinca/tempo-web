<?php
require_once __DIR__ . '/config.php';
requireAuth(); // All API endpoints require authentication

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'database-info':
        getDatabaseInfo();
        break;

    case 'tables':
        getTables();
        break;

    case 'test-connection':
        testConnection();
        break;

    case 'table-data':
        $tableName = $_GET['table'] ?? '';
        getTableData($tableName);
        break;

    default:
        sendJson(['success' => false, 'message' => 'Invalid action'], 400);
}

/**
 * Get database connection information
 */
function getDatabaseInfo() {
    $conn = getDbConnection();

    if (!$conn) {
        sendJson([
            'success' => false,
            'message' => 'Database connection failed'
        ], 500);
    }

    $result = $conn->query("SELECT DATABASE() as dbname");
    $row = $result->fetch_assoc();

    sendJson([
        'success' => true,
        'data' => [
            'database' => DB_NAME,
            'host' => DB_HOST,
            'port' => DB_PORT,
            'user' => DB_USER,
            'connected' => $row['dbname'] ? true : false
        ]
    ]);
}

/**
 * Get list of all tables in the database
 */
function getTables() {
    $conn = getDbConnection();

    if (!$conn) {
        sendJson([
            'success' => false,
            'message' => 'Database connection failed'
        ], 500);
    }

    $result = $conn->query("SHOW TABLES");

    if (!$result) {
        sendJson([
            'success' => false,
            'message' => 'Failed to get tables: ' . $conn->error
        ], 500);
    }

    $tables = [];
    while ($row = $result->fetch_array()) {
        $tables[] = ['name' => $row[0]];
    }

    sendJson([
        'success' => true,
        'data' => $tables
    ]);
}

/**
 * Test database connection
 */
function testConnection() {
    $conn = getDbConnection();

    if (!$conn) {
        sendJson([
            'success' => false,
            'message' => 'Database connection failed'
        ], 500);
    }

    $result = $conn->query("SELECT 1 + 1 AS result");

    if (!$result) {
        sendJson([
            'success' => false,
            'message' => 'Connection test failed'
        ], 500);
    }

    $row = $result->fetch_assoc();

    sendJson([
        'success' => true,
        'message' => 'Database connection is working',
        'data' => $row
    ]);
}

/**
 * Get data from a specific table
 */
function getTableData($tableName) {
    if (empty($tableName)) {
        sendJson([
            'success' => false,
            'message' => 'Table name is required'
        ], 400);
    }

    $conn = getDbConnection();

    if (!$conn) {
        sendJson([
            'success' => false,
            'message' => 'Database connection failed'
        ], 500);
    }

    // Validate table exists
    $result = $conn->query("SHOW TABLES");
    $tableExists = false;

    while ($row = $result->fetch_array()) {
        if ($row[0] === $tableName) {
            $tableExists = true;
            break;
        }
    }

    if (!$tableExists) {
        sendJson([
            'success' => false,
            'message' => 'Table not found'
        ], 404);
    }

    // Get pagination parameters
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 100;
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

    // Sanitize table name (only allow alphanumeric and underscore)
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
        sendJson([
            'success' => false,
            'message' => 'Invalid table name'
        ], 400);
    }

    // Get table data
    $query = "SELECT * FROM `{$tableName}` LIMIT {$limit} OFFSET {$offset}";
    $result = $conn->query($query);

    if (!$result) {
        sendJson([
            'success' => false,
            'message' => 'Failed to get table data: ' . $conn->error
        ], 500);
    }

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    // Get total count
    $countResult = $conn->query("SELECT COUNT(*) as total FROM `{$tableName}`");
    $countRow = $countResult->fetch_assoc();

    sendJson([
        'success' => true,
        'data' => [
            'rows' => $rows,
            'total' => $countRow['total'],
            'limit' => $limit,
            'offset' => $offset
        ]
    ]);
}
?>
