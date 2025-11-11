#!/bin/bash

echo "================================================"
echo "  Tempo Web - Production Deployment Script"
echo "================================================"
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_info() {
    echo -e "${YELLOW}➜${NC} $1"
}

# Check if Node.js is installed
print_info "Checking Node.js installation..."
if command -v node &> /dev/null; then
    NODE_VERSION=$(node -v)
    print_success "Node.js is installed: $NODE_VERSION"
else
    print_error "Node.js is not installed!"
    echo "Please install Node.js from cPanel or contact your hosting provider."
    exit 1
fi

# Check if npm is installed
print_info "Checking npm installation..."
if command -v npm &> /dev/null; then
    NPM_VERSION=$(npm -v)
    print_success "npm is installed: $NPM_VERSION"
else
    print_error "npm is not installed!"
    exit 1
fi

# Install dependencies
print_info "Installing Node.js dependencies..."
npm install
if [ $? -eq 0 ]; then
    print_success "Dependencies installed successfully"
else
    print_error "Failed to install dependencies"
    exit 1
fi

# Check if .env file exists
print_info "Checking environment configuration..."
if [ -f ".env" ]; then
    print_success ".env file found"
else
    print_error ".env file not found!"
    echo "Please ensure .env file is uploaded with correct credentials."
    exit 1
fi

# Test database connection
print_info "Testing database connection..."
node -e "
const mysql = require('mysql2/promise');
require('dotenv').config();

async function testDB() {
    try {
        const connection = await mysql.createConnection({
            host: process.env.DB_HOST,
            user: process.env.DB_USER,
            password: process.env.DB_PASSWORD,
            database: process.env.DB_NAME,
            port: process.env.DB_PORT
        });
        console.log('✓ Database connection successful');
        await connection.end();
        process.exit(0);
    } catch (error) {
        console.log('✗ Database connection failed:', error.message);
        process.exit(1);
    }
}

testDB();
"

if [ $? -eq 0 ]; then
    print_success "Database connection test passed"
else
    print_error "Database connection test failed"
    echo "Please check your database credentials in .env file"
    exit 1
fi

# Check if PM2 is installed
print_info "Checking for PM2 process manager..."
if command -v pm2 &> /dev/null; then
    print_success "PM2 is already installed"
else
    print_info "Installing PM2 globally..."
    npm install -g pm2
    if [ $? -eq 0 ]; then
        print_success "PM2 installed successfully"
    else
        print_error "Failed to install PM2"
        echo "You may need to run: npm install -g pm2 with sudo or admin privileges"
    fi
fi

# Stop existing PM2 process if running
print_info "Checking for existing running processes..."
if command -v pm2 &> /dev/null; then
    pm2 stop tempo-web 2>/dev/null
    pm2 delete tempo-web 2>/dev/null
    print_success "Cleaned up old processes"
fi

# Start the application with PM2
print_info "Starting Tempo Web application..."
if command -v pm2 &> /dev/null; then
    pm2 start server.js --name tempo-web --env production
    if [ $? -eq 0 ]; then
        print_success "Application started with PM2"
        pm2 save
        echo ""
        pm2 status
    else
        print_error "Failed to start application with PM2"
        exit 1
    fi
else
    print_info "PM2 not available, starting with node directly..."
    print_info "Note: This will run in foreground. Press Ctrl+C to stop."
    node server.js
fi

echo ""
echo "================================================"
echo "  Deployment Complete!"
echo "================================================"
echo ""
print_success "Your Tempo Web application is now running!"
echo ""
echo "Access your application at:"
echo "  • Homepage:  https://tempoapp.ro"
echo "  • Login:     https://tempoapp.ro/login"
echo "  • Register:  https://tempoapp.ro/register"
echo ""
echo "Useful PM2 commands:"
echo "  • pm2 status              - Check status"
echo "  • pm2 logs tempo-web      - View logs"
echo "  • pm2 restart tempo-web   - Restart app"
echo "  • pm2 stop tempo-web      - Stop app"
echo ""
