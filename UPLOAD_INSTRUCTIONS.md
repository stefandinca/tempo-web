# 📤 Quick Upload Instructions

## Files Ready for Upload

All files in this directory are configured and ready for production deployment to **tempoapp.ro**.

---

## ✅ What's Already Configured:

- ✅ Database credentials set in `.env`
- ✅ Production mode enabled
- ✅ Apache reverse proxy configured (`.htaccess`)
- ✅ PM2 process manager config
- ✅ Automated deployment script

---

## 🚀 Quick Start (3 Steps)

### Step 1: Upload Files

**Upload ALL files in this folder to your server EXCEPT:**
- ❌ `node_modules/` (will be installed on server)
- ❌ `.git/` (not needed)

**Upload to:** `~/tempo-web/` or `~/public_html/tempo-web/`

**Methods:**
- **FileZilla/WinSCP:** Connect via SFTP to `tempoapp.ro`
- **cPanel File Manager:** Upload as ZIP, then extract
- **Git Clone:** If you have git access on server

---

### Step 2: Connect via SSH

```bash
ssh your_username@tempoapp.ro
```

Or use **cPanel → Terminal**

Navigate to project:
```bash
cd ~/tempo-web
```

---

### Step 3: Run Deployment Script

```bash
chmod +x deploy.sh
./deploy.sh
```

**Done!** Your site is now live at https://tempoapp.ro

---

## 📁 Complete File Structure to Upload

```
tempo-web/
├── .env                         ← Credentials configured
├── .htaccess                    ← Apache proxy configured
├── .gitignore
├── deploy.sh                    ← Deployment automation
├── ecosystem.config.js          ← PM2 configuration
├── package.json                 ← Dependencies list
├── package-lock.json
├── server.js                    ← Main server
├── tailwind.config.js
├── README.md
├── DEPLOYMENT_GUIDE.md          ← Full deployment docs
├── UPLOAD_INSTRUCTIONS.md       ← This file
├── config/
│   └── database.js
├── routes/
│   └── auth.js
├── dist/
│   ├── index.html
│   ├── login.html
│   ├── register.html
│   ├── resources.html
│   ├── articol-template.html
│   ├── primele-semne-ale-autismului.html
│   ├── styles.css
│   ├── tailwind-styles.css
│   ├── robots.txt
│   └── sitemap.xml
├── img/
│   └── (all image files)
├── logs/
│   └── .gitkeep
└── src/
    └── input.css
```

---

## 🔧 Alternative: cPanel File Manager Upload

1. **Login to cPanel:** `https://tempoapp.ro:2083`
2. **Open File Manager**
3. **Navigate to:** `public_html/` or create `tempo-web/` folder
4. **Click "Upload"**
5. **Drag and drop all files** (or use "Select File" button)
6. **Wait for upload to complete**
7. **Open Terminal in cPanel**
8. **Run:**
   ```bash
   cd ~/public_html/tempo-web
   chmod +x deploy.sh
   ./deploy.sh
   ```

---

## 🔧 Alternative: Upload as ZIP

1. **On your local machine, create ZIP:**
   - Windows: Right-click folder → "Send to" → "Compressed folder"
   - Mac: Right-click folder → "Compress"
   - Linux: `zip -r tempo-web.zip tempo-web/ -x "*/node_modules/*" "*/.git/*"`

2. **Upload ZIP via cPanel:**
   - Login to cPanel
   - File Manager
   - Navigate to destination folder
   - Click "Upload"
   - Select the ZIP file
   - After upload, right-click → "Extract"

3. **Connect via SSH and deploy:**
   ```bash
   cd ~/public_html/tempo-web
   chmod +x deploy.sh
   ./deploy.sh
   ```

---

## 🆘 Need Help?

If you encounter any issues, check:
- **Full Guide:** Open `DEPLOYMENT_GUIDE.md`
- **Troubleshooting Section:** Located in deployment guide
- **Logs:** After deployment, check `pm2 logs tempo-web`

---

## 🔐 Important Security Notes

✅ **Already secured in this package:**
- Passwords are hashed with bcrypt (10 salt rounds)
- JWT tokens for authentication
- HTTP-only cookies prevent XSS attacks
- Session secrets configured
- `.env` file has production credentials (not committed to git)

⚠️ **After deployment:**
- Keep `.env` file secure (already in `.gitignore`)
- Consider changing JWT_SECRET and SESSION_SECRET to random strings
- Enable HTTPS/SSL certificate (see deployment guide Step 6)
- Regularly backup your database

---

## ✅ Quick Verification After Upload

After running `./deploy.sh`, verify:

1. **Check site loads:**
   ```
   https://tempoapp.ro
   ```

2. **Test registration:**
   ```
   https://tempoapp.ro/register
   ```

3. **Test login:**
   ```
   https://tempoapp.ro/login
   ```

4. **Verify database:**
   ```bash
   mysql -u incjzljm_tempo_app_main -p
   USE incjzljm_tempo_app_main;
   SELECT * FROM tempo_clients;
   ```

If all 4 work → **SUCCESS!** ✅

---

## 🎯 Summary

1. Upload all files (except node_modules and .git)
2. SSH to server → `cd ~/tempo-web`
3. Run → `./deploy.sh`
4. Visit → `https://tempoapp.ro`

**Time Required:** ~10-15 minutes

**Questions?** Check `DEPLOYMENT_GUIDE.md` for detailed troubleshooting.
