<?php
require_once 'config.php';
require_once 'language_helper.php';

// Hardcoded chef login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if ($username == 'chef' && $password == 'chef123') {
        $_SESSION['user_id'] = 999;
        $_SESSION['username'] = 'chef';
        $_SESSION['role'] = 'chef';
        $_SESSION['full_name'] = 'Head Chef';
        header('Location: chef-dashboard.php');
        exit();
    } else {
        $error = '❌ ' . __('invalid_credentials');
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ('chef_login'); ?> - <?php echo ('app_name'); ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }
        .slideshow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }
        .slideshow .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
            animation: slideAnimation 30s infinite;
        }
        .slideshow .slide:nth-child(1) {
            background-image: url('assets/images/photo_2026-07-06_15-32-28 (2).jpg');
            animation-delay: 0s;
        }
        .slideshow .slide:nth-child(2) {
            background-image: url('assets/images/photo_2026-07-06_15-32-28 (3).jpg');
            animation-delay: 5s;
        }
        .slideshow .slide:nth-child(3) {
            background-image: url('assets/images/photo_2026-07-06_15-32-28.jpg');
            animation-delay: 10s;
        }
        .slideshow .slide:nth-child(4) {
            background-image: url('assets/images/photo_2026-07-06_15-32-29 (2).jpg');
            animation-delay: 15s;
        }
        .slideshow .slide:nth-child(5) {
            background-image: url('assets/images/photo_2026-07-06_15-32-29 (3).jpg');
            animation-delay: 20s;
        }
        .slideshow .slide:nth-child(6) {
            background-image: url('assets/images/photo_2026-07-06_15-32-29.jpg');
            animation-delay: 25s;
        }

        @keyframes slideAnimation {
            0% { opacity: 0; }
            8% { opacity: 1; }
            20% { opacity: 1; }
            28% { opacity: 0; }
            100% { opacity: 0; }
        }
        .login-box {
            position: relative;
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            padding: 40px;
            width: 420px;
            max-width: 100%;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
        }
        .login-box .logo { text-align: center; font-size: 60px; margin-bottom: 10px; }
        .login-box h1 {
            font-family: 'Playfair Display', serif;
            color: #d4af37;
            text-align: center;
            font-size: 28px;
        }
        .login-box .subtitle {
            text-align: center;
            color: rgba(255,255,255,0.6);
            font-size: 14px;
            margin-bottom: 25px;
        }
        .login-box input {
            width: 100%;
            padding: 14px 18px;
            margin-bottom: 15px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: white;
            font-size: 14px;
            transition: 0.3s;
        }
        .login-box input::placeholder { color: rgba(255,255,255,0.4); }
        .login-box input:focus {
            outline: none;
            border-color: #d4af37;
            background: rgba(255,255,255,0.12);
        }
        .login-box .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #d4af37, #b8960f);
            color: #1a1a2e;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        .login-box .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3);
        }
        .login-box .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: rgba(255,255,255,0.3);
            font-size: 13px;
            text-decoration: none;
        }
        .login-box .back-link:hover { color: rgba(255,255,255,0.6); }
        .error {
            color: #ff6b6b;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(255,0,0,0.1);
            border-radius: 8px;
            font-size: 14px;
        }
        .decorative-line {
            height: 2px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
            margin: 20px 0 25px;
        }
        .role-badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            background: rgba(212, 175, 55, 0.2);
            color: #d4af37;
        }
        .lang-switcher {
            position: absolute;
            top: 15px;
            right: 20px;
            z-index: 10;
        }
        .lang-switcher select {
            padding: 5px 10px;
            border-radius: 8px;
            border: 2px solid rgba(212, 175, 55, 0.5);
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 12px;
            cursor: pointer;
            font-weight: 600;
        }
        .lang-switcher select:hover {
            border-color: #d4af37;
        }
        .lang-switcher select option {
            background: #1a1a2e;
            color: white;
        }
    </style>
</head>
<body>
    <div class="slideshow">
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
        <div class="slide"></div>
    </div>

    <div class="login-box">
        <!-- Language Switcher -->
        <div class="lang-switcher">
            <select onchange="window.location.href='?lang=' + this.value">
                <?php foreach ($available_languages as $code => $name): ?>
                    <option value="<?php echo $code; ?>" <?php echo ($_SESSION['lang'] ?? 'en') == $code ? 'selected' : ''; ?>>
                        <?php echo $name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="logo">👨‍🍳</div>
        <h1><?php echo __('chef_login'); ?></h1>
        <p class="subtitle">
            <span class="role-badge"><?php echo __('Kitchen Staff'); ?></span>
        </p>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="<?php echo __('username'); ?>" required>
            <div style="position: relative;">
                <input type="password" name="password" id="password" placeholder="<?php echo __('password'); ?>" required style="width: 100%; padding: 14px 45px 14px 18px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); border-radius: 12px; color: white; font-size: 14px;">
                <i class="fas fa-eye" id="togglePassword" onclick="togglePasswordVisibility()" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: rgba(255,255,255,0.5); cursor: pointer; font-size: 18px; z-index: 10;"></i>
            </div>
            <button type="submit" class="btn-login"><?php echo __('login'); ?></button>
        </form>

        <a href="index.php" class="back-link">⬅️ <?php echo __('back_to_main'); ?></a>

        <div class="decorative-line"></div>
        <p style="text-align:center; font-size:11px; color:rgba(255,255,255,0.2);">
           
        </p>
    </div>

    <script>
        // Toggle Password Visibility
        function togglePasswordVisibility() {
            const password = document.getElementById('password');
            const toggle = document.getElementById('togglePassword');
            
            if (!password || !toggle) {
                console.log('Elements not found!');
                return;
            }
            
            if (password.type === 'password') {
                password.type = 'text';
                toggle.classList.remove('fa-eye');
                toggle.classList.add('fa-eye-slash');
                toggle.style.color = '#d4af37';
            } else {
                password.type = 'password';
                toggle.classList.remove('fa-eye-slash');
                toggle.classList.add('fa-eye');
                toggle.style.color = 'rgba(255,255,255,0.5)';
            }
        }
    </script>
</body>
</html>