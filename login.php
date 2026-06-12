<?php
// login.php - Login Page
session_start();
require_once 'config/database.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_type'] == 'super_admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: vendor/dashboard.php");
    }
    exit();
}

$error = "";

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Encrypt password (same as install.php using MD5)
    $encrypted_password = md5($password);

    // Check user credentials
    $query = "SELECT * FROM vendors 
              WHERE email = '$email' 
              AND password = '$encrypted_password' 
              AND status = 1";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Store data in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['vendor_id'] = $user['id'];

        // Redirect based on user type
        if ($user['user_type'] == 'super_admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: vendor/dashboard.php");
        }
        exit();
    } else {
        $error = "❌ Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Payment Follow-up System</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #00695c 0%, #004d40 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: #00695c;
            color: #fff;
            text-align: center;
            padding: 30px 20px;
        }

        .login-header .logo {
            font-size: 50px;
            margin-bottom: 10px;
        }

        .login-header h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .login-header p {
            font-size: 13px;
            opacity: 0.9;
        }

        .login-body {
            padding: 35px 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group label i {
            color: #00695c;
            margin-right: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #00695c;
            box-shadow: 0 0 0 3px rgba(0,105,92,0.1);
        }

        .btn-login {
            width: 100%;
            background: #00695c;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #004d40;
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: center;
            border-left: 4px solid #c62828;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: #00695c;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .demo-info {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        .demo-info h4 {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
            text-align: center;
        }

        .demo-box {
            background: #f5f5f5;
            border-radius: 8px;
            padding: 12px;
            font-size: 12px;
        }

        .demo-box p {
            margin: 5px 0;
            color: #555;
        }

        .demo-box strong {
            color: #00695c;
        }

        @media (max-width: 480px) {
            .login-body {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">

        <div class="login-header">
            <div class="logo">💰📱</div>
            <h2>Login</h2>
            <p>Welcome to Payment Follow-up System</p>
        </div>

        <div class="login-body">

            <?php if ($error != "") : ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label><i>📧</i> Email Address</label>
                    <input
                        type="email"
                        name="email"
                        placeholder="example@email.com"
                        required
                        autofocus>
                </div>

                <div class="form-group">
                    <label><i>🔒</i> Password</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Enter Password"
                        required>
                </div>

                <button type="submit" class="btn-login">
                    🔐 Login
                </button>
            </form>

            <div class="back-link">
                <a href="index.php">← Back to Homepage</a>
            </div>

            <div class="demo-info">
                <h4>📋 Demo Login Information</h4>

                <div class="demo-box">
                    <p><strong>👑 Super Admin:</strong></p>
                    <p>📧 admin@example.com</p>
                    <p>🔑 admin123</p>

                    <hr style="margin: 8px 0;">

                    <p><strong>🏪 Vendor (Test):</strong></p>
                    <p>Login as Super Admin first and create a vendor account.</p>
                </div>
            </div>

        </div>

    </div>
</div>

</body>
</html>