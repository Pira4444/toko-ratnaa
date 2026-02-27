<?php
// ... Logika PHP tetap sama (error reporting, session, login logic) ...
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();
session_start();

include '../koneksi.php';

if (isset($_POST['login_admin'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM admin WHERE email = '$email'");
    
    if (mysqli_num_rows($query) === 1) {
        $row = mysqli_fetch_assoc($query);
        if (md5($pass) == $row['password']) {
            $_SESSION['login_admin'] = true;
            $_SESSION['admin_data']  = $row;
            session_write_close();
            header("Location: admin.php");
            exit;
        } else {
            $error = "Password yang Anda masukkan salah!";
        }
    } else {
        $error = "Email tidak terdaftar di sistem!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Toko Ana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 15px;
        }

        .card-login {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .card-header-custom {
            background: white;
            padding: 40px 30px 20px 30px;
            text-align: center;
            border: none;
        }

        .icon-circle {
            width: 70px;
            height: 70px;
            background: #f0f3ff;
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 20px auto;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            background-color: #f8f9fa;
            border: 2px solid transparent;
            transition: all 0.3s;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: var(--primary);
            box-shadow: none;
        }

        .btn-login {
            background: var(--primary);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .alert-custom {
            border-radius: 12px;
            font-size: 14px;
            border: none;
            background-color: #fff0f0;
            color: #d63031;
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="card card-login">
        <div class="card-header-custom">
            <div class="icon-circle">
                <i class="fas fa-user-shield"></i>
            </div>
            <h4 class="fw-bold text-dark m-0">Welcome Back</h4>
            <p class="text-muted small mt-2">Silakan login untuk mengelola toko Anda</p>
        </div>
        
        <div class="card-body p-4 pt-2">
            <?php if(isset($error)) : ?>
                <div class="alert alert-custom d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div><?= $error; ?></div>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Email Address</label>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="admin@tokoana.com" required>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" name="login_admin" class="btn btn-primary btn-login w-100 mb-3">
                    Sign In <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="footer-text">
        &copy; 2026 Toko Ana System &bull; Crafted with <i class="fas fa-heart text-danger"></i>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>