# Tempo Web - Login & Registration System

Platforma completă de management pentru centre de terapie cu sistem de autentificare funcțional.

## 🚀 Caracteristici

- **Sistem de autentificare complet**: Login și înregistrare cu validare
- **Securitate**: Parole hash-uite cu bcrypt, JWT tokens, sesiuni securizate
- **Design responsive**: Interfață modernă cu Tailwind CSS
- **Backend robust**: Node.js cu Express și MySQL
- **Bază de date**: MySQL cu tabela `tempo_clients`

## 📋 Cerințe

- Node.js (versiunea 14 sau mai nouă)
- MySQL Server
- npm sau yarn

## 🛠️ Instalare

### 1. Clonează repository-ul

```bash
git clone https://github.com/stefandinca/tempo-web.git
cd tempo-web
```

### 2. Instalează dependențele

```bash
npm install
```

### 3. Configurează baza de date

Creează baza de date MySQL:

```sql
CREATE DATABASE incjzljm_tempo_app_main;
```

Tabela `tempo_clients` va fi creată automat la pornirea serverului.

### 4. Configurează variabilele de mediu

Editează fișierul `.env` și adaugă configurația ta de bază de date:

```env
# Database Configuration
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=parola_ta
DB_NAME=incjzljm_tempo_app_main
DB_PORT=3306

# Server Configuration
PORT=3000
NODE_ENV=development

# JWT Secret (schimbă cu un string aleatoriu în producție)
JWT_SECRET=tempo_jwt_secret_key_2025

# Session Secret (schimbă cu un string aleatoriu în producție)
SESSION_SECRET=tempo_session_secret_2025
```

## 🚀 Rulare

### Mod dezvoltare

```bash
npm start
```

sau cu nodemon (reîncărcare automată):

```bash
npm run dev:server
```

Serverul va porni pe `http://localhost:3000`

### Compilare Tailwind CSS

Pentru a recompila CSS-ul Tailwind:

```bash
npm run dev
```

## 📁 Structura proiectului

```
tempo-web/
├── config/
│   └── database.js          # Configurare conexiune MySQL
├── routes/
│   └── auth.js             # Endpoints autentificare
├── dist/                   # Fișiere frontend (HTML, CSS)
│   ├── index.html         # Pagina principală
│   ├── login.html         # Pagina de autentificare
│   ├── register.html      # Pagina de înregistrare
│   ├── styles.css         # Stiluri custom
│   └── tailwind-styles.css # Stiluri Tailwind
├── server.js              # Server Express
├── package.json           # Dependențe npm
├── .env                   # Variabile de mediu (nu se comite)
└── .env.example          # Template variabile de mediu
```

## 🔐 API Endpoints

### Înregistrare
```
POST /api/auth/register
Body: {
  "email": "user@example.com",
  "password": "parola123",
  "first_name": "Ion",
  "last_name": "Popescu"
}
```

### Autentificare
```
POST /api/auth/login
Body: {
  "email": "user@example.com",
  "password": "parola123"
}
```

### Logout
```
POST /api/auth/logout
```

### Verificare utilizator curent
```
GET /api/auth/me
Header: Authorization: Bearer <token>
```

### Verificare token
```
GET /api/auth/verify
Header: Authorization: Bearer <token>
```

## 🗄️ Schema bazei de date

### Tabela `tempo_clients`

```sql
CREATE TABLE tempo_clients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  first_name VARCHAR(100),
  last_name VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## 🔒 Securitate

- Parolele sunt hash-uite folosind **bcrypt** cu 10 salt rounds
- Autentificare bazată pe **JWT tokens** (valabile 24h)
- Sesiuni securizate cu **express-session**
- Cookies HTTP-only pentru protecție împotriva XSS
- Validare email și parolă (minim 6 caractere)
- Verificare unicitate email în baza de date

## 🌐 Pagini disponibile

- `/` - Pagina principală (landing page)
- `/login` - Pagina de autentificare
- `/register` - Pagina de înregistrare

## 🛠️ Tehnologii utilizate

### Frontend
- HTML5
- Tailwind CSS v4.1.17
- Vanilla JavaScript

### Backend
- Node.js
- Express.js
- MySQL2 (cu promise support)
- bcrypt (hashing parole)
- jsonwebtoken (JWT authentication)
- express-session (gestiune sesiuni)
- cookie-parser
- cors
- dotenv

## 📝 Notițe

- Asigură-te că MySQL Server rulează înainte de a porni aplicația
- Pentru producție, schimbă `JWT_SECRET` și `SESSION_SECRET` cu valori sigure
- În producție, setează `NODE_ENV=production` și `cookie.secure=true`

## 🐛 Debugging

Dacă întâmpini probleme de conectare la baza de date:

1. Verifică dacă MySQL Server rulează
2. Verifică credențialele din `.env`
3. Verifică dacă baza de date `incjzljm_tempo_app_main` există
4. Verifică log-urile serverului pentru mesaje de eroare

## 📄 Licență

ISC

## 👥 Autor

Tempo Team
