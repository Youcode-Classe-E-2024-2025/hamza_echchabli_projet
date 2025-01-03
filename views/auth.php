<?php
namespace views;
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/allStyling.css">
    <link rel="stylesheet" href="css/test.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .auth-wrapper {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .auth-container {
            width: 100%;
            max-width: 400px;
            background: var(--background-color-secondary);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            background: var(--background-color);
            color: var(--text-color);
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
        }
        .error {
            color: var(--danger-color);
        }
        .success {
            color: var(--success-color);
        }
        .toggle {
            text-align: center;
            margin-top: 15px;
            color: var(--text-color-muted);
        }
        .toggle a {
            color: var(--primary-color);
            text-decoration: none;
        }
        .hidden{
            display: none;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <span><a href="home" class="active">KanbanFlow</a></span>
        </div>
        <div class="nav-links">
            <a href="#">Authentication</a>
            <div class="nav-right">
               
            </div>
        </div>
    </nav>

    <div class="auth-wrapper">
        <div class="auth-container">
            <div id="message" class="message">
                <?php
                if (isset($_SESSION['auth_error'])) {
                    echo '<div class="error">' . htmlspecialchars($_SESSION['auth_error']) . '</div>';
                    unset($_SESSION['auth_error']);
                }
                if (isset($_SESSION['auth_success'])) {
                    echo '<div class="success">' . htmlspecialchars($_SESSION['auth_success']) . '</div>';
                    unset($_SESSION['auth_success']);
                }
                ?>
            </div>

            <form id="login-form" action="/handelauth" method="POST">
                <div class="form-group">
                <input class="hidden" name="login" value="login">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <button type="submit">Login</button>
                </div>
                <div class="toggle">
                    Don't have an account? <a href="#" id="show-register">Register</a>
                </div>
            </form>

            <form id="register-form" action="/handelauth"  style="display:none;" method="POST">
                <div class="form-group">
                   <input class="hidden" name="register" value="register">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <button type="submit">Register</button>
                </div>
                <div class="toggle">
                    Already have an account? <a href="#" id="show-login">Login</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Theme toggle
        
        

        // Form toggle functionality
        document.getElementById('show-register').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('register-form').style.display = 'block';
        });

        document.getElementById('show-login').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('register-form').style.display = 'none';
            document.getElementById('login-form').style.display = 'block';
        });

        
    </script> 
</body>
</html>