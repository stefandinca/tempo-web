<?php
require_once __DIR__ . '/config.php';

initSession();

// If already logged in, redirect to dashboard
if (isAuthenticated()) {
    header('Location: /admin-dashboard.php');
    exit;
}

// Handle login form submission
$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Handle JSON requests (from fetch API)
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (strpos($contentType, 'application/json') !== false) {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
    }

    if (verifyAdminCredentials($username, $password)) {
        $_SESSION['isAdmin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['loginTime'] = date('c');

        // If JSON request, return JSON response
        if (strpos($contentType, 'application/json') !== false) {
            sendJson(['success' => true, 'message' => 'Login successful']);
        }

        header('Location: /admin-dashboard.php');
        exit;
    } else {
        $errorMessage = 'invalid';

        // If JSON request, return error
        if (strpos($contentType, 'application/json') !== false) {
            sendJson(['success' => false, 'message' => 'Invalid credentials'], 401);
        }
    }
}

// Check for error parameter
if (isset($_GET['error']) && $_GET['error'] === 'invalid') {
    $errorMessage = 'invalid';
}
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login - Tempo</title>
    <link rel="stylesheet" href="/tailwind-styles.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 3rem;
            width: 100%;
            max-width: 400px;
        }
        .error-message {
            background-color: #fee;
            border: 1px solid #fcc;
            color: #c33;
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: none;
        }
        .error-message.show {
            display: block;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .input-field {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        .input-field:focus {
            outline: none;
            border-color: #667eea;
        }
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo h1 {
            font-size: 2rem;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .logo p {
            color: #718096;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>🔐 TEMPO</h1>
            <p>Super Admin Portal</p>
        </div>

        <div id="errorMessage" class="error-message <?php echo $errorMessage === 'invalid' ? 'show' : ''; ?>">
            Invalid username or password
        </div>

        <form id="loginForm" method="POST" action="/admin-login.php">
            <div>
                <label for="username" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748;">Username</label>
                <input type="text" id="username" name="username" class="input-field" required autocomplete="username">
            </div>

            <div>
                <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748;">Password</label>
                <input type="password" id="password" name="password" class="input-field" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn-primary">
                Login to Dashboard
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="/" style="color: #667eea; text-decoration: none; font-size: 0.9rem;">
                ← Back to Website
            </a>
        </div>
    </div>

    <script>
        // Handle form submission with fetch API
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const data = {
                username: formData.get('username'),
                password: formData.get('password')
            };

            try {
                const response = await fetch('/admin-login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    window.location.href = '/admin-dashboard.php';
                } else {
                    document.getElementById('errorMessage').classList.add('show');
                }
            } catch (error) {
                console.error('Login error:', error);
                document.getElementById('errorMessage').classList.add('show');
            }
        });
    </script>
</body>
</html>
