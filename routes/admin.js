const express = require('express');
const router = express.Router();
const path = require('path');
const { isAuthenticated, verifyAdminCredentials } = require('../middleware/auth');
const { promisePool } = require('../config/database');

/**
 * GET /admin/login
 * Display login page
 */
router.get('/login', (req, res) => {
  // If already logged in, redirect to dashboard
  if (req.session && req.session.isAdmin) {
    return res.redirect('/admin/dashboard');
  }
  res.sendFile(path.join(__dirname, '../views/admin-login.html'));
});

/**
 * POST /admin/login
 * Handle login authentication
 */
router.post('/login', async (req, res) => {
  const { username, password } = req.body;

  try {
    const isValid = await verifyAdminCredentials(username, password);

    if (isValid) {
      req.session.isAdmin = true;
      req.session.username = username;
      req.session.loginTime = new Date().toISOString();

      res.json({ success: true, message: 'Login successful' });
    } else {
      res.status(401).json({ success: false, message: 'Invalid credentials' });
    }
  } catch (error) {
    console.error('Login error:', error);
    res.status(500).json({ success: false, message: 'Server error' });
  }
});

/**
 * POST /admin/logout
 * Handle logout
 */
router.post('/logout', (req, res) => {
  req.session.destroy((err) => {
    if (err) {
      return res.status(500).json({ success: false, message: 'Logout failed' });
    }
    res.json({ success: true, message: 'Logged out successfully' });
  });
});

/**
 * GET /admin/dashboard
 * Display admin dashboard (protected)
 */
router.get('/dashboard', isAuthenticated, (req, res) => {
  res.sendFile(path.join(__dirname, '../views/admin-dashboard.html'));
});

/**
 * GET /admin/api/database-info
 * Get database connection information
 */
router.get('/api/database-info', isAuthenticated, async (req, res) => {
  try {
    const [rows] = await promisePool.query('SELECT DATABASE() as dbname');
    const dbName = rows[0].dbname;

    res.json({
      success: true,
      data: {
        database: process.env.DB_NAME || 'incjzljm_tempo_app_main',
        host: process.env.DB_HOST || 'localhost',
        port: process.env.DB_PORT || 3306,
        user: process.env.DB_USER || 'root',
        connected: dbName ? true : false
      }
    });
  } catch (error) {
    console.error('Database info error:', error);
    res.status(500).json({
      success: false,
      message: 'Failed to get database information',
      error: error.message
    });
  }
});

/**
 * GET /admin/api/tables
 * Get list of all tables in the database
 */
router.get('/api/tables', isAuthenticated, async (req, res) => {
  try {
    const [tables] = await promisePool.query('SHOW TABLES');
    const tableNames = tables.map(table => ({
      name: Object.values(table)[0]
    }));

    res.json({
      success: true,
      data: tableNames
    });
  } catch (error) {
    console.error('Tables list error:', error);
    res.status(500).json({
      success: false,
      message: 'Failed to get tables list',
      error: error.message
    });
  }
});

/**
 * GET /admin/api/test-connection
 * Test database connection
 */
router.get('/api/test-connection', isAuthenticated, async (req, res) => {
  try {
    const [rows] = await promisePool.query('SELECT 1 + 1 AS result');
    res.json({
      success: true,
      message: 'Database connection is working',
      data: rows[0]
    });
  } catch (error) {
    console.error('Connection test error:', error);
    res.status(500).json({
      success: false,
      message: 'Database connection failed',
      error: error.message
    });
  }
});

/**
 * GET /admin/api/table/:tableName
 * Get data from a specific table
 */
router.get('/api/table/:tableName', isAuthenticated, async (req, res) => {
  const { tableName } = req.params;
  const limit = parseInt(req.query.limit) || 100;
  const offset = parseInt(req.query.offset) || 0;

  try {
    // Validate table name to prevent SQL injection
    const [tables] = await promisePool.query('SHOW TABLES');
    const tableExists = tables.some(table => Object.values(table)[0] === tableName);

    if (!tableExists) {
      return res.status(404).json({
        success: false,
        message: 'Table not found'
      });
    }

    // Get table data
    const [rows] = await promisePool.query(
      `SELECT * FROM ?? LIMIT ? OFFSET ?`,
      [tableName, limit, offset]
    );

    // Get total count
    const [countRows] = await promisePool.query(
      `SELECT COUNT(*) as total FROM ??`,
      [tableName]
    );

    res.json({
      success: true,
      data: {
        rows,
        total: countRows[0].total,
        limit,
        offset
      }
    });
  } catch (error) {
    console.error('Table data error:', error);
    res.status(500).json({
      success: false,
      message: 'Failed to get table data',
      error: error.message
    });
  }
});

module.exports = router;
