# Tempo Web Application

A web application for Tempo - therapy management platform with a password-protected Super Admin dashboard.

## Features

- 🌐 Modern landing page with Tailwind CSS
- 🔐 Password-protected Super Admin portal
- 🗄️ MySQL database integration (`incjzljm_tempo_app_main`)
- 📊 Admin dashboard for database management
- 🔒 Session-based authentication
- 📱 Responsive design

## Prerequisites

- Node.js (v14 or higher)
- MySQL database server
- npm or yarn package manager

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd tempo-web
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Configure environment variables**

   Copy `.env.example` to `.env` and update the values:
   ```bash
   cp .env.example .env
   ```

   Edit `.env` file with your configuration:
   ```env
   # Server Configuration
   PORT=3000
   NODE_ENV=development

   # Database Configuration
   DB_HOST=localhost
   DB_USER=your_database_user
   DB_PASSWORD=your_database_password
   DB_NAME=incjzljm_tempo_app_main
   DB_PORT=3306

   # Admin Credentials (CHANGE THESE!)
   ADMIN_USERNAME=admin
   ADMIN_PASSWORD=YourSecurePassword123!

   # Session Secret (Generate a random string)
   SESSION_SECRET=your-super-secret-session-key
   ```

4. **Ensure MySQL database exists**

   Make sure the database `incjzljm_tempo_app_main` is created on your MySQL server:
   ```sql
   CREATE DATABASE IF NOT EXISTS incjzljm_tempo_app_main;
   ```

## Running the Application

### Development Mode

Start the server:
```bash
npm start
```

Or for development with auto-reload (if you install nodemon):
```bash
npm run dev:server
```

The application will be available at:
- **Main Website**: http://localhost:3000
- **Admin Login**: http://localhost:3000/admin/login
- **Admin Dashboard**: http://localhost:3000/admin/dashboard

### Production Mode

Set environment to production in `.env`:
```env
NODE_ENV=production
```

Then start the server:
```bash
npm start
```

## Super Admin Access

### Login Credentials

Default credentials (as configured in `.env`):
- **Username**: `admin`
- **Password**: `ChangeThisPassword123!`

**⚠️ IMPORTANT**: Change these default credentials in the `.env` file before deploying to production!

### Admin Dashboard Features

Once logged in, the Super Admin dashboard provides:

- ✅ **Database Status**: Real-time connection monitoring
- 📊 **Database Tables**: View all tables in the database
- 🔄 **Refresh Tables**: Update the tables list
- 🔌 **Test Connection**: Verify database connectivity
- 📝 **Database Info**: View connection details
- 🏠 **Quick Navigation**: Easy access to main website

### API Endpoints

The admin panel exposes these protected API endpoints:

- `GET /admin/api/database-info` - Database connection information
- `GET /admin/api/tables` - List all database tables
- `GET /admin/api/test-connection` - Test database connection
- `GET /admin/api/table/:tableName` - Get data from a specific table
- `POST /admin/login` - Authenticate admin user
- `POST /admin/logout` - Logout admin user

## Project Structure

```
tempo-web/
├── config/
│   └── database.js          # Database configuration
├── middleware/
│   └── auth.js              # Authentication middleware
├── routes/
│   └── admin.js             # Admin routes
├── views/
│   ├── admin-login.html     # Admin login page
│   └── admin-dashboard.html # Admin dashboard
├── dist/                    # Static website files
│   ├── index.html
│   ├── resources.html
│   ├── tailwind-styles.css
│   └── ...
├── src/
│   └── input.css            # Tailwind source
├── .env                     # Environment configuration (not in git)
├── .env.example             # Environment template
├── .gitignore              # Git ignore rules
├── package.json            # Dependencies
├── server.js               # Express server entry point
└── README.md               # This file
```

## Security Notes

### Password Protection

The admin panel is protected by:
1. **Session-based authentication** - Prevents unauthorized access
2. **Password verification** - Credentials checked against `.env` file
3. **HTTP-only cookies** - Prevents XSS attacks
4. **Secure cookies in production** - HTTPS-only in production mode

### Best Practices

1. **Change default credentials** immediately after setup
2. **Use strong passwords** with mixed case, numbers, and symbols
3. **Keep `.env` file secure** and never commit it to version control
4. **Use HTTPS in production** for encrypted communication
5. **Regularly update dependencies** to patch security vulnerabilities
6. **Limit admin access** to trusted networks only

### For Production Deployment

1. Set `NODE_ENV=production` in `.env`
2. Use a strong, randomly generated `SESSION_SECRET`
3. Enable HTTPS/SSL certificates
4. Consider using password hashing for admin credentials
5. Set up firewall rules to restrict database access
6. Use environment-specific configuration management

## Database

The application connects to the `incjzljm_tempo_app_main` MySQL database. You can populate this database with tables and data as needed.

### Example: Creating a Test Table

```sql
USE incjzljm_tempo_app_main;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email) VALUES
  ('John Doe', 'john@example.com'),
  ('Jane Smith', 'jane@example.com');
```

## Tailwind CSS Development

To rebuild Tailwind CSS styles:
```bash
npm run dev
```

This compiles `src/input.css` to `dist/tailwind-styles.css`.

## Troubleshooting

### Database Connection Issues

If you see database connection errors:

1. Verify MySQL is running: `sudo service mysql status`
2. Check credentials in `.env` file
3. Ensure database exists: `SHOW DATABASES;`
4. Verify user permissions: `GRANT ALL ON incjzljm_tempo_app_main.* TO 'user'@'localhost';`

### Cannot Access Admin Panel

1. Ensure server is running: `npm start`
2. Check if port 3000 is available
3. Verify admin credentials in `.env`
4. Clear browser cookies and try again

### Session Issues

If you get logged out unexpectedly:

1. Check `SESSION_SECRET` is set in `.env`
2. Ensure cookies are enabled in browser
3. Verify session cookie settings in `server.js`

## License

ISC

## Support

For issues and questions, please contact the development team.
