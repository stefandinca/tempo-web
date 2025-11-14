const express = require('express');
const session = require('express-session');
const bodyParser = require('body-parser');
const path = require('path');
require('dotenv').config();

const { testConnection } = require('./config/database');
const adminRoutes = require('./routes/admin');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Session configuration
app.use(session({
  secret: process.env.SESSION_SECRET || 'tempo-default-secret-change-this',
  resave: false,
  saveUninitialized: false,
  cookie: {
    secure: process.env.NODE_ENV === 'production', // Use secure cookies in production
    httpOnly: true,
    maxAge: 24 * 60 * 60 * 1000 // 24 hours
  }
}));

// Serve static files from dist directory
app.use(express.static(path.join(__dirname, 'dist')));

// Admin routes
app.use('/admin', adminRoutes);

// Root route - serve the main landing page
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'dist', 'index.html'));
});

// Health check endpoint
app.get('/health', (req, res) => {
  res.json({
    status: 'ok',
    timestamp: new Date().toISOString(),
    uptime: process.uptime()
  });
});

// 404 handler
app.use((req, res) => {
  res.status(404).sendFile(path.join(__dirname, 'dist', 'index.html'));
});

// Error handler
app.use((err, req, res, next) => {
  console.error('Server error:', err);
  res.status(500).json({
    success: false,
    message: 'Internal server error',
    error: process.env.NODE_ENV === 'development' ? err.message : undefined
  });
});

// Start server
const startServer = async () => {
  try {
    // Test database connection
    console.log('\n🔍 Testing database connection...');
    const dbConnected = await testConnection();

    if (!dbConnected) {
      console.warn('⚠️  Warning: Could not connect to database. Please check your .env configuration.');
      console.warn('   The server will start, but admin features may not work properly.\n');
    }

    // Start listening
    app.listen(PORT, () => {
      console.log('\n╔════════════════════════════════════════════════════════╗');
      console.log('║                                                        ║');
      console.log('║            🚀 TEMPO WEB SERVER STARTED                ║');
      console.log('║                                                        ║');
      console.log('╠════════════════════════════════════════════════════════╣');
      console.log(`║  🌐 Server:          http://localhost:${PORT.toString().padEnd(18)} ║`);
      console.log(`║  🔐 Admin Login:     http://localhost:${PORT}/admin/login    ║`);
      console.log(`║  📊 Admin Dashboard: http://localhost:${PORT}/admin/dashboard║`);
      console.log('╠════════════════════════════════════════════════════════╣');
      console.log(`║  📦 Environment:     ${(process.env.NODE_ENV || 'development').padEnd(31)}║`);
      console.log(`║  🗄️  Database:        ${(process.env.DB_NAME || 'Not configured').padEnd(31)}║`);
      console.log(`║  🔌 DB Status:       ${(dbConnected ? '✅ Connected' : '❌ Disconnected').padEnd(31)}║`);
      console.log('╚════════════════════════════════════════════════════════╝\n');
      console.log('📝 Default admin credentials (change in .env file):');
      console.log(`   Username: ${process.env.ADMIN_USERNAME || 'admin'}`);
      console.log(`   Password: ${process.env.ADMIN_PASSWORD || 'ChangeThisPassword123!'}\n`);
      console.log('Press Ctrl+C to stop the server\n');
    });
  } catch (error) {
    console.error('❌ Failed to start server:', error);
    process.exit(1);
  }
};

// Handle graceful shutdown
process.on('SIGTERM', () => {
  console.log('\n🛑 SIGTERM received, shutting down gracefully...');
  process.exit(0);
});

process.on('SIGINT', () => {
  console.log('\n\n🛑 Server stopped by user');
  process.exit(0);
});

// Start the server
startServer();

module.exports = app;
