 <?php
require_once 'config.php';
require_once 'language_helper.php';

if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'receptionist') {
    header('Location: reception-dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password' AND role='receptionist'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = 'receptionist';
        $_SESSION['full_name'] = $user['full_name'];
        header('Location: reception-dashboard.php');
        exit();
    } else {
        $error = '❌ Invalid username or password!';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
    <?php echo ('Reception Login'); ?> - <?php echo ('app_name'); ?></title>
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
         /* ============================================ */
/* SLIDESHOW BACKGROUND - 6 IMAGES             */
/* ============================================ */
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
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 24px;
            padding: 40px;
            width: 420px;
            max-width: 100%;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5);
            position: relative;
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
        
        /* Language Switcher INSIDE the login box */
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
        <!-- Language Switcher INSIDE the box -->
        <div class="lang-switcher">
            <select onchange="window.location.href='?lang=' + this.value">
                <?php foreach ($available_languages as $code => $name): ?>
                    <option value="<?php echo $code; ?>" <?php echo ($_SESSION['lang'] ?? 'en') == $code ? 'selected' : ''; ?>>
                        <?php echo $name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="logo">🏨</div>
        <h1><?php echo __('Reception Login'); ?></h1>
        <p class="subtitle">
            <span class="role-badge">Receptionist</span>
        </p>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="<?php echo __('username'); ?>" required>
            <input type="password" name="password" placeholder="<?php echo __('password'); ?>" required>
            <button type="submit" class="btn-login"><?php echo __('login'); ?></button>
        </form>

        <a href="index.php" class="back-link">⬅ <?php echo __('Back to Main Login'); ?></a>

        <div class="decorative-line"></div>
        <p style="text-align:center; font-size:11px; color:rgba(255,255,255,0.2);">
         </p>
    </div>
</body>
</html>