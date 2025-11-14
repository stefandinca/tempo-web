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

    case 'add-subscriber':
        addSubscriber();
        break;

    case 'get-clients':
        getClients();
        break;

    case 'update-subscriber':
        updateSubscriber();
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

/**
 * Add a new subscriber to the clients table
 */
function addSubscriber() {
    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        sendJson([
            'success' => false,
            'message' => 'Invalid JSON data'
        ], 400);
    }

    // Validate required fields
    $required = ['name', 'date_created', 'expiry', 'subscription_type', 'max_clients', 'max_users'];
    foreach ($required as $field) {
        if (!isset($data[$field]) || $data[$field] === '') {
            sendJson([
                'success' => false,
                'message' => "Missing required field: {$field}"
            ], 400);
        }
    }

    $conn = getDbConnection();

    if (!$conn) {
        sendJson([
            'success' => false,
            'message' => 'Database connection failed'
        ], 500);
    }

    // Convert datetime-local format to MySQL datetime format
    $dateCreated = date('Y-m-d H:i:s', strtotime($data['date_created']));
    $expiry = $data['expiry'];
    $subscriberId = isset($data['subscriber_id']) ? $data['subscriber_id'] : '';
    $link = isset($data['link']) ? $data['link'] : '';
    $contactInfo = isset($data['contact_info']) ? $data['contact_info'] : '';

    // Prepare and execute insert query
    $stmt = $conn->prepare("INSERT INTO subscribers (subscriber_id, name, date_created, expiry, subscription_type, max_clients, max_users, link, contact_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        sendJson([
            'success' => false,
            'message' => 'Failed to prepare statement: ' . $conn->error
        ], 500);
    }

    $stmt->bind_param(
        'sssssiiss',
        $subscriberId,
        $data['name'],
        $dateCreated,
        $expiry,
        $data['subscription_type'],
        $data['max_clients'],
        $data['max_users'],
        $link,
        $contactInfo
    );

    if ($stmt->execute()) {
        sendJson([
            'success' => true,
            'message' => 'Subscriber added successfully',
            'data' => ['id' => $conn->insert_id]
        ]);
    } else {
        sendJson([
            'success' => false,
            'message' => 'Failed to add subscriber: ' . $stmt->error
        ], 500);
    }
}

/**
 * Get all clients from the subscribers table
 */
function getClients() {
    $conn = getDbConnection();

    if (!$conn) {
        sendJson([
            'success' => false,
            'message' => 'Database connection failed'
        ], 500);
    }

    // Get all clients ordered by date_created descending
    $result = $conn->query("SELECT * FROM subscribers ORDER BY date_created DESC");

    if (!$result) {
        sendJson([
            'success' => false,
            'message' => 'Failed to get clients: ' . $conn->error
        ], 500);
    }

    $clients = [];
    while ($row = $result->fetch_assoc()) {
        $clients[] = $row;
    }

    sendJson([
        'success' => true,
        'data' => $clients
    ]);
}

/**
 * Update an existing subscriber in the subscribers table
 */
function updateSubscriber() {
    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        sendJson([
            'success' => false,
            'message' => 'Invalid JSON data'
        ], 400);
    }

    // Validate required fields
    $required = ['id', 'name', 'date_created', 'expiry', 'subscription_type', 'max_clients', 'max_users'];
    foreach ($required as $field) {
        // For numeric fields, allow 0 as valid
        if (!isset($data[$field])) {
            sendJson([
                'success' => false,
                'message' => "Missing required field: {$field}"
            ], 400);
        }
        // For string fields, check if empty
        if (in_array($field, ['name', 'date_created', 'expiry', 'subscription_type']) && $data[$field] === '') {
            sendJson([
                'success' => false,
                'message' => "Field cannot be empty: {$field}"
            ], 400);
        }
    }

    $conn = getDbConnection();

    if (!$conn) {
        sendJson([
            'success' => false,
            'message' => 'Database connection failed'
        ], 500);
    }

    // Convert datetime-local format to MySQL datetime format
    $dateCreated = date('Y-m-d H:i:s', strtotime($data['date_created']));
    $expiry = $data['expiry'];
    $subscriberId = isset($data['subscriber_id']) ? $data['subscriber_id'] : '';
    $link = isset($data['link']) ? $data['link'] : '';
    $contactInfo = isset($data['contact_info']) ? $data['contact_info'] : '';

    // Prepare and execute update query
    $stmt = $conn->prepare("UPDATE subscribers SET subscriber_id = ?, name = ?, date_created = ?, expiry = ?, subscription_type = ?, max_clients = ?, max_users = ?, link = ?, contact_info = ? WHERE id = ?");

    if (!$stmt) {
        sendJson([
            'success' => false,
            'message' => 'Failed to prepare statement: ' . $conn->error
        ], 500);
    }

    $stmt->bind_param(
        'sssssiissi',
        $subscriberId,
        $data['name'],
        $dateCreated,
        $expiry,
        $data['subscription_type'],
        $data['max_clients'],
        $data['max_users'],
        $link,
        $contactInfo,
        $data['id']
    );

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            sendJson([
                'success' => true,
                'message' => 'Subscriber updated successfully'
            ]);
        } else {
            sendJson([
                'success' => false,
                'message' => 'No changes made or subscriber not found'
            ], 404);
        }
    } else {
        sendJson([
            'success' => false,
            'message' => 'Failed to update subscriber: ' . $stmt->error
        ], 500);
    }
}
?>
