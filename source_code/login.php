<?php
include 'includes/db.php';

// Already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = MD5(trim($_POST['password']));

    $res = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    $user = mysqli_fetch_assoc($res);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['username']= $user['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Customer Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d47a1 0%, #1a73e8 50%, #42a5f5 100%);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .login-wrapper { width: 100%; max-width: 420px; padding: 20px; }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            color: white;
            text-align: center;
            padding: 35px 20px;
        }
        .login-header .icon {
            width: 80px; height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 15px;
            font-size: 35px;
        }
        .login-header h4 { margin: 0; font-size: 22px; font-weight: 700; }
        .login-header p { margin: 5px 0 0; opacity: 0.85; font-size: 14px; }
        .login-body { padding: 35px 30px; }
        .form-label { font-weight: 600; font-size: 14px; color: #444; }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px 15px;
            font-size: 15px;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 3px rgba(26,115,232,0.15);
        }
        .input-group-text {
            border-radius: 10px 0 0 10px;
            border: 2px solid #e0e0e0;
            border-right: none;
            background: #f8f9fa;
            color: #1a73e8;
        }
        .input-group .form-control { border-radius: 0 10px 10px 0; border-left: none; }
        .btn-login {
            background: linear-gradient(135deg, #1a73e8, #0d47a1);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(26,115,232,0.4); }
        .credentials-box {
            background: #f0f7ff;
            border: 1px solid #c8e0ff;
            border-radius: 10px;
            padding: 12px 15px;
            margin-top: 20px;
            font-size: 13px;
        }
        .credentials-box p { margin: 3px 0; color: #444; }
        .credentials-box strong { color: #1a73e8; }
        .alert { border-radius: 10px; font-size: 14px; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <div class="icon"><i class="fas fa-users-cog"></i></div>
            <h4>Customer Management System</h4>
            <p>Please login to continue</p>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control"
                               placeholder="Enter username" required autofocus
                               value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control"
                               placeholder="Enter password" required id="pwd">
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="togglePwd()" style="border-radius:0 10px 10px 0;">
                            <i class="fas fa-eye" id="eye_icon"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>

            <div class="credentials-box">
                <p><i class="fas fa-info-circle me-1 text-primary"></i><strong>Demo Credentials:</strong></p>
                <p><strong>Admin:</strong> username: <strong>admin</strong> | password: <strong>admin123</strong></p>
                <p><strong>User:</strong> username: <strong>nikhil</strong> | password: <strong>nikhil123</strong></p>
            </div>
        </div>
    </div>
</div>

<script>
function togglePwd() {
    var pwd = document.getElementById('pwd');
    var icon = document.getElementById('eye_icon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        pwd.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
</body>
</html>
