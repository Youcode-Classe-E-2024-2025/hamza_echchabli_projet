<?php
namespace views;


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
        }
        .container {
            width: 400px;
            background-color: white;
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
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
        .toggle {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div id="message" class="message"></div>

        <form id="login-form" action="/handelLogin">
            <div class="form-group">
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

        <form id="register-form" style="display:none;">
            <div class="form-group">
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

    <script>
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

        // Form submission handler
        function handleFormSubmit(form, action) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                formData.append('action', action);

                fetch('/auth', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    const messageEl = document.getElementById('message');
                    if (response.ok) {
                        return response.json().then(data => {
                            messageEl.textContent = data.message;
                            messageEl.className = 'message success';
                            
                            // Redirect or update UI after successful login/register
                            if (action === 'login') {
                                // Redirect to dashboard or home page
                                window.location.href = '/dashboard';
                            } else {
                                // Switch to login form after successful registration
                                document.getElementById('register-form').style.display = 'none';
                                document.getElementById('login-form').style.display = 'block';
                            }
                        });
                    } else {
                        return response.json().then(data => {
                            messageEl.textContent = data.message;
                            messageEl.className = 'message error';
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const messageEl = document.getElementById('message');
                    messageEl.textContent = 'An error occurred. Please try again.';
                    messageEl.className = 'message error';
                });
            });
        }

        // Attach form submission handlers
        handleFormSubmit(document.getElementById('login-form'), 'login');
        handleFormSubmit(document.getElementById('register-form'), 'register');
    </script>
</body>
</html>