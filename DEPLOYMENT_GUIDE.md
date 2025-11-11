# 🚀 Tempo Web - Production Deployment Guide for cPanel

This guide will help you deploy the Tempo Web login/register system to your production server.

## 📋 Prerequisites Checklist

- ✅ cPanel hosting with Node.js support
- ✅ SSH/Terminal access enabled
- ✅ MySQL database created: `incjzljm_tempo_app_main`
- ✅ MySQL user configured with password
- ✅ Domain pointing to your server: `tempoapp.ro`

---

## 📦 Step 1: Upload Files to Server

### Files to Upload (via FTP, SFTP, or cPanel File Manager):

```
tempo-web/
├── server.js                    ✅ Main server file
├── package.json                 ✅ Dependencies list
├── package-lock.json            ✅ Lock file
├── .env                         ✅ Environment config (with credentials)
├── .htaccess                    ✅ Apache reverse proxy config
├── deploy.sh                    ✅ Deployment script
├── ecosystem.config.js          ✅ PM2 configuration
├── config/
│   └── database.js             ✅ Database connection
├── routes/
│   └── auth.js                 ✅ Authentication routes
├── dist/
│   ├── index.html              ✅ Homepage
│   ├── login.html              ✅ Login page
│   ├── register.html           ✅ Register page
│   ├── resources.html          ✅ Resources page
│   ├── *.html                  ✅ Other HTML files
│   ├── styles.css              ✅ Styles
│   └── tailwind-styles.css     ✅ Tailwind styles
├── img/                         ✅ All images
├── logs/                        ✅ Empty logs directory
├── tailwind.config.js          ✅ Tailwind config
├── .gitignore                  ✅ Git ignore file
└── README.md                   ✅ Documentation
```

### ⚠️ DO NOT Upload:
- ❌ `node_modules/` folder (too large, will install on server)
- ❌ `.git/` folder (not needed in production)

### Upload Location:
- **Recommended:** `~/tempo-web/` or `~/public_html/tempo-web/`
- If you want it as the main site, upload to: `~/public_html/`

---

## 🔧 Step 2: Connect to Server via SSH

### Method 1: SSH Terminal (Recommended)
```bash
ssh your_username@tempoapp.ro
```

### Method 2: cPanel Terminal
1. Login to cPanel at `https://tempoapp.ro:2083`
2. Find **"Terminal"** or **"SSH Access"** in the search
3. Click to open web-based terminal

### Navigate to Your Project:
```bash
cd ~/tempo-web
# or
cd ~/public_html/tempo-web
```

---

## 🚀 Step 3: Run Deployment Script

Make the script executable and run it:

```bash
chmod +x deploy.sh
./deploy.sh
```

### What This Script Does:
1. ✅ Checks Node.js and npm installation
2. ✅ Installs all dependencies (`npm install`)
3. ✅ Verifies `.env` configuration
4. ✅ Tests database connection
5. ✅ Installs PM2 process manager
6. ✅ Starts the Node.js application
7. ✅ Configures auto-restart on server reboot

### Expected Output:
```
================================================
  Tempo Web - Production Deployment Script
================================================

✓ Node.js is installed: v18.x.x
✓ npm is installed: 9.x.x
✓ Dependencies installed successfully
✓ .env file found
✓ Database connection test passed
✓ PM2 installed successfully
✓ Application started with PM2

================================================
  Deployment Complete!
================================================

✓ Your Tempo Web application is now running!

Access your application at:
  • Homepage:  https://tempoapp.ro
  • Login:     https://tempoapp.ro/login
  • Register:  https://tempoapp.ro/register
```

---

## 🌐 Step 4: Configure cPanel Node.js App (Alternative to PM2)

If PM2 doesn't work or you prefer cPanel's built-in Node.js manager:

1. **Login to cPanel**
2. **Find "Setup Node.js App"** (search for it)
3. **Click "Create Application"**
4. **Configure:**
   - **Node.js version:** Select latest (18.x or 20.x)
   - **Application mode:** Production
   - **Application root:** `/home/your_username/tempo-web`
   - **Application URL:** `tempoapp.ro`
   - **Application startup file:** `server.js`
   - **Passenger log file:** Leave default
5. **Click "Create"**
6. **Click "Run NPM Install"** button (this installs dependencies)
7. **Click "Start App"**

---

## 🔒 Step 5: Configure Apache/Proxy (Usually Automatic)

### If cPanel Node.js Setup Was Used:
✅ Apache configuration is done automatically - **Skip this step**

### If Using PM2 Manually:
The `.htaccess` file should already be in place. Verify it's working:

```bash
cat .htaccess
```

If missing, create it with this content:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ http://localhost:3000/$1 [P,L]

<IfModule mod_proxy.c>
    ProxyPreserveHost On
    ProxyPass / http://localhost:3000/
    ProxyPassReverse / http://localhost:3000/
</IfModule>
```

---

## 🔐 Step 6: Enable SSL Certificate (HTTPS)

### Method 1: AutoSSL (Recommended for cPanel)
1. Go to cPanel → **"SSL/TLS Status"**
2. Check the box next to `tempoapp.ro`
3. Click **"Run AutoSSL"**
4. Wait 2-5 minutes for certificate installation

### Method 2: Let's Encrypt via cPanel
1. Go to cPanel → **"SSL/TLS"**
2. Click **"Manage SSL sites"**
3. Install Let's Encrypt certificate

### Method 3: Manual Let's Encrypt (SSH)
```bash
sudo certbot --apache -d tempoapp.ro -d www.tempoapp.ro
```

---

## ✅ Step 7: Test Your Deployment

### 1. Visit Your Website:
- Homepage: `https://tempoapp.ro`
- Should load the Tempo landing page

### 2. Test Registration:
- Go to: `https://tempoapp.ro/register`
- Fill in:
  - First Name: Test
  - Last Name: User
  - Email: test@example.com
  - Password: test1234
- Click **"Crează cont"**
- Should redirect to homepage after success

### 3. Test Login:
- Go to: `https://tempoapp.ro/login`
- Enter the credentials you just registered
- Click **"Autentifică-te"**
- Should redirect to homepage

### 4. Verify Database:
```bash
mysql -u incjzljm_tempo_app_main -p
# Enter password: tempoapp1988

USE incjzljm_tempo_app_main;
SHOW TABLES;
# Should show: tempo_clients

SELECT * FROM tempo_clients;
# Should show your test user with hashed password

EXIT;
```

---

## 🔄 Managing Your Application

### Using PM2 Commands:

```bash
# Check status
pm2 status

# View logs (real-time)
pm2 logs tempo-web

# View last 100 lines of logs
pm2 logs tempo-web --lines 100

# Restart application
pm2 restart tempo-web

# Stop application
pm2 stop tempo-web

# Start application
pm2 start tempo-web

# Delete from PM2
pm2 delete tempo-web

# Start with ecosystem config
pm2 start ecosystem.config.js

# Save current PM2 list (persist after reboot)
pm2 save

# Setup PM2 to start on server reboot
pm2 startup
```

### Using cPanel Node.js Manager:

1. Login to cPanel
2. Go to **"Setup Node.js App"**
3. Click on your application
4. Use buttons: **Start App**, **Stop App**, **Restart**

---

## 🐛 Troubleshooting

### Problem: "Cannot find module 'express'"
**Solution:**
```bash
cd ~/tempo-web
npm install
```

### Problem: "Database connection failed"
**Solution:**
1. Check credentials in `.env`:
   ```bash
   cat .env
   ```
2. Test MySQL connection:
   ```bash
   mysql -u incjzljm_tempo_app_main -p
   # Enter password: tempoapp1988
   ```
3. Verify database exists:
   ```sql
   SHOW DATABASES;
   ```

### Problem: "502 Bad Gateway" or "503 Service Unavailable"
**Solution:**
1. Check if Node.js app is running:
   ```bash
   pm2 status
   ```
2. Restart the application:
   ```bash
   pm2 restart tempo-web
   ```
3. Check logs for errors:
   ```bash
   pm2 logs tempo-web --err
   ```

### Problem: "Port 3000 already in use"
**Solution:**
```bash
# Find what's using port 3000
lsof -i :3000

# Stop the PM2 process
pm2 stop tempo-web
pm2 delete tempo-web

# Or kill the specific process
kill -9 <PID>

# Restart
pm2 start server.js --name tempo-web
```

### Problem: "Permission denied" when running deploy.sh
**Solution:**
```bash
chmod +x deploy.sh
```

### Problem: Changes not reflecting on website
**Solution:**
```bash
# Clear browser cache, then restart app
pm2 restart tempo-web

# Or via cPanel, restart the Node.js app
```

### Problem: PM2 not found after installation
**Solution:**
```bash
# Reload shell
source ~/.bashrc

# Or reinstall
npm install -g pm2

# Or use npx
npx pm2 start server.js --name tempo-web
```

---

## 📊 Monitoring & Logs

### View Application Logs:
```bash
# PM2 logs
pm2 logs tempo-web

# Or check log files
tail -f logs/output.log
tail -f logs/error.log
```

### Monitor Resources:
```bash
pm2 monit
```

### Check Application Status:
```bash
pm2 status
```

---

## 🔄 Updating Your Application

When you make changes to the code:

1. **Upload updated files** via FTP/SFTP
2. **Connect via SSH**
3. **Navigate to project:**
   ```bash
   cd ~/tempo-web
   ```
4. **If package.json changed:**
   ```bash
   npm install
   ```
5. **Restart application:**
   ```bash
   pm2 restart tempo-web
   ```

---

## 📞 Support

If you encounter issues:

1. **Check logs:** `pm2 logs tempo-web`
2. **Verify Node.js version:** `node -v` (should be 14+)
3. **Check MySQL access:** Test database connection
4. **Review cPanel error logs:** cPanel → Errors

---

## ✅ Final Checklist

- [ ] All files uploaded to server
- [ ] `.env` configured with correct credentials
- [ ] SSH/Terminal access working
- [ ] Ran `./deploy.sh` successfully
- [ ] Database connection tested and working
- [ ] Node.js application running (PM2 or cPanel)
- [ ] Can access `https://tempoapp.ro`
- [ ] Registration form works
- [ ] Login form works
- [ ] User data saved in database
- [ ] SSL certificate installed (HTTPS working)
- [ ] PM2 configured for auto-restart

---

## 🎉 Congratulations!

Your Tempo Web authentication system is now live in production!

**Application URLs:**
- Homepage: https://tempoapp.ro
- Login: https://tempoapp.ro/login
- Register: https://tempoapp.ro/register

**Database:** `incjzljm_tempo_app_main`
**Table:** `tempo_clients`
**Process Manager:** PM2 or cPanel Node.js
